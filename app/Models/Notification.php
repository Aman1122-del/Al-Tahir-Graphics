<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'is_important',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'is_important' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): void
    {
        $query->whereNull('read_at');
    }

    public function scopeImportant(Builder $query): void
    {
        $query->where('is_important', true);
    }

    public function scopeByType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public static function createForUser(int $userId, string $type, string $title, string $message, array $data = [], bool $isImportant = false): self
    {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_important' => $isImportant,
        ]);
    }

    public static function createForAdmins(string $type, string $title, string $message, array $data = [], bool $isImportant = false): void
    {
        $adminIds = User::role(['admin', 'support'])->pluck('id');
        
        foreach ($adminIds as $adminId) {
            static::createForUser($adminId, $type, $title, $message, $data, $isImportant);
        }
    }
}
