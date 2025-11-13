<?php

namespace App\Models\Book;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ru',
        'responsible_worker',
        'genre_category_id'
    ];


    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function workerBooks()
    {
        return $this->hasMany(WorkerBook::class);
    }

    public function genreCategories()
    {
        return $this->belongsToMany(GenreCategory::class, 'category_genre', 'genre_id', 'genre_category_id');
    }
}
