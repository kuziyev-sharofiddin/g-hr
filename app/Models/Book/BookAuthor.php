<?php

namespace App\Models\Book;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAuthor extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'name',
        'description',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function workerBooks()
    {
        return $this->hasMany(WorkerBook::class);
    }
}
