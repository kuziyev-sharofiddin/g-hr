<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionForWorkersStatistics extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'questions_for_workers_id',
        'true_or_false_answer',
        'answer_text'
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }

    public function questionForWorker(){
        return $this->belongsTo(QuestionsForWorker::class);
    }
}
