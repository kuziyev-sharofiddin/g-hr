<?php

namespace App\Models\Book;

use App\Enums\WorkerBookStatus;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ru',
        'book_id',
        'short_description',
        'short_description_ru',
        'long_description',
        'long_description_ru',
        'book_author_id',
        'book_language_id',
        'book_genre_id',
        'status',
        'worker_id',
        'comment',
        'image_path',
    ];
    protected $casts = [
        'image_path' => 'array',
        'status' => WorkerBookStatus::class,
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bookAuthor()
    {
        return $this->belongsTo(BookAuthor::class);
    }

    public function bookGenre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function booksLanguages()
    {
        return $this->hasMany(WorkerBooksLanguages::class);
    }

}
