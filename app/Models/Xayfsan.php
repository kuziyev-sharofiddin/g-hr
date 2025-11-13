<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use App\Http\Resources\XayfsanResource;
use App\QueryFilter\Xayfsan\WorkerName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class   Xayfsan extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'type',
        'description',
        'status',
        'responsible_worker',
        'worker_id'
    ];

    public static function search($request)
    {
        $data = app(Pipeline::class)
            ->send(
                Xayfsan::query()->filter($request->all())
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return XayfsanResource::collection($data);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function xayfsanHistory()
    {
        return $this->hasMany(XayfsanHistory::class);
    }
}
