<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
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

        // Get all users who have sent or received messages
        $chatUsers = User::whereHas('sentMessages')
            ->orWhereHas('receivedMessages')
            ->where('id', '!=', $user->id)
            ->withCount(['sentMessages', 'receivedMessages'])
            ->withCount(['unreadMessages as unread_count' => function ($query) use ($user) {
                $query->where('receiver_id', $user->id);
            }])
            ->orderBy('unread_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get recent conversations
        $recentConversations = Message::with(['sender:id,name', 'receiver:id,name'])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->groupBy(function ($message) use ($user) {
                if ($message->sender_id == $user->id) {
                    return $message->receiver_id;
                }
                return $message->sender_id;
            });

        return view('admin.chat.index', compact('chatUsers', 'recentConversations'));
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

        $messages = Message::betweenUsers($user->id, $userId)
            ->with(['sender:id,name', 'receiver:id,name'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('admin.chat.show', compact('otherUser', 'messages'));
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
            'total_messages' => Message::count(),
            'total_users_with_chats' => User::whereHas('sentMessages')
                ->orWhereHas('receivedMessages')
                ->count(),
            'unread_messages' => Message::whereNull('read_at')->count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
            'messages_this_week' => Message::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'messages_this_month' => Message::whereMonth('created_at', now()->month)->count(),
        ];

        // Top chat users
        $topChatUsers = User::withCount(['sentMessages', 'receivedMessages'])
            ->whereHas('sentMessages')
            ->orWhereHas('receivedMessages')
            ->orderBy('sent_messages_count', 'desc')
            ->orderBy('received_messages_count', 'desc')
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
        ->whereHas('sentMessages')
        ->orWhereHas('receivedMessages')
        ->withCount(['sentMessages', 'receivedMessages'])
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

        $query = Message::with(['sender:id,name,email', 'receiver:id,name,email']);

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
            fputcsv($file, ['Date', 'Time', 'Sender', 'Receiver', 'Message', 'File', 'Read At']);
            
            foreach ($messages as $message) {
                fputcsv($file, [
                    $message->created_at->format('Y-m-d'),
                    $message->created_at->format('H:i:s'),
                    $message->sender->name . ' (' . $message->sender->email . ')',
                    $message->receiver->name . ' (' . $message->receiver->email . ')',
                    $message->message,
                    $message->file_name ?: 'No file',
                    $message->read_at ? $message->read_at->format('Y-m-d H:i:s') : 'Unread',
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
}
