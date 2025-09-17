<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnifiedChat;
use App\Models\UnifiedChatMessage;
use App\Models\User;
use App\Jobs\SendChatNotificationEmail;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class UnifiedChatController extends Controller
{
    public function __construct()
    {
        // No external dependencies - using database polling instead
    }

    /**
     * Display a listing of chats.
     */
    public function index(Request $request)
    {
        $query = UnifiedChat::with(['latestMessage', 'participants', 'assignedUser']);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        if ($request->has('assigned_to') && $request->assigned_to !== '') {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        // Date range filter
        if ($request->has('date_range') && $request->date_range !== '') {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('participants', function ($subQ) use ($search) {
                      $subQ->where('participant_name', 'like', "%{$search}%")
                           ->orWhere('participant_email', 'like', "%{$search}%");
                  });
            });
        }

        $chats = $query->orderBy('last_message_at', 'desc')->paginate(20);
        $admins = User::role(['admin', 'support'])->get();

        return view('admin.chat.unified.index', compact('chats', 'admins'));
    }

    /**
     * Display the specified chat.
     */
    public function show(UnifiedChat $chat)
    {
        $chat->load(['messages.sender', 'participants.user', 'assignedUser']);

        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        // Mark messages as read for the current admin
        $chat->markAsReadForUser(Auth::id());

        return view('admin.chat.unified.show', compact('chat', 'messages'));
    }

    /**
     * Get messages for a chat (AJAX)
     */
    public function messages(Request $request, UnifiedChat $chat)
    {
        $query = $chat->messages()->with('sender');
        
        // If 'since' parameter is provided, only get messages after that ID
        if ($request->has('since') && $request->since > 0) {
            $query->where('id', '>', $request->since);
        }
        
        $messages = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a reply to a chat
     */
    public function reply(Request $request, UnifiedChat $chat)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

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

        // Update chat's last message time
        $chat->update(['last_message_at' => now()]);

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
     * Assign chat to admin
     */
    public function assign(Request $request, UnifiedChat $chat)
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $admin = User::findOrFail($request->admin_id);

        if (!$admin->hasRole(['admin', 'support'])) {
            return response()->json(['error' => 'User must be an admin or support'], 400);
        }

        $chat->update(['assigned_to' => $admin->id]);

        // Add admin as participant if not already
        if (!$chat->hasParticipant($admin->id)) {
            $chat->addParticipant($admin->id, null, null, 'admin');
        }

        return response()->json([
            'success' => true,
            'message' => 'Chat assigned successfully',
        ]);
    }

    /**
     * Close chat
     */
    public function close(Request $request, UnifiedChat $chat)
    {
        $chat->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Chat closed successfully',
        ]);
    }

    /**
     * Archive chat
     */
    public function archive(Request $request, UnifiedChat $chat)
    {
        $chat->update(['status' => 'archived']);

        return response()->json([
            'success' => true,
            'message' => 'Chat archived successfully',
        ]);
    }

    /**
     * Get chat statistics
     */
    public function statistics()
    {
        $stats = [
            'total_chats' => UnifiedChat::count(),
            'active_chats' => UnifiedChat::where('status', 'active')->count(),
            'closed_chats' => UnifiedChat::where('status', 'closed')->count(),
            'archived_chats' => UnifiedChat::where('status', 'archived')->count(),
            'unread_messages' => UnifiedChatMessage::where('is_read', false)->count(),
            'today_messages' => UnifiedChatMessage::whereDate('created_at', today())->count(),
            'assigned_to_me' => UnifiedChat::where('assigned_to', Auth::id())->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Mark chat messages as read
     */
    public function markAsRead(UnifiedChat $chat)
    {
        // Mark all messages in this chat as read for the current admin
        $chat->messages()->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Chat marked as read',
        ]);
    }

    /**
     * Mark chat messages as unread
     */
    public function markAsUnread(UnifiedChat $chat)
    {
        // Mark all messages in this chat as unread
        $chat->messages()->update(['is_read' => false]);
        
        return response()->json([
            'success' => true,
            'message' => 'Chat marked as unread',
        ]);
    }

    /**
     * Toggle chat active status
     */
    public function toggleActive(UnifiedChat $chat)
    {
        $newStatus = $chat->status === 'active' ? 'inactive' : 'active';
        $chat->update(['status' => $newStatus]);
        
        return response()->json([
            'success' => true,
            'message' => "Chat status changed to {$newStatus}",
        ]);
    }

    /**
     * Delete chat permanently
     */
    public function delete(UnifiedChat $chat)
    {
        // Delete all related messages and participants
        $chat->messages()->delete();
        $chat->participants()->delete();
        $chat->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Chat deleted successfully',
        ]);
    }

    /**
     * Export chats to Excel
     */
    public function export(Request $request)
    {
        $query = UnifiedChat::with(['messages', 'participants', 'assignedUser']);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        if ($request->has('assigned_to') && $request->assigned_to !== '') {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->has('start_date') && $request->start_date !== '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date !== '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $chats = $query->orderBy('created_at', 'desc')->get();

        $filename = 'chats_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($chats) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Chat ID',
                'Title',
                'Status',
                'Priority',
                'Assigned To',
                'Participants',
                'Message Count',
                'Last Message',
                'Created At',
                'Updated At'
            ]);

            foreach ($chats as $chat) {
                $participants = $chat->participants->map(function($p) {
                    return $p->participant_name ?? $p->user->name ?? 'Unknown';
                })->implode(', ');

                fputcsv($file, [
                    $chat->id,
                    $chat->title,
                    $chat->status,
                    $chat->priority,
                    $chat->assignedUser->name ?? 'Unassigned',
                    $participants,
                    $chat->messages->count(),
                    $chat->last_message_at ? $chat->last_message_at->format('Y-m-d H:i:s') : 'No messages',
                    $chat->created_at->format('Y-m-d H:i:s'),
                    $chat->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Broadcasting methods removed - using database polling instead
}