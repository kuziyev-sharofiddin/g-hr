<?php

namespace App\Models;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\ChangeBranchResource;
use App\QueryFilter\RelationWorker\WorkerName;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChangeBranch extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'branch_id',
        'worker_id',
        'old_branch_id',
        'new_branch_id',
        'description',
        'change_date',
        'responsible_worker'
    ];

    public static function search($request)
    {
        $changeBranch = app(Pipeline::class)
            ->send(
                ChangeBranch::filter($request->all())
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return ChangeBranchResource::collection($changeBranch);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function oldBranch()
    {
        return $this->belongsTo(Branch::class, 'old_branch_id');
    }

    public function newBranch()
    {
        return $this->belongsTo(Branch::class, 'new_branch_id');
    }
}
