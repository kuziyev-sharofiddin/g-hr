<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddNewSections extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'workers_id',
        'positions_id'
    ];

    protected $casts = [
        'workers_id' => 'array',
        'positions_id' => 'array'
    ];
}
