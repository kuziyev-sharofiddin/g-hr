<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthenticatorForWorker extends Model
{
    use HasFactory;

    protected $table = 'authenticator_for_workers';
    protected $fillable = [
        'worker_id', // UUID of the worker
        'code', // e.g., 'google', 'yubikey', etc.
        'active_date', // Date when the authenticator was activated
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
