<?php

namespace App\Models\Book;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'comment_id',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
