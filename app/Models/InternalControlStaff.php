<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalControlStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'pages_permission',
        'responsible_worker',
    ];

    protected $casts = [
        'responsible_worker' => 'array',
        'pages_permission' => 'array',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
