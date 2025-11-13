<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestComplaintChat extends Model
{
    use HasFactory;

    protected $table = 'test_complaint_chats';

    protected $fillable = [
        'worker_id',
        'test_description_by_worker_id',
        'question_for_worker_id', // Nullable field for future use
        'blocked',
    ];

    protected $casts = [
        'test_description_by_worker_id' => 'array', // Cast to array for JSONB field
    ];

    public function messages()
    {
        return $this->hasMany(TestComplaintChatMessage::class, 'chat_id');
    }

    public function latestMessage()
{
    return $this->hasOne(TestComplaintChatMessage::class, 'chat_id')->latestOfMany();
}


    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function test()
    {
        return $this->belongsTo(QuestionsForWorker::class, 'question_for_worker_id');
    }
}
