<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhoneNumberHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'worker_id',
        'phone_number',
        'status',
        'responsible_worker'
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
