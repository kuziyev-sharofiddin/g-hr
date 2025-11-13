<?php

namespace App\Models\Book;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryGenre extends Pivot
{
    use HasFactory;
    protected $table = 'category_genre';
    public $timestamps = true;
}
