<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\ChangePositionResource;
use App\QueryFilter\RelationWorker\WorkerName;
use App\Http\Resources\ChangePositionOtherResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChangePosition extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'worker_id',
        'old_position_id',
        'new_position_id',
        'description',
        'interview_person_id',
        'interview_date',
        'change_date',
        'status',
        'responsible_worker'
    ];

    public static function search($request)
    {
        $changePosition = app(Pipeline::class)
            ->send(
                ChangePosition::filter($request->all())
                    ->where('status', $request->tab_id)
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return ChangePositionResource::collection($changePosition);
    }

    public static function searchOtherPage($request)
    {
        $changePosition = app(Pipeline::class)
            ->send(
                ChangePosition::filter($request->all())
                    ->where('status', 4)
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return ChangePositionOtherResource::collection($changePosition);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function oldPosition()
    {
        return $this->belongsTo(Position::class, 'old_position_id');
    }

    public function newPosition()
    {
        return $this->belongsTo(Position::class, 'new_position_id');
    }

    public function changePositionRate()
    {
        return $this->hasMany(ChangePositionRate::class);
    }

    public function interviewPreson()
    {
        return $this->belongsTo(Worker::class, 'interview_person_id');
    }
}
