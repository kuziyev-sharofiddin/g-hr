<?php

namespace App\Models\Book;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BooksLanguages extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'book_language_id',
        'name',
        'short_description',
        'long_description',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function language()
    {
        return $this->belongsTo(BookLanguage::class, 'book_language_id');
    }
}
