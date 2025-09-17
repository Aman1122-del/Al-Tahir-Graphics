<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnifiedChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'unified_chat_id',
        'sender_id',
        'sender_email',
        'sender_name',
        'message',
        'message_type',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'metadata',
        'is_read',
        'read_at',
        'is_edited',
        'edited_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    /**
     * Get the chat that owns the message.
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(UnifiedChat::class, 'unified_chat_id');
    }

    /**
     * Get the user who sent the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the sender name (either from user or guest).
     */
    public function getSenderNameAttribute($value): string
    {
        if ($this->sender) {
            return $this->sender->name;
        }
        return $value ?? 'Guest';
    }

    /**
     * Get the sender email (either from user or guest).
     */
    public function getSenderEmailAttribute($value): ?string
    {
        if ($this->sender) {
            return $this->sender->email;
        }
        return $value;
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
    public function getFileSizeFormattedAttribute(): ?string
    {
        if ($this->file_size) {
            $units = ['B', 'KB', 'MB', 'GB'];
            $i = 0;
            $size = $this->file_size;
            while ($size >= 1024 && $i < count($units) - 1) {
                $size /= 1024;
                $i++;
            }
            return round($size, 2) . ' ' . $units[$i];
        }
        return null;
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark message as edited.
     */
    public function markAsEdited(): void
    {
        $this->update([
            'is_edited' => true,
            'edited_at' => now(),
        ]);
    }

    /**
     * Scope for unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for messages from a specific sender.
     */
    public function scopeFromSender($query, $senderId)
    {
        return $query->where('sender_id', $senderId);
    }

    /**
     * Scope for messages from a specific email.
     */
    public function scopeFromEmail($query, $email)
    {
        return $query->where('sender_email', $email);
    }

    /**
     * Scope for file messages.
     */
    public function scopeWithFiles($query)
    {
        return $query->whereNotNull('file_path');
    }

    /**
     * Scope for text messages.
     */
    public function scopeTextOnly($query)
    {
        return $query->where('message_type', 'text');
    }
}