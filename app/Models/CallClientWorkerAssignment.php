<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallClientWorkerAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'call_center_extension_code',
        'responsible_worker',
    ];

    protected $casts = [
        'responsible_worker' => 'array',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}
