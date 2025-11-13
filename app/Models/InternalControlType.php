<?php

namespace App\Models;

use App\Http\Resources\InternalControlTypeResource;
use Illuminate\Pipeline\Pipeline;
use App\QueryFilter\CertificateType\CertificateTypeName;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalControlType extends Model
{
    use HasFactory, Filterable;//, SoftDeletes;

    protected $fillable = [
        'topic_name',
        'description',
        'responsible_worker',
    ];

    public static function internalControlTypeSearch($request)
    {
        $certificateType = app(Pipeline::class)
            ->send(
                InternalControlType::query()->filter($request->all())
            )
            ->through([
                CertificateTypeName::class,
            ])
            ->thenReturn()
            ->orderBy('updated_at', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data' => InternalControlTypeResource::collection($certificateType)
        ]);
    }
}
