<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatTicket extends Model
{
    protected $fillable = [
        'chat_session_id', 'ticket_number', 'status', 'subject'
    ];
}


