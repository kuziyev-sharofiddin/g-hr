<?php

namespace App\Models\RecommendedQuestionsForWorkerByWorker;

use App\Http\Resources\QuestionsForWorkerReportResource;
use App\Http\Resources\QuestionsForWorkerResource;
use App\Http\Resources\QuestionsForWorkerSectionsReportResource;
use App\Http\Resources\ReportTest\TestCategoryReportResource;
use App\Http\Resources\ReportTest\TestDegreeReportResource;
use App\Models\Position;
use App\Models\QuestionForWorkerCategory;
use App\Models\QuestionForWorkerLevel;
use App\Models\QuestionForWorkersStatistics;
use App\Models\QuestionsForWorker;
use App\Models\Section;
use App\Models\TestDescriptionByWorker;
use App\Models\Worker;
use App\QueryFilter\QuestionsForWorker\QuestionName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;

class RecommendedQuestionsForWorkersByWorker extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'recommended_by_worker_id',
        'section_id',
        'positions',
        'comment',
        'level_id',
        'responsible_worker',
        'question',
        'status',
        'answers',
        'correct_answer',
        'constancy',
        'description',
        'all_selected',
        'status_description',
        'question_description'
    ];
    public function worker()
    {
        return $this->belongsTo(Worker::class,'recommended_by_worker_id');
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
}
