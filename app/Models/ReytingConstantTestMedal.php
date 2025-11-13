<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReytingConstantTestMedal extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'medal',
        'season',
        'reyting',
        'star_count',
        'coin_count'
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
