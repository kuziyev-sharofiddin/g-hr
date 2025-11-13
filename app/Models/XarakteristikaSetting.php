<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\XarakteristikaSettingResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\QueryFilter\XarakteristikaSetting\PositionName;

class XarakteristikaSetting extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'position_id',
        'branches_json',
        'responsible_worker'
    ];

    public static function search($request)
    {
        $data = app(Pipeline::class)
            ->send(
                XarakteristikaSetting::query()->filter($request->all())
            )
            ->through([
                PositionName::class,
            ])
            ->thenReturn()
            ->orderBy('id', 'DESC')
            ->get();

        $request->position_list = Position::all();

        return XarakteristikaSettingResource::collection($data);
    }

    public function xarakteristikaSettingDetail()
    {
        return $this->hasMany(XarakteristikaSettingDetail::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
