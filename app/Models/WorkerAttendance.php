<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerAttendance extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'date'
    ];

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function workerAttendanceDetail(){
        return $this->hasMany(WorkerAttendanceDetail::class);
    }
}
