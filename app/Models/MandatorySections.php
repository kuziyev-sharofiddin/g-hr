<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MandatorySections extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'branches',
        'positions',
        'workers',
        'status'
    ];

    protected $casts = [
        'branches' => 'json',
        'positions' => 'json',
        'workers' => 'json',
    ];
}
