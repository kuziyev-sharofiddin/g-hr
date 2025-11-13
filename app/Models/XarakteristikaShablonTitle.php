<?php

namespace App\Models;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Resources\Api\XarakteristikaShablonTitleResource;
use App\QueryFilter\XarakteristikaShablonTitle\XarakteristikaShablonTitleName;

class XarakteristikaShablonTitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'responsible_worker'
    ];

    public static function search()
    {
        $data = app(Pipeline::class)
            ->send(
                XarakteristikaShablonTitle::query()
            )
            ->through([
                XarakteristikaShablonTitleName::class,
            ])
            ->thenReturn()
            ->orderBy('name', 'ASC')
            ->get();

        return XarakteristikaShablonTitleResource::collection($data);
    }
}
