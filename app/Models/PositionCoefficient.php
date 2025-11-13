<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionCoefficient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guid',
        'position_guid',
        'position_name',
        'coefficient',
        'date',
        'responsible_worker'
    ];

    protected $casts = [
        'responsible_worker' => 'array',
    ];

}
