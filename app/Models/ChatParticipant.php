<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'unified_chat_id',
        'user_id',
        'participant_email',
        'participant_name',
        'role',
        'joined_at',
        'last_read_at',
        'is_active',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the chat that owns the participant.
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(UnifiedChat::class, 'unified_chat_id');
    }

    /**
     * Get the user for this participant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the participant name (either from user or guest).
     */
    public function getParticipantNameAttribute($value): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $value ?? 'Guest';
    }

    /**
     * Get the participant email (either from user or guest).
     */
    public function getParticipantEmailAttribute($value): ?string
    {
        if ($this->user) {
            return $this->user->email;
        }
        return $value;
    }

    /**
     * Check if participant is an admin.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'support']);
    }

    /**
     * Check if participant is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Deactivate participant.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate participant.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Update last read time.
     */
    public function updateLastRead(): void
    {
        $this->update(['last_read_at' => now()]);
    }

    /**
     * Scope for active participants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for admin participants.
     */
    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['admin', 'support']);
    }

    /**
     * Scope for regular participants.
     */
    public function scopeRegular($query)
    {
        return $query->where('role', 'participant');
    }
}