<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\XarakteristikaResource;
use App\QueryFilter\Xarakteristika\WorkerName;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Xarakteristika extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'worker_id',
        'xarakteristika_json',
        'description',
        'responsible_worker',
        'responsible_worker_id',
        'position_id'
    ];

    public static function search($request)
    {
        $data = app(Pipeline::class)
            ->send(
                (Auth::user()->role == 4 || Auth::user()->role == 1)
                    ? Xarakteristika::query()->filter($request->all())
                    : Xarakteristika::query()->filter($request->all())->where('responsible_worker_id', Auth::user()->worker_id)
                // ? Xarakteristika::query()->filter($request->all())
                //     ->where('responsible_worker_id', $request->responsible_worker_id)
                // : Xarakteristika::query()->filter($request->all())
                //     ->where('responsible_worker_id', Auth::user()->worker_id)
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return XarakteristikaResource::collection($data);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function workerResponsible()
    {
        return $this->belongsTo(Worker::class, 'responsible_worker_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
