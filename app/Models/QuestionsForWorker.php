<?php

namespace App\Models;

use App\Http\Resources\ReportTest\TestCategoryReportResource;
use App\Http\Resources\ReportTest\TestDegreeReportResource;
use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\QuestionsForWorkerResource;
use App\QueryFilter\QuestionsForWorker\QuestionName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Resources\QuestionsForWorkerReportResource;
use App\Http\Resources\QuestionsForWorkerSectionsReportResource;

class QuestionsForWorker extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'category_id',
        'section_id',
        'positions',
        'level_id',
        'responsible_worker',
        'recommended_by_worker_id',
        'question',
        'answers',
        'correct_answer',
        'constancy',
        'description',
        'all_selected',
        'status_description',
        'question_description'
    ];

    public static function filterSearch($request)
    {
        $questions = app(Pipeline::class)
            ->send(
                QuestionsForWorker::filter($request->all())
                    ->orderByDesc('id')
            )
            ->through([
                QuestionName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return QuestionsForWorkerResource::collection($questions);
    }

    public static function questionsReport($request)
    {
        $data = QuestionsForWorker::filter($request->all())->get();

        if ($request->position_id) {
            if ($request->position_id == -1) {
                $positions = collect([
                    (object)[
                        'id' => -1,
                        'name' => "Barchasi uchun",
                        'section_id' => -1
                    ]
                ]);
            } else {
                $positions = Position::query()->where('id', $request->position_id)->select('id', 'name', 'section_id')->orderBy('name', 'ASC')->get();
            }
        } else {
            $positions = Position::query()->select('id', 'name', 'section_id')->orderBy('name', 'ASC')->get();
        }

        $request->questions = $data;

        if ($request->report_type == "sections") {
            $sections = Section::query()->orderBy('name', 'ASC')->get();

            return [
                'all_questions_count' => $data->count(),
                'details' => QuestionsForWorkerSectionsReportResource::collection($sections)
            ];
        }

        if ($request->report_type == "degree") {
            $data = QuestionsForWorker::query()->groupBy('level_id')->get();
            return [
                'all_questions_count' => QuestionsForWorker::query()->where('level_id', '!=', null)->count(),
                'details' => TestDegreeReportResource::collection($data)
            ];
        }

        if ($request->report_type == "category") {
            $data = QuestionsForWorker::query()->groupBy('category_id')->get();
            return [
                'all_questions_count' => QuestionsForWorker::query()->where('level_id', '!=', null)->count(),
                'details' => TestCategoryReportResource::collection($data)
            ];
        }

        return [
            'all_questions_count' => $data->count(),
            'details' => QuestionsForWorkerReportResource::collection($positions)
        ];
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function category()
    {
        return $this->belongsTo(QuestionForWorkerCategory::class, 'category_id');
    }

    public function level()
    {
        return $this->belongsTo(QuestionForWorkerLevel::class, 'level_id');
    }

    public function testDescriptionByWorker()
    {
        return $this->hasMany(TestDescriptionByWorker::class);
    }

    public function questionForWorkerStatistics()
    {
        return $this->hasMany(QuestionForWorkersStatistics::class);
    }
}
