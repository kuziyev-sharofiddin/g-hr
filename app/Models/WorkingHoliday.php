<?php

namespace App\Models;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\QueryFilter\RelationWorker\WorkerName;
use App\Http\Resources\WorkingHolidayWorkersResource;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingHoliday extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'section_id',
        'worker_id',
        'start_date',
        'end_date',
        'returned_date',
        'description',
        'status',
        'responsible_worker'
    ];

    public static function workingHolidayFilterSearch($request)
    {
        $workingHoliday = app(Pipeline::class)
            ->send(
                WorkingHoliday::filter($request->all())
                    ->where('status', 1)
            )
            ->through([
                WorkerName::class
            ])
            ->thenReturn()
            ->paginate(20);

        return WorkingHolidayWorkersResource::collection($workingHoliday);
    }

    public static function workingHolidayReportFilterSearch($request)
    {
        $workingHoliday = app(Pipeline::class)
            ->send(
                WorkingHoliday::filter($request->all())
                    ->where('status', 2)
            )
            ->through([
                WorkerName::class
            ])
            ->thenReturn()
            ->paginate(20);

        return WorkingHolidayWorkersResource::collection($workingHoliday);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
