<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerAttestationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'start_date',
        'end_date',
        'star_count',
        'result_persent',
        'time_spend',
        'attestation_preparation_status',
        'attestation_status',
        'status'
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
