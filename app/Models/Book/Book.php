<?php

namespace App\Models\Book;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\BookStatus;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ru',
        'short_description',
        'short_description_ru',
        'long_description',
        'long_description_ru',
        'book_author_id',
        'book_language_id',
        'book_genre_id',
        'recommended_by_worker',
        'recommended_by_worker_id',
        'book_status', // recommended va unrecommended qabul qiladi
        'responsible_worker',
        'responsible_worker_id',
        'image_path',
    ];

    public function scopeByStatus($query, BookStatus $status)
    {
        if ($status !== BookStatus::UNRECOMMENDED) {
            return $query->where('book_status', $status->value);
        }
        return $query;
    }

    protected $casts = [
        'image_path' => 'array',
//        'book_status' => BookStatus::class,
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function featuredBooks()
    {
        return $this->hasMany(FeatureBook::class);
    }

    public function workerBooks()
    {
        return $this->hasMany(WorkerBook::class);
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
        return $this->hasMany(BooksLanguages::class);
    }

}
