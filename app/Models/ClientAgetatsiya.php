<?php

namespace App\Models;

use App\Http\Resources\ClientAgetatsiyaResource;
use App\QueryFilter\ClientAgetatsiya\ClientName;
use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientAgetatsiya extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'name',
        'phone',
        'phone1',
        'birthday',
        'state_id',
        'region_id',
        'address',
        'organization_for_agetatsiya_id',
        'organization_name_for_agetatsiya_id',
        'worker_id',
        'state_region_area_id',
        'key_for_agetatsiya_employee_id'
    ];

    static function search($request)
    {
        $data = app(Pipeline::class)
            ->send(
                ClientAgetatsiya::filter($request->all())
            )
            ->through([
                ClientName::class
            ])
            ->thenReturn()
            ->paginate(20);

        return ClientAgetatsiyaResource::collection($data);
    }

    public function state(){
        return $this->belongsTo(State::class);
    }

    public function region(){
        return $this->belongsTo(Region::class);
    }

    public function organizationForAgetatsiya(){
        return $this->belongsTo(OrganizationForAgetatsiya::class);
    }

    public function organizationNameForAgetatsiya(){
        return $this->belongsTo(OrganizationNameForAgetatsiya::class);
    }

    public function worker(){
        return $this->belongsTo(Worker::class);
    }

    public function stateRegionArea(){
        return $this->belongsTo(StateRegionArea::class);
    }

    public function keyForAgetatsiyaEmployee(){
        return $this->belongsTo(KeyForAgetatsiyaEmployee::class);
    }

    public function clientAgetatsiyaPerevod(){
        return $this->hasMany(ClientAgetatsiyaPerevod::class);
    }
}
