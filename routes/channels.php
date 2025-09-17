<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // Check if user is authenticated
    if (!Auth::check()) {
        return false;
    }

    // Check if chat exists
    $chat = \App\Models\UnifiedChat::find($chatId);
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
});
