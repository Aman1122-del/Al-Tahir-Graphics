<?php

namespace App\Http\Controllers;

use App\Models\UnifiedChat;
use App\Models\UnifiedChatMessage;
use App\Models\ChatParticipant;
use App\Models\User;
use App\Jobs\SendChatNotificationEmail;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
class UnifiedChatController extends Controller
{
    public function __construct()
    {
        // No external dependencies - using database polling instead
    }

    /**
     * Start a new chat session (for guests and authenticated users)
     */
    public function startChat(Request $request)
    {
        $user = Auth::user();
        
        // Different validation rules for guests vs authenticated users
        if ($user) {
            $validator = Validator::make($request->all(), [
                'message' => 'required|string|max:1000',
                'topic' => 'nullable|string|max:255',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string|max:1000',
                'topic' => 'nullable|string|max:255',
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Rate limiting
        $key = $user ? 'user_chat_' . $user->id : 'guest_chat_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json(['error' => 'Too many chat requests. Please try again later.'], 429);
        }
        RateLimiter::hit($key, 300); // 5 minutes

        // Create new chat
        $chatTitle = $user 
            ? 'Support Request from ' . $user->name
            : 'Support Request from ' . $request->name;
            
        $chat = UnifiedChat::create([
            'type' => 'support',
            'title' => $chatTitle,
            'status' => 'active',
            'priority' => 'normal',
            'created_by' => $user ? $user->id : null,
            'metadata' => [
                'topic' => $request->topic,
                'guest_ip' => $request->ip(),
            ],
        ]);

        // Add user as participant
        if ($user) {
            $chat->addParticipant($user->id, null, null, 'participant');
        } else {
            $chat->addParticipant(
                null, // no user_id for guests
                $request->email,
                $request->name,
                'participant'
            );
        }

        // Add admin as participant (find first admin)
        $admin = User::role('admin')->first();
        if ($admin) {
            $chat->addParticipant($admin->id, null, null, 'admin');
            $chat->update(['assigned_to' => $admin->id]);
        }

        // Create initial message
        $message = UnifiedChatMessage::create([
            'unified_chat_id' => $chat->id,
            'sender_id' => $user ? $user->id : null,
            'sender_email' => $user ? null : $request->email,
            'sender_name' => $user ? null : $request->name,
            'message' => $request->message,
            'message_type' => 'text',
        ]);

        // Update chat's last message time
        $chat->update(['last_message_at' => now()]);

        // Send email notification to admin
        SendChatNotificationEmail::dispatch($message);

        return response()->json([
            'success' => true,
            'chat_id' => $chat->id,
            'message' => 'Chat started successfully. We will respond soon.',
        ]);
    }

    /**
     * Send a message to an existing chat
     */
    public function sendMessage(Request $request, $chatId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $chat = UnifiedChat::findOrFail($chatId);
        $user = Auth::user();

        // Check if user can send message to this chat
        if (!$this->canSendMessageToChat($chat, $user, $request)) {
            return response()->json(['error' => 'Unauthorized to send message to this chat'], 403);
        }

        // Rate limiting
        $key = 'chat_message_' . ($user ? $user->id : $request->ip());
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['error' => 'Too many messages. Please slow down.'], 429);
        }
        RateLimiter::hit($key, 60); // 1 minute

        $messageData = [
            'unified_chat_id' => $chat->id,
            'sender_id' => $user ? $user->id : null,
            'sender_email' => $user ? null : $request->input('sender_email'),
            'sender_name' => $user ? null : $request->input('sender_name'),
            'message' => $request->message,
            'message_type' => 'text',
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('chat_uploads', $fileName, 'public');
            
            $messageData['file_path'] = $filePath;
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_type'] = $file->getMimeType();
            $messageData['file_size'] = $file->getSize();
            $messageData['message_type'] = 'file';
        }

        $message = UnifiedChatMessage::create($messageData);
        $message->load('sender');

        // Update chat's last message time
        $chat->update(['last_message_at' => now()]);

        // Send email notification if message is from user/guest (not admin)
        if (!$user || !$user->hasRole(['admin', 'support'])) {
            SendChatNotificationEmail::dispatch($message);
        }

        // Broadcast the new message event
        try {
            broadcast(new NewChatMessage($message))->toOthers();
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Log::error('Failed to broadcast chat message: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Get messages for a chat (supports polling with 'since' parameter)
     */
    public function getMessages(Request $request, $chatId)
    {
        $chat = UnifiedChat::findOrFail($chatId);
        $user = Auth::user();

        // Check if user can access this chat
        if (!$this->canAccessChat($chat, $user, $request)) {
            return response()->json(['error' => 'Unauthorized to access this chat'], 403);
        }

        $query = $chat->messages()->with('sender');

        // If 'since' parameter is provided, only get messages after that time
        if ($request->has('since')) {
            try {
                $since = \Carbon\Carbon::parse($request->input('since'));
                $query->where('created_at', '>', $since);
            } catch (\Exception $e) {
                // If parsing fails, ignore the since parameter
            }
        }

        $messages = $query->orderBy('created_at', 'asc')->get();

        // Mark messages as read for the current user (only if not polling)
        if ($user && !$request->has('since')) {
            $chat->markAsReadForUser($user->id);
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'chat' => $chat,
        ]);
    }

    /**
     * Get user's chats
     */
    public function getUserChats(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $chats = UnifiedChat::forUser($user->id)
            ->with(['latestMessage', 'participants'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'chats' => $chats,
        ]);
    }

    /**
     * Get admin's assigned chats
     */
    public function getAdminChats(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['admin', 'support'])) {
            return response()->json(['error' => 'Admin access required'], 403);
        }

        $chats = UnifiedChat::assignedTo($user->id)
            ->with(['latestMessage', 'participants'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'chats' => $chats,
        ]);
    }

