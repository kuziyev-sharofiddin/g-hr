<?php

namespace App\Models;

use App\Models\Anketa;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\TestResultResource;
use App\QueryFilter\Anketa\InterviewCandidateName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionResoult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'anketa_id',
        'question_count',
        'correct_answers',
        'persent',
        'date',
        'time',
        'test_category',
        'status'
    ];

    public static function testResultsFilterSearch($request)
    {
        $list = app(Pipeline::class)
            ->send(
                QuestionResoult::query()
                    ->whereHas('anketa', function ($query) use ($request) {
                        $query->filter($request->all())
                            ->where('status', 1);
                    })
                    ->where('status', 1)
            )
            ->through([
                InterviewCandidateName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return TestResultResource::collection($list);
    }

    public function anketa()
    {
        return $this->belongsTo(Anketa::class);
    }
}
