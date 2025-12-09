<?php

namespace App\Http\Controllers;

use App\Models\UnifiedChat;
use App\Models\UnifiedChatMessage;
use App\Models\ChatParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function __construct()
    {
        // No external dependencies - using database polling instead
    }

    /**
     * Show the chat interface.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get user's active chats (most recent first)
        $chats = UnifiedChat::forUser($user->id)
            ->with(['latestMessage', 'participants'])
            ->where('status', 'active') 
            ->orderBy('last_message_at', 'desc')
            ->get();
        
        // Return JSON if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'chats' => $chats,
            ]);
        }
        
        // For the new floating widget chat page, we don't need the chatUsers
        return view('chat.index');
    }

    /**
     * Send a message.
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|exists:unified_chats,id',
            'message' => 'required|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $chat = UnifiedChat::findOrFail($request->chat_id);

        // Check if user can send message to this chat
        if (!$chat->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized to send message to this chat'], 403);
        }

        $messageData = [
            'unified_chat_id' => $chat->id,
            'sender_id' => $user->id,
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

        // Add file URL if file exists
        if ($message->file_path) {
            $message->file_url = asset('storage/' . $message->file_path);
        }

        // Update chat's last message time
        $chat->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Fetch messages for a chat.
     */
    public function fetchMessages(Request $request, $chatId)
    {
        $user = Auth::user();
        $chat = UnifiedChat::findOrFail($chatId);
        $since = $request->query('since', 0);

        // Check if user can access this chat
        if (!$chat->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized to access this chat'], 403);
        }

        $messagesQuery = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc');

        // If polling for new messages, only get messages since the last ID
        if ($since > 0) {
            $messagesQuery->where('id', '>', $since);
        }

        $messages = $messagesQuery->get();

        // Add file URLs to messages
        $messages->each(function ($message) {
            if ($message->file_path) {
                $message->file_url = asset('storage/' . $message->file_path);
            }
        });

        // Mark messages as read for this user (only if fetching all messages)
        if ($since === 0) {
            $chat->markAsReadForUser($user->id);
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|exists:unified_chats,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $chat = UnifiedChat::findOrFail($request->chat_id);

        // Check if user can access this chat
        if (!$chat->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized to access this chat'], 403);
        }

        // Mark messages as read for this user
        $chat->markAsReadForUser($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read',
        ]);
    }

    /**
     * Get unread message count for current user.
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        // Get unread count from all user's chats
        $count = UnifiedChat::forUser($user->id)
            ->withCount(['messages as unread_count' => function ($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                      ->where('is_read', false);
            }])
            ->get()
            ->sum('unread_count');

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Get chat users for current user.
     */
    public function getChatUsers()
    {
        $user = Auth::user();
        
        // Get all users that have admin/support roles
        $adminUsers = User::where('is_admin', true)->get();

        return response()->json([
            'success' => true,
            'users' => $adminUsers,
        ]);
    }

    /**
     * Start a new chat with support.
     */
    public function startChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'topic' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        
        // Check if user already has an active chat (get the most recent one)
        $existingChat = UnifiedChat::forUser($user->id)
            ->where('status', 'active')
            ->orderBy('last_message_at', 'desc')
            ->first();

        if ($existingChat) {
            return response()->json([
                'success' => true,
                'chat_id' => $existingChat->id,
                'message' => 'Using existing chat',
            ]);
        }

        // Create new chat
        $chat = UnifiedChat::create([
            'type' => 'support',
            'title' => 'Support Request from ' . $user->name,
            'status' => 'active',
            'priority' => 'normal',
            'created_by' => $user->id,
            'metadata' => [
                'topic' => $request->topic,
            ],
        ]);

        // Add user as participant
        $chat->addParticipant($user->id, null, null, 'participant');

        // Add admin as participant (find first admin)
        $admin = User::where('is_admin', true)->first();
        if ($admin) {
            $chat->addParticipant($admin->id, null, null, 'admin');
            $chat->update(['assigned_to' => $admin->id]);
        }

        // Create initial message
        $message = UnifiedChatMessage::create([
            'unified_chat_id' => $chat->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'message_type' => 'text',
        ]);

        // Update chat's last message time
        $chat->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'chat_id' => $chat->id,
            'message' => 'Chat started successfully',
        ]);
    }
}
