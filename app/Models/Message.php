<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'file_path',
        'file_name',
        'file_type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Scope to get unread messages for a user.
     */
    public function scopeUnread(Builder $query, $userId): void
    {
        $query->where('receiver_id', $userId)->whereNull('read_at');
    }

    /**
     * Scope to get messages between two users.
     */
    public function scopeBetweenUsers(Builder $query, $user1Id, $user2Id): void
    {
        $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
        });
    }

    /**
     * Check if message is read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Check if message has file attachment.
     */
    public function hasFile(): bool
    {
        return !is_null($this->file_path);
    }

    /**
     * Get file URL for public access.
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->hasFile()) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    /**
     * Get file size in human readable format.
     */
    public function getFileSizeAttribute(): ?string
    {
        if ($this->hasFile() && file_exists(storage_path('app/public/' . $this->file_path))) {
            $size = filesize(storage_path('app/public/' . $this->file_path));
            $units = ['B', 'KB', 'MB', 'GB'];
            $i = 0;
            while ($size >= 1024 && $i < count($units) - 1) {
                $size /= 1024;
                $i++;
            }
            return round($size, 2) . ' ' . $units[$i];
        }
        return null;
    }
}
