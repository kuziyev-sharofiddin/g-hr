<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'worker_id',
        'rating',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

}