    /**
     * Get all chats for admin dashboard
     */
    public function getAllChats(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['admin', 'support'])) {
            return response()->json(['error' => 'Admin access required'], 403);
        }

        $query = UnifiedChat::with(['latestMessage', 'participants', 'assignedUser']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $chats = $query->orderBy('last_message_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'chats' => $chats,
        ]);
    }

    /**
     * Assign chat to admin
     */
    public function assignChat(Request $request, $chatId)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['admin', 'support'])) {
            return response()->json(['error' => 'Admin access required'], 403);
        }

        $chat = UnifiedChat::findOrFail($chatId);
        $chat->update(['assigned_to' => $user->id]);

        // Add admin as participant if not already
        if (!$chat->hasParticipant($user->id)) {
            $chat->addParticipant($user->id, null, null, 'admin');
        }

        return response()->json([
            'success' => true,
            'message' => 'Chat assigned successfully',
        ]);
    }

    /**
     * Close chat
     */
    public function closeChat(Request $request, $chatId)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['admin', 'support'])) {
            return response()->json(['error' => 'Admin access required'], 403);
        }

        $chat = UnifiedChat::findOrFail($chatId);
        $chat->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Chat closed successfully',
        ]);
    }

    /**
     * Archive chat
     */
    public function archiveChat(Request $request, $chatId)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['admin', 'support'])) {
            return response()->json(['error' => 'Admin access required'], 403);
        }

        $chat = UnifiedChat::findOrFail($chatId);
        $chat->update(['status' => 'archived']);

        return response()->json([
            'success' => true,
            'message' => 'Chat archived successfully',
        ]);
    }

    /**
     * Get chat statistics
     */
    public function getStatistics(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole(['admin', 'support'])) {
            return response()->json(['error' => 'Admin access required'], 403);
        }

        $stats = [
            'total_chats' => UnifiedChat::count(),
            'active_chats' => UnifiedChat::where('status', 'active')->count(),
            'closed_chats' => UnifiedChat::where('status', 'closed')->count(),
            'archived_chats' => UnifiedChat::where('status', 'archived')->count(),
            'unread_messages' => UnifiedChatMessage::where('is_read', false)->count(),
            'today_messages' => UnifiedChatMessage::whereDate('created_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Check if user can send message to chat
     */
    private function canSendMessageToChat(UnifiedChat $chat, $user, Request $request): bool
    {
        if (!$user) {
            // Guest users can only send to chats they're participants in
            $senderEmail = $request->input('sender_email');
            return $senderEmail && $chat->hasParticipantByEmail($senderEmail);
        }

        // Registered users can send if they're participants or admins
        return $chat->hasParticipant($user->id) || $user->hasRole(['admin', 'support']);
    }

    /**
     * Check if user can access chat
     */
    private function canAccessChat(UnifiedChat $chat, $user, Request $request): bool
    {
        if (!$user) {
            // Guests can access chats they're participants in
            $senderEmail = $request->input('sender_email');
            return $senderEmail && $chat->hasParticipantByEmail($senderEmail);
        }

        // Admins can access all chats
        if ($user->hasRole(['admin', 'support'])) {
            return true;
        }

        // Regular users can only access chats they're participants in
        return $chat->hasParticipant($user->id);
    }

    /**
     * Get events for a specific channel (polling-based broadcasting)
     */
    public function getEvents(Request $request, $channel)
    {
        try {
            $since = $request->input('since', time() - 60); // Default to last 60 seconds
            $events = [];
            
            // Get all cache keys for this channel
            $pattern = "broadcast:{$channel}:*";
            $keys = Cache::getRedis()->keys($pattern);
            
            foreach ($keys as $key) {
                $event = Cache::get($key);
                if ($event && $event['timestamp'] > $since) {
                    $events[] = $event;
                }
            }
            
            // Sort by timestamp
            usort($events, function($a, $b) {
                return $a['timestamp'] - $b['timestamp'];
            });
            
            return response()->json([
                'success' => true,
                'events' => $events,
                'timestamp' => time()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to get events', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get events'
            ], 500);
        }
    }

    // Broadcasting methods removed - using database polling instead
}