<?php

namespace App\Models;

use App\Http\Resources\CertificateResource;
use Illuminate\Pipeline\Pipeline;
use App\QueryFilter\Certificate\CertificateName;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Certificate extends Model
{
    use HasFactory, Filterable;//, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'worker_id',
        'certificate_type_id',
        'image',
        'date',
        'description',
        'responsible_worker',
    ];

    public static function certificateSearch($request)
    {
        $certificate = app(Pipeline::class)
            ->send(
                Certificate::query()->filter($request->all())
            )
            ->through([
                CertificateName::class,
            ])
            ->thenReturn()
            ->orderBy('id', 'DESC')
            ->get();

        return CertificateResource::collection($certificate);
    }

    public static function certificateForWorker()
    {
        $certificate = app(Pipeline::class)
            ->send(
                Certificate::query()->where('worker_id', Auth::user()->worker_id)
            )
            ->through([
                // CertificateName::class,
            ])
            ->thenReturn()
            ->orderBy('id', 'DESC')
            ->get();

        return CertificateResource::collection($certificate);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function certificateType()
    {
        return $this->belongsTo(CertificateType::class);
    }
}
