<?php

namespace App\Models\Book;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerBooksLanguages extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_book_id',
        'book_language_id',
        'name',
        'short_description',
        'long_description',
    ];

    public function workerBook()
    {
        return $this->belongsTo(WorkerBook::class);
    }

    public function language()
    {
        return $this->belongsTo(BookLanguage::class, 'book_language_id');
    }
}
