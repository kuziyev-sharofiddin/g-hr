<?php

namespace App\Models;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Resources\EmployeeApplicationTitleResource;
use App\QueryFilter\EmployeeApplicationTitle\EmployeeApplicationTitleName;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeApplicationTitle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'responsible_worker'
    ];

    public static function employeeApplicationTitleSearch(){
        $data = app(Pipeline::class)
            ->send(
                    EmployeeApplicationTitle::query()
                )
            ->through([
                EmployeeApplicationTitleName::class,
            ])
            ->thenReturn()
            ->get();

        return EmployeeApplicationTitleResource::collection($data);
    }

    public function employeeApplication()
    {
        return $this->hasMany(EmployeeApplication::class);
    }
}
