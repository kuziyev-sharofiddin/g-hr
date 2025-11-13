<?php

namespace App\Models\Book;

use App\Enums\FeatureBookStatus;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'status',
        'date',
        'comment',
        'worker_id',
        'rating'
    ];

    protected $casts = [
        'status' => FeatureBookStatus::class,
    ];

    public function scopeByStatus($query, FeatureBookStatus $status)
    {
        return $query->where('status', $status->value);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function likes()
    {
        return $this->hasMany(FeatureBook::class)->where('status', FeatureBookStatus::LIKED);
    }

    public function featureBookLanguages()
    {
        return $this->belongsToMany(BookLanguage::class, 'feature_book_language', 'feature_book_id', 'book_language_id');
    }

    public function ratings()
    {
        return $this->hasMany(FeatureBook::class)->where('status', FeatureBookStatus::RATING);
    }
}
