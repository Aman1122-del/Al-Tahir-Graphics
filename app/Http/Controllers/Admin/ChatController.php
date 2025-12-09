<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnifiedChat;
use App\Models\UnifiedChatMessage;
use App\Models\ChatParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Show admin chat dashboard with all active conversations.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->canAccessChats()) {
            abort(403, 'Unauthorized access.');
        }

        // Get all users who participate in chats with admin
        $chatUsers = User::whereHas('chatParticipants', function ($query) {
            $query->whereHas('chat', function ($q) {
                $q->whereHas('participants', function ($participantQuery) {
                    $participantQuery->where('user_id', Auth::id());
                });
            });
        })
        ->where('id', '!=', $user->id)
        ->with(['chatParticipants' => function ($query) {
            $query->whereHas('chat', function ($chatQuery) {
                $chatQuery->whereHas('participants', function ($participantQuery) {
                    $participantQuery->where('user_id', Auth::id());
                })
                ->with(['latestMessage', 'participants']);
            });
        }])
        ->get()
        ->map(function ($user) {
            $participant = $user->chatParticipants->first();
            if ($participant && $participant->chat) {
                $chat = $participant->chat;
                $user->last_message = $chat->latestMessage;
                $user->unread_count = $chat->getUnreadCountForUser(Auth::id());
                $user->sent_messages_count = $chat->messages()->where('sender_id', $user->id)->count();
                $user->received_messages_count = $chat->messages()->where('sender_id', '!=', $user->id)->count();
                $user->last_activity = $chat->last_message_at;
            } else {
                $user->last_message = null;
                $user->unread_count = 0;
                $user->sent_messages_count = 0;
                $user->received_messages_count = 0;
                $user->last_activity = null;
            }
            return $user;
        });

        // Get recent conversations
        $recentChats = UnifiedChat::whereHas('participants', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->with(['latestMessage', 'participants.user:id,name'])
        ->orderBy('last_message_at', 'desc')
        ->limit(10)
        ->get();

        $recentConversations = [];
        foreach ($recentChats as $chat) {
            // Get the other participant
            $otherParticipant = $chat->participants->where('user_id', '!=', Auth::id())->first();
            if ($otherParticipant) {
                $userId = $otherParticipant->user_id;
                if (!isset($recentConversations[$userId])) {
                    $recentConversations[$userId] = [];
                }
                $recentConversations[$userId][] = $chat->latestMessage;
            }
        }

        // Transform data for the new interface
        $conversations = $chatUsers->map(function ($user) {
            return [
                'id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'last_message' => $user->last_message ? $user->last_message->message : 'No messages yet',
                'last_message_at' => $user->last_message ? $user->last_message->created_at->toISOString() : null,
                'unread_count' => $user->unread_count,
                'is_online' => false, // You can implement this based on your requirements
                'sent_messages_count' => $user->sent_messages_count,
                'received_messages_count' => $user->received_messages_count,
            ];
        })->values()->toArray();

        $stats = [
            'total_messages' => UnifiedChatMessage::count(),
            'total_users_with_chats' => User::whereHas('chatParticipants')->count(),
            'unread_messages' => UnifiedChatMessage::where('is_read', false)->count(),
            'messages_today' => UnifiedChatMessage::whereDate('created_at', today())->count(),
        ];

        // If AJAX request, return JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'conversations' => $conversations,
                'stats' => $stats,
            ]);
        }

        return view('admin.chat.index', compact('conversations', 'stats'));
    }

    /**
     * Show chat conversation with a specific user.
     */
    public function show($userId)
    {
        $user = Auth::user();
        $otherUser = User::findOrFail($userId);

        if (!$user->canAccessChats()) {
            abort(403, 'Unauthorized access.');
        }

        // Find or create chat between admin and user
        $chat = UnifiedChat::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->first();

        if (!$chat) {
            // Create new chat if it doesn't exist
            $chat = UnifiedChat::create([
                'type' => 'support',
                'title' => "Support Chat: {$otherUser->name}",
                'status' => 'active',
                'priority' => 'normal',
                'created_by' => $user->id,
            ]);

            // Add both users as participants
            $chat->addParticipant($user->id, null, null, 'admin');
            $chat->addParticipant($userId, null, null, 'participant');
        }

        $messages = $chat->messages()
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read for admin
        $chat->markAsReadForUser($user->id);

        // If AJAX request, return JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'messages' => $messages,
                'chat' => $chat,
            ]);
        }

        return view('admin.chat.show', compact('otherUser', 'messages', 'chat'));
    }

    /**
     * Get chat statistics for admin dashboard.
     */
    public function statistics()
    {
        $user = Auth::user();

        if (!$user->canAccessChats()) {
            abort(403, 'Unauthorized access.');
        }

        $stats = [
            'total_messages' => UnifiedChatMessage::count(),
            'total_users_with_chats' => User::whereHas('chatParticipants')->count(),
            'unread_messages' => UnifiedChatMessage::where('is_read', false)->count(),
            'messages_today' => UnifiedChatMessage::whereDate('created_at', today())->count(),
            'messages_this_week' => UnifiedChatMessage::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'messages_this_month' => UnifiedChatMessage::whereMonth('created_at', now()->month)->count(),
        ];

        // Top chat users
        $topChatUsers = User::withCount(['chatParticipants'])
            ->whereHas('chatParticipants')
            ->orderBy('chat_participants_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'top_users' => $topChatUsers,
        ]);
    }

    /**
     * Search chat users.
     */
    public function searchUsers(Request $request)
    {
        $user = Auth::user();

        if (!$user->canAccessChats()) {
            abort(403, 'Unauthorized access.');
        }

        $query = $request->get('query');

        $users = User::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })
        ->whereHas('chatParticipants')
        ->withCount(['chatParticipants'])
        ->limit(20)
        ->get();

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    /**
     * Export chat data (for admin purposes).
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        if (!$user->canAccessChats()) {
            abort(403, 'Unauthorized access.');
        }

        $format = $request->get('format', 'csv');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = UnifiedChatMessage::with(['sender:id,name,email', 'chat:id,title']);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $messages = $query->orderBy('created_at', 'desc')->get();

        if ($format === 'csv') {
            return $this->exportToCsv($messages);
        }

        return $this->exportToExcel($messages);
    }

    /**
     * Export messages to CSV.
     */
    private function exportToCsv($messages)
    {
        $filename = 'chat_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($messages) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, ['Date', 'Time', 'Sender', 'Chat Title', 'Message', 'File', 'Read At']);

            foreach ($messages as $message) {
                fputcsv($file, [
                    $message->created_at->format('Y-m-d'),
                    $message->created_at->format('H:i:s'),
                    $message->sender->name . ' (' . $message->sender->email . ')',
                    $message->chat->title ?? 'N/A',
                    $message->message,
                    $message->file_name ?: 'No file',
                    $message->is_read ? 'Read' : 'Unread',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export messages to Excel.
     */
    private function exportToExcel($messages)
    {
        // This would require the Excel package to be properly configured
        // For now, return CSV as fallback
        return $this->exportToCsv($messages);
    }

    /**
     * Reply to a user's message.
     */
    public function reply(Request $request, $userId)
    {
        $user = Auth::user();
        $otherUser = User::findOrFail($userId);

        if (!$user->canAccessChats()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Find or create chat between admin and user
        $chat = UnifiedChat::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->first();

        if (!$chat) {
            // Create new chat if it doesn't exist
            $chat = UnifiedChat::create([
                'type' => 'support',
                'title' => "Support Chat: {$otherUser->name}",
                'status' => 'active',
                'priority' => 'normal',
                'created_by' => $user->id,
            ]);

            // Add both users as participants
            $chat->addParticipant($user->id, null, null, 'admin');
            $chat->addParticipant($userId, null, null, 'participant');
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
        $message->load('sender:id,name');
        $chat->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Get messages for a specific user conversation.
     */
    public function messages(Request $request, $userId)
    {
        $user = Auth::user();
        $otherUser = User::findOrFail($userId);

        if (!$user->canAccessChats()) {
            abort(403, 'Unauthorized access.');
        }

        // Find chat between admin and user
        $chat = UnifiedChat::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->first();

        if (!$chat) {
            return response()->json([
                'success' => true,
                'messages' => [],
            ]);
        }

        $query = $chat->messages();

        // If since parameter is provided, only get messages after that ID
        if ($request->has('since')) {
            $query->where('id', '>', $request->since);
        }

        $messages = $query->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read for admin (only if fetching all messages)
        if ($request->query('since', 0) == 0) {
            $chat->markAsReadForUser($user->id);
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }
}
