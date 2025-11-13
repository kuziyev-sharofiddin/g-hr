<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\QueryFilter\RelationWorker\WorkerName;
use App\Http\Resources\EmployeeApplicationResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeApplication extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'worker_id',
        'title_id',
        'business_trip_where_to',
        'old_position_id',
        'new_position_id',
        'dismissed_reason_id',
        'old_branch_id',
        'new_branch_id',
        'text',
        'status' // 1 -> Ko'rilmagan, 2 -> ko'rilgan, 3-admin javob berdi, 4-user javob berdi
    ];

    public static function filterSearch($request)
    {
        $data = app(Pipeline::class)
            ->send(
                EmployeeApplication::filter($request->all())
                    // ->orderByRaw("FIELD(status, '1', '2') ASC")
                    ->orderBy('created_at', 'DESC')
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return EmployeeApplicationResource::collection($data);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function title()
    {
        return $this->belongsTo(EmployeeApplicationTitle::class);
    }

    public function oldPosition()
    {
        return $this->belongsTo(Position::class, 'old_position_id');
    }

    public function newPosition()
    {
        return $this->belongsTo(Position::class, 'new_position_id');
    }

    public function dismissedReason()
    {
        return $this->belongsTo(DismissedWorkerReason::class, 'dismissed_reason_id');
    }

    public function oldBranch()
    {
        return $this->belongsTo(Branch::class, 'old_branch_id');
    }

    public function newBranch()
    {
        return $this->belongsTo(Branch::class, 'new_branch_id');
    }

    public function employeeApplicationMessage(){
        return $this->hasMany(EmployeeApplicationMessage::class, 'application_id')->latest();
    }
}
