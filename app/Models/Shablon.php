<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shablon extends Model
{
    use HasFactory;

    protected  $fillable = [
        'sorovnoma_questions_id',
        'name',
        'status',
        'term',
        'description',
        'responsible_worker',
        'branch_ids',
        'every_month_day',
    ];

    public $casts = [
        'sorovnoma_questions_id' => 'array',
        'branch_ids' => 'array'
    ];
}
