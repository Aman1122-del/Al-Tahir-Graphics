<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;

class ChatController extends Controller
{
    protected $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );
    }

    /**
     * Show the chat interface.
     */
    public function index()
    {
        $user = Auth::user();
        $chatUsers = $user->getChatUsers();
        
        return view('chat.index', compact('chatUsers'));
    }

    /**
     * Send a message.
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $receiverId = $request->receiver_id;

        // Check if user can send message to receiver
        if (!$this->canSendMessage($user, $receiverId)) {
            return response()->json(['error' => 'Unauthorized to send message to this user'], 403);
        }

        $messageData = [
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('chat_uploads', $fileName, 'public');
            
            $messageData['file_path'] = $filePath;
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_type'] = $file->getMimeType();
        }

        $message = Message::create($messageData);
        $message->load('sender');

        // Broadcast to Pusher
        $this->pusher->trigger('chat-channel', 'new-message', [
            'message' => $message,
            'sender' => $message->sender->name,
            'receiver_id' => $receiverId,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Fetch messages between two users.
     */
    public function fetchMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $otherUserId = $request->user_id;

        // Check if user can access messages with this user
        if (!$this->canAccessMessages($user, $otherUserId)) {
            return response()->json(['error' => 'Unauthorized to access these messages'], 403);
        }

        $messages = Message::betweenUsers($user->id, $otherUserId)
            ->with(['sender:id,name', 'receiver:id,name'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

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
            'sender_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $senderId = $request->sender_id;

        // Check if user can mark messages as read
        if (!$this->canAccessMessages($user, $senderId)) {
            return response()->json(['error' => 'Unauthorized to access these messages'], 403);
        }

        $updated = Message::where('sender_id', $senderId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'updated_count' => $updated,
        ]);
    }

    /**
     * Get unread message count for current user.
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->getUnreadMessageCount();

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
        $chatUsers = $user->getChatUsers();

        return response()->json([
            'success' => true,
            'users' => $chatUsers,
        ]);
    }

    /**
     * Check if user can send message to receiver.
     */
    private function canSendMessage($user, $receiverId)
    {
        if ($user->id == $receiverId) {
            return false; // Can't send message to self
        }

        if ($user->hasRole(['admin', 'support'])) {
            return true; // Admin/support can message anyone
        }

        // Regular users can only message admin/support
        $receiver = User::find($receiverId);
        return $receiver && $receiver->hasRole(['admin', 'support']);
    }

    /**
     * Check if user can access messages with another user.
     */
    private function canAccessMessages($user, $otherUserId)
    {
        if ($user->id == $otherUserId) {
            return false; // Can't access messages with self
        }

        if ($user->hasRole(['admin', 'support'])) {
            return true; // Admin/support can access all messages
        }

        // Regular users can only access messages with admin/support
        $otherUser = User::find($otherUserId);
        return $otherUser && $otherUser->hasRole(['admin', 'support']);
    }
}
