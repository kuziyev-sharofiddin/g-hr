<?php

namespace App\Models;

use App\Http\Resources\InternalControlResource;
use App\QueryFilter\InternalControl\InternalControlName;
use App\Traits\Searchable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;

class InternalControl extends Model
{
    use HasFactory, Filterable, Searchable; //, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'worker_id',
        'internal_control_type_id',
        'summary',
        'rate',
        'responsible_worker',
    ];

    protected $searchable = [
        'worker.name',
        'branch.name',
        'internalControlType.topic_name',
    ];

    public static function internalControlSearch($request)
    {
        $certificate = app(Pipeline::class)
            ->send(
                InternalControl::query()->filter($request->all())
            )
            ->through([
                InternalControlName::class,
            ])
            ->thenReturn()
            ->orderBy('updated_at', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data' => InternalControlResource::collection($certificate),
        ]);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function internalControlType()
    {
        return $this->belongsTo(InternalControlType::class);
    }

}
