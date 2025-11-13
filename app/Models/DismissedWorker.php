<?php

namespace App\Models;

use App\Http\Resources\DismissedWorkerResource;
use App\QueryFilter\RelationWorker\WorkerName;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pipeline\Pipeline;

class DismissedWorker extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'worker_id',
        'manager',
        'branch_id',
        'section_id',
        'position_id',
        'indebtedness',
        'indebtedness_text',
        'social_networks',
        'uniform',
        'reason_id',
        'description',
        'reviewer_curator',
        'reviewer_hr',
        'reviewer_curator_status',
        'reviewer_hr_status',
        'reviewer_curator_comment',
        'reviewer_hr_comment',
        'status',
        'responsible_worker',
    ];

    public static function search($request)
    {
        $dismissedWorker = app(Pipeline::class)
            ->send(
                DismissedWorker::filter($request->all())->actionStatusWeb($request->action_status)
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return DismissedWorkerResource::collection($dismissedWorker);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function dissmissedWorkerReason()
    {
        return $this->belongsTo(DismissedWorkerReason::class, 'reason_id');
    }

    public function scopeActionStatusMobile($query, $actionStatus, $type)
    {
        $validStatuses = ['pending', 'completed', 'canceled'];

        // actionStatus noto'g'ri yoki bo'sh bo'lsa hech narsa qaytmasin
        if (empty($actionStatus) || !in_array($actionStatus, $validStatuses)) {
            return $query->whereRaw('0 = 1');
        }

        if ($type === 'action_hr') {
            if ($actionStatus === 'pending') {
                $query->whereNull('reviewer_hr_status');
            } elseif ($actionStatus === 'completed') {
                $query->where('reviewer_hr_status', true);
            } elseif ($actionStatus === 'canceled') {
                $query->where(function ($q) {
                    $q->where('reviewer_hr_status', false);
                });
            }
            return $query;
        }

        if ($type === 'action_curator') {
            if ($actionStatus === 'pending') {
                $query->whereNull('reviewer_curator_status');
            } elseif ($actionStatus === 'completed') {
                $query->where('reviewer_curator_status', true);
            } elseif ($actionStatus === 'canceled') {
                $query->where(function ($q) {
                    $q->where('reviewer_curator_status', false);
                });
            }
            return $query;
        }

        return $query->whereRaw('0 = 1');
    }

    public function scopeActionStatusWeb($query, $actionStatus, $search = null)
    {
        $validStatuses = ['pending', 'half_confrimed', 'completed', 'canceled'];

        // Noto‘g‘ri status bo‘lsa hech nima qaytmasin
        if (empty($actionStatus) || !in_array($actionStatus, $validStatuses)) {
            return $query->whereRaw('0 = 1');
        }

        switch ($actionStatus) {
            case 'pending':
                $query->where('status', 0);
                break;

            case 'half_confrimed':
                $query->where('status', 2);
                break;

            case 'completed':
                $query->where('status', 1);
                break;

            case 'canceled':
                $query->where('status', 3);
                break;
        }

        // Qidiruv
        if (!empty($search)) {
            $query->whereHas('worker', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
                // Agar boshqa ustunlar ham bo‘lsa, ular ham qo‘shiladi:
                // $q->orWhere('surname', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function firstViewerCurator()
    {
        return $this->belongsTo(Worker::class, 'reviewer_curator');
    }

    public function secondViewerHr()
    {
        return $this->belongsTo(Worker::class, 'reviewer_hr');
    }
}
