<?php

namespace App\Models;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\DismissedWorkerReasonResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\QueryFilter\DismissedWorkerReason\DismissedWorkerReasonName;
use Illuminate\Database\Eloquent\SoftDeletes;

class DismissedWorkerReason extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'responsible_worker'
    ];

    public static function dismissedWorkerReasonSearch(){
        $data = app(Pipeline::class)
            ->send(
                    DismissedWorkerReason::query()
                )
            ->through([
                DismissedWorkerReasonName::class,
            ])
            ->thenReturn()
            ->get();

        return DismissedWorkerReasonResource::collection($data);
    }

    public function dissmissedWorker(){
        return $this->hasMany(DismissedWorker::class, 'reason_id');
    }

    public function employeeApplication()
    {
        return $this->hasMany(EmployeeApplication::class);
    }
}
