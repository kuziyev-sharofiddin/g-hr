<?php

namespace App\Models\Book;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'worker_id',
        'description',
        'parent_id'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
