<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestDescriptionByWorker extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'description',
        'question_for_worker_id',
        'worker_id',
        'status'
    ];

    public function questionForWorker(){
        return $this->belongsTo(QuestionsForWorker::class);
    }

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
