<?php

namespace App\Models;

use App\Http\Resources\EmployeeWorkHistoryResource;
use App\QueryFilter\EmployeeWorkHistory\WorkerName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pipeline\Pipeline;

class EmployeeWorkHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'worker_id',
        'title',
        'text',
        'responsible_worker'
    ];

    public static function searchFilter($request)
    {
        $data = app(Pipeline::class)
            ->send(
                EmployeeWorkHistory::filter($request->all())
            )
            ->through([
                WorkerName::class
            ])
            ->thenReturn()
            ->paginate(20);

        return EmployeeWorkHistoryResource::collection($data);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }
}
