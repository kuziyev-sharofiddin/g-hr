<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestComplaintChatMessage extends Model
{
    use HasFactory;

    protected $table = 'test_complaint_chat_messages';
    protected $fillable = [
        'chat_id',
        'sender_type', // user //admin
        'sender_id',
        'message',
        'image_paths',
        'reply_to_message_id',
        'is_read',
    ];

    protected $casts = [
        'image_paths' => 'array',
    ];

    public function chat()
    {
        return $this->belongsTo(TestComplaintChat::class, 'chat_id');
    }
}
