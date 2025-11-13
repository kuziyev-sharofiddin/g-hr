<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReytingAttestationPreparationTest extends Model
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
        'time_spend'
    ];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
