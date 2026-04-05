<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    protected $table = 'ai_chat_messages';

    protected $fillable = ['mahasiswa_id', 'pb_id', 'role', 'content'];
}
