<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\AdaptatsiyaResource;
use App\QueryFilter\RelationWorker\WorkerName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Adaptatsiya extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'worker_id',
        'branch_id',
        'responsible_worker_id',
        'from_date',
        'status',
        'responsible_worker'
    ];

    public static function filterSearch($request)
    {
        $list = app(Pipeline::class)
            ->send(
                Adaptatsiya::filter($request->all())
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return AdaptatsiyaResource::collection($list);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function responsibleWorker()
    {
        return $this->belongsTo(Worker::class, 'responsible_worker_id');
    }
}
