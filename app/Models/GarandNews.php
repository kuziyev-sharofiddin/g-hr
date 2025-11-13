<?php

namespace App\Models;

use App\Models\News\NewsComment;
use App\Models\News\NewsLike;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarandNews extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'viewers_count' => 'array',
        'images' => 'array',
    ];

    public function comments()
    {
        return $this->hasMany(NewsComment::class,'garand_new_id');
    }
    public function likes()
    {
        return $this->hasMany(NewsLike::class,'garand_new_id');
    }
}
