<?php

namespace App\Models\Book;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function workerBooks()
    {
        return $this->hasMany(WorkerBook::class);
    }

    public function boooks()
    {
        return $this->belongsToMany(Book::class, 'book_language', 'book_language_id', 'book_id');
    }

    public function featureBooks()
    {
        return $this->belongsToMany(FeatureBook::class, 'feature_book_language', 'book_language_id', 'feature_book_id');
    }

}
