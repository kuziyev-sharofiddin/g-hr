<?php

namespace App\Models;

use App\Http\Resources\OrganizationForAgetatsiyaResource;
use App\QueryFilter\OrganizationForAgetatsiya\OrganizationName;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationForAgetatsiya extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'responsible_worker'
    ];

    static function search($request)
    {
        $data = app(Pipeline::class)
            ->send(
                OrganizationForAgetatsiya::query()
            )
            ->through([
                OrganizationName::class
            ])
            ->thenReturn()
            ->get();

        return OrganizationForAgetatsiyaResource::collection($data);
    }

    public function clientAgetatsiya(){
        return $this->hasMany(ClientAgetatsiya::class);
    }

    public function organizationNameForAgetatsiya(){
        return $this->hasMany(OrganizationNameForAgetatsiya::class);
    }
}
