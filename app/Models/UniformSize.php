<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniformSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'size',
        'season',
        'brand',
        'quantity',
        'uniform_id'
    ];

    public function uniform(){
        return $this->belongsTo(Uniform::class);
    }
}
