<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSetting extends Model
{
    protected $fillable = [
        'force_bot_only', 'force_live_only', 'business_hours'
    ];

    protected $casts = [
        'force_bot_only' => 'boolean',
        'force_live_only' => 'boolean',
        'business_hours' => 'array',
    ];
}


