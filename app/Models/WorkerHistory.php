<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        "worker_id",
        "column_name",
        "odd_value",
        "new_value",
        "responsible_worker",
    ];

    protected $casts = [
        'responsible_worker' => 'array',
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
