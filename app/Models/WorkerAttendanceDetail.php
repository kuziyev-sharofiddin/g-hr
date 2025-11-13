<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Section;
use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\QueryFilter\Attendance\WorkerName;
use App\Http\Resources\WorkerAttendanceMonthResource;
use App\Http\Resources\WorkerAttendanceDetailResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerAttendanceDetail extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'section_id',
        'worker_id',
        'status',
        'description',
        'date',
        'worker_attendance_id'
    ];

    public static function workerAttendanceSearch($branch_id)
    {
        $workerAttendance = app(Pipeline::class)
            ->send(
                WorkerAttendanceDetail::query()
                    ->where('branch_id', $branch_id)
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->get();

        return WorkerAttendanceDetailResource::collection($workerAttendance);
    }

    public static function workerAttendanceReportSearch($request)
    {
        $data = app(Pipeline::class)
            ->send(
                WorkerAttendanceDetail::filter($request->all())
                    ->select('worker_id')
                    ->groupBy('worker_id')
            )
            ->through([
                WorkerName::class
            ])
            ->thenReturn()
            ->orderBy(
                Worker::select('name')
                    ->whereColumn('workers.id', 'worker_attendance_details.worker_id')
            )
            ->paginate(20);

        $request->workerAttendanceDetail = WorkerAttendanceDetail::filter($request->all())->get();
        
        return WorkerAttendanceMonthResource::collection($data);
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

    public function workerAttendance()
    {
        return $this->belongsTo(WorkerAttendance::class);
    }
}
