<?php

namespace App\Models;

use App\Http\Resources\CertificateTypeResource;
use Illuminate\Pipeline\Pipeline;
use App\QueryFilter\CertificateType\CertificateTypeName;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateType extends Model
{
    use HasFactory, Filterable;//, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'responsible_worker',
    ];

    public static function certificateTypeSearch($request)
    {
        $certificateType = app(Pipeline::class)
            ->send(
                CertificateType::query()->filter($request->all())
            )
            ->through([
                CertificateTypeName::class,
            ])
            ->thenReturn()
            ->orderBy('id', 'DESC')
            ->get();

        return CertificateTypeResource::collection($certificateType);
    }

    
}
