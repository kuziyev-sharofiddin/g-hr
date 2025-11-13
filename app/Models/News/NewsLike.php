<?php

namespace App\Models\News;

use App\Models\GarandNews;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'garand_new_id',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function news()
    {
        return $this->belongsTo(GarandNews::class);
    }
}
