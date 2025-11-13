<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StartSeasonWorkerConfirm extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'season',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
