<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $guarded = ['id'];

    public function sender(){
        return $this->belongsTo('App\Models\User', 'sender_id', 'id');
    }
}
