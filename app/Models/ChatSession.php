<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    protected $fillable = [
        'status', 'user_name', 'user_email', 'user_phone', 'topic', 'agent_offered', 'agent_offer_at', 'agent_id'
    ];

    protected $casts = [
        'agent_offered' => 'boolean',
        'agent_offer_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ChatAttachment::class);
    }

    public function ticket()
    {
        return $this->hasOne(ChatTicket::class);
    }
}


