<?php

namespace App\Models;

use App\Http\Resources\SectionResource;
use App\QueryFilter\Section\SectionName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'guid',
        'name',
        'responsible_worker',
    ];

    public static function sectionSearch()
    {
        $section = app(Pipeline::class)
            ->send(
                Section::query()
            )
            ->through([
                SectionName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return SectionResource::collection($section);
    }

    public function worker()
    {
        return $this->hasMany(Worker::class);
    }

    public function sectionDetail()
    {
        return $this->hasMany(SectionDetail::class);
    }

    public function workingHoliday()
    {
        return $this->hasMany(WorkingHoliday::class);
    }

    public function dismissionWorker()
    {
        return $this->hasMany(DismissedWorker::class);
    }

    public function position()
    {
        return $this->hasMany(Position::class);
    }

    public function shtatka()
    {
        return $this->hasMany(Shtatka::class);
    }

    public function workerAttendanceDetail()
    {
        return $this->hasMany(WorkerAttendanceDetail::class);
    }

    public function questionsForWorker()
    {
        return $this->hasMany(QuestionsForWorker::class);
    }
}
