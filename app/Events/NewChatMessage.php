<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\UnifiedChatMessage;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $chatId;

    /**
     * Create a new event instance.
     */
    public function __construct(UnifiedChatMessage $message)
    {
        $this->message = $message->load('sender');
        $this->chatId = $message->unified_chat_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->chatId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-message';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'sender_id' => $this->message->sender_id,
                'sender_name' => $this->message->sender->name ?? 'Admin',
                'sender_email' => $this->message->sender->email ?? null,
                'created_at' => $this->message->created_at->toISOString(),
                'message_type' => $this->message->message_type,
                'file_path' => $this->message->file_path,
                'file_name' => $this->message->file_name,
                'file_type' => $this->message->file_type,
                'file_size' => $this->message->file_size,
                'is_read' => $this->message->is_read,
            ],
            'chat_id' => $this->chatId,
            'timestamp' => now()->toISOString(),
        ];
    }
}
