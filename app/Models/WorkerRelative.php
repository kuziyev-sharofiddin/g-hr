<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerRelative extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kinship',
        'name',
        'birthday',
        'workplace',
        'address',
        'worker_id'
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
