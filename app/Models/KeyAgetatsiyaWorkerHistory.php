<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyAgetatsiyaWorkerHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'start_date',
        'end_date',
        'region_id',
        'area',
        'active_status',
        'key_agetatsiya_employee_id'
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }

    public function region(){
        return $this->belongsTo(Region::class);
    }

    public function keyForAgetatsiyaEmployee(){
        return $this->belongsTo(KeyForAgetatsiyaEmployee::class, 'key_agetatsiya_employee_id');
    }
}
