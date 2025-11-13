<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReserveWorker extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'old_position_id',
        'new_position_id',
        'description',
        'responsible_worker'
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }

    public function oldPosition(){
        return $this->belongsTo(Position::class, 'old_position_id');
    }

    public function newPosition(){
        return $this->belongsTo(Position::class, 'new_position_id');
    }
}
