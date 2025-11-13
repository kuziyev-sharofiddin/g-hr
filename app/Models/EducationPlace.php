<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationPlace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'degree',
        'specialty',
        'period',
        'worker_id'
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
