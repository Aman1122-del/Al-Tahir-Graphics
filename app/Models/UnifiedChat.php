<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class UnifiedChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'status',
        'priority',
        'metadata',
        'last_message_at',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the messages for the chat.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(UnifiedChatMessage::class);
    }

    /**
     * Get the participants for the chat.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class);
    }

    /**
     * Get the user who created the chat.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the admin/support assigned to the chat.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all users participating in this chat.
     */
    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            ChatParticipant::class,
            'unified_chat_id',
            'id',
            'id',
            'user_id'
        );
    }

    /**
     * Get the latest message for the chat.
     */
    public function latestMessage()
    {
        return $this->hasOne(UnifiedChatMessage::class)->latest();
    }

    /**
     * Get unread messages count for a specific user.
     */
    public function getUnreadCountForUser($userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Check if user is a participant in this chat.
     */
    public function hasParticipant($userId): bool
    {
        return $this->participants()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if email is a participant in this chat.
     */
    public function hasParticipantByEmail($email): bool
    {
        return $this->participants()
            ->where('participant_email', $email)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Add a participant to the chat.
     */
    public function addParticipant($userId = null, $email = null, $name = null, $role = 'participant'): ChatParticipant
    {
        return $this->participants()->create([
            'user_id' => $userId,
            'participant_email' => $email,
            'participant_name' => $name,
            'role' => $role,
            'is_active' => true,
            'joined_at' => now(),
        ]);
    }

    /**
     * Mark messages as read for a user.
     */
    public function markAsReadForUser($userId): void
    {
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Update participant's last read time
        $this->participants()
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);
    }

    /**
     * Scope for active chats.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for chats assigned to a user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope for chats where user is a participant.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('is_active', true);
        });
    }

    /**
     * Scope for chats where email is a participant.
     */
    public function scopeForEmail($query, $email)
    {
        return $query->whereHas('participants', function ($q) use ($email) {
            $q->where('participant_email', $email)->where('is_active', true);
        });
    }
}