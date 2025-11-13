<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstantTestRandomCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'question_ids',
        'is_completed'
    ];

    protected $casts = [
        'question_ids' => 'array'
    ];
}
