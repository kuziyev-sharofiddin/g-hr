<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'image_path',
        'responsible_worker',
    ];

    protected $casts = [
        'responsible_worker' => 'array',
    ];
}
