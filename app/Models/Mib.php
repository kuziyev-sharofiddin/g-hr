<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mib extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'summa',
        'description',
        'status',
        'worker_id'
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
