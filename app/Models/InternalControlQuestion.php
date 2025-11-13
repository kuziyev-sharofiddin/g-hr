<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalControlQuestion extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'name',
        'description', // nullable
        'responsible_worker'
    ];

    protected $searchable = ['name'];

    protected $casts = [
        'responsible_worker' => 'array'
    ];
}
