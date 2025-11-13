<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReytingConstantTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'all_questions',
        'correct_answers',
        'result_persent',
        'stars_count',
        'season',
        'old_position',
        'time_spend',
        'transaction_status'
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
