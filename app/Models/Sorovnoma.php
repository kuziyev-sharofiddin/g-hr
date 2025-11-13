<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sorovnoma extends Model
{
    use HasFactory, Filterable;

    protected  $fillable = [
        'worker_id',
        'branch_id',
        'hidden_worker_status'
    ];

    public function sorovnomaDetails()
    {
        return $this->hasMany(SorovnomaDetail::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
