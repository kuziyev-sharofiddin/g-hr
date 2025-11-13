<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use App\QueryFilter\Order\WorkerName;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\OrderWorkerResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderWorker extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'old_branch_id',
        'new_branch_id',
        'worker_id',
        'register_number',
        'register_date',
        'status',           // 1-ishga qabul qilish, 2-ishdan bo'shatish, 3-boshqa filialga o'tqazish
        'guid',
        'responsible_worker'
    ];

    public static function filterSearch($request)
    {
        $list = app(Pipeline::class)
            ->send(
                OrderWorker::filter($request->all())
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return OrderWorkerResource::collection($list);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function oldBranch(){
        return $this->belongsTo(Branch::class, 'old_branch_id');
    }

    public function newBranch(){
        return $this->belongsTo(Branch::class, 'new_branch_id');
    }

    public function worker(){
        return $this->belongsTo(Worker::class);
    }
}
