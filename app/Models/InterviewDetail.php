<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rate',
        'description',
        'proposed_position',
        'future_position',
        'reason_for_rejection',
        'interview_candidate_id'
    ];

    public function interviewCandidate(){
        return $this->belongsTo(InterviewCandidate::class);
    }
}
