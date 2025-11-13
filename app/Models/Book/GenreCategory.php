<?php

namespace App\Models\Book;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenreCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_uz',
        'name_ru',
        'responsible_worker',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function workerBooks()
    {
        return $this->hasMany(WorkerBook::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'category_genre', 'genre_category_id', 'genre_id');
    }
}
