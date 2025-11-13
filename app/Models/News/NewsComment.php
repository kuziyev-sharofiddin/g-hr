<?php

namespace App\Models\News;

use App\Events\GarantNews;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'garand_new_id',
        'worker_id',
        'description',
        'parent_id'
    ];

//    public function news()
//    {
//        return $this->belongsTo(GarantNews::class);
//    }
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function replies()
    {
        return $this->hasMany(NewsComment::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(NewsComment::class, 'parent_id');
    }
}
