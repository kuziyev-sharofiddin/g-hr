<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SorovnomaRandom extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'worker_ids',
        'random_date',
        'status'
    ];

    protected $casts = [
        'worker_ids' => 'array',
        'random_date' => 'array',
    ];
}
