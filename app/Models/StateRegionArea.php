<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\QueryFilter\StateRegionArea\Area;
use App\Http\Resources\StateRegionAreaResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StateRegionArea extends Model
{
    use HasFactory, Filterable;
    
    protected $fillable = [
        'state_id',
        'region_id',
        'area',
        'description',
        'responsible_worker'
    ];

    static function search($request)
    {
        $data = app(Pipeline::class)
            ->send(
                StateRegionArea::filter($request->all())
            )
            ->through([
                Area::class
            ])
            ->thenReturn()
            ->paginate(20);

        return StateRegionAreaResource::collection($data);
    }

    static function areaListByStateAndRegionSearch($request)
    {
        $data = app(Pipeline::class)
            ->send(
                StateRegionArea::query()->where('state_id', $request->state_id)->where('region_id', $request->region_id)
            )
            ->through([
                Area::class
            ])
            ->thenReturn()
            ->get();

        return StateRegionAreaResource::collection($data);
    }

    public function state(){
        return $this->belongsTo(State::class);
    }

    public function region(){
        return $this->belongsTo(Region::class);
    }

    public function clientAgetatsiya(){
        return $this->hasMany(ClientAgetatsiya::class);
    }

    public function keyForAgetatsiyaEmployee(){
        return $this->hasMany(KeyForAgetatsiyaEmployee::class);
    }
}
