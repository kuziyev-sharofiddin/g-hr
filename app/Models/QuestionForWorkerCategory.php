<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionForWorkerCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'responsible_worker'
    ];

    public function questionsForWorker(){
        return $this->hasMany(QuestionsForWorker::class);
    }
}
