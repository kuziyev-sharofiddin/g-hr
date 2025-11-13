<?php

namespace App\Models;

use App\Http\Resources\InterviewCandidateHandlingResource;
use App\Http\Resources\InterviewCandidateResource;
use App\QueryFilter\Anketa\InterviewCandidateName;
use App\QueryFilter\Anketa\InterviewRateCandidateName;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pipeline\Pipeline;

class InterviewCandidate extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'anketa_id',
        'interview_date',
        'interview_person',
        'responsible_interview_colled_worker',
        'interview_address',
        'description',
        'status',
        'confirmation',
        'send_notification_date',
        'arrival_to_work_date',
        'arrival_to_work_time',
        'send_notification_status',
        'dont_arrival_to_work_description',
        'interview_late_description_status',
        'interview_late_description',
        'worker_id',
    ];

    public static function interviewCandidateFilterSearch($request)
    {
        $interviewCandidate = app(Pipeline::class)
            ->send(
                    InterviewCandidate::filter($request->all())
                        ->where('status', 1)
                )
            ->through([
                InterviewCandidateName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return InterviewCandidateResource::collection($interviewCandidate);
    }

    public static function interviewRateCandidateFilterSearch($request)
    {
        $interviewCandidate = app(Pipeline::class)
            ->send(
                    InterviewCandidate::filter($request->all())
                        ->where('status', 2)
                )
            ->through([
                InterviewRateCandidateName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return InterviewCandidateHandlingResource::collection($interviewCandidate);
    }

    public function anketa(){
        return $this->belongsTo(Anketa::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function interviewDetail(){
        return $this->hasMany(InterviewDetail::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
