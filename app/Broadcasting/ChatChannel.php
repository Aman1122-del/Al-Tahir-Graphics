<?php

namespace App\Broadcasting;

use App\Models\UnifiedChat;
use Illuminate\Support\Facades\Auth;

class ChatChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join($user, $chatId)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return false;
        }

        // Check if chat exists
        $chat = UnifiedChat::find($chatId);
        if (!$chat) {
            return false;
        }

        // Check if user is a participant in this chat
        $isParticipant = $chat->participants()
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('participant_email', $user->email);
            })
            ->exists();

        // Allow admins to join any chat
        $isAdmin = $user->hasRole(['admin', 'support']);

        return $isParticipant || $isAdmin;
    }
}
