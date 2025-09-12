<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAttachment extends Model
{
    protected $fillable = [
        'chat_session_id', 'chat_message_id', 'original_name', 'path', 'mime_type', 'size_bytes'
    ];
}


