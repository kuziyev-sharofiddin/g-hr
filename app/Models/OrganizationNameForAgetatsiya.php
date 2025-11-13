<?php

namespace App\Models;

use App\Traits\Searchable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationNameForAgetatsiya extends Model
{
    use HasFactory, Filterable, Searchable;

    protected $fillable = [
        'name',
        'organization_for_agetatsiya_id',
        'state_id',
        'region_id',
        'key_for_agetatsiya_employee_id',
        'responsible_worker'
    ];

    protected $searchable = [
        'name'
    ];

    public function organizationForAgetatsiya(){
        return $this->belongsTo(OrganizationForAgetatsiya::class);
    }
    
    public function state(){
        return $this->belongsTo(State::class);
    }

    public function region(){
        return $this->belongsTo(Region::class);
    }

    public function keyForAgetatsiyaEmployee(){
        return $this->belongsTo(KeyForAgetatsiyaEmployee::class);
    }

    public function clientAgetatsiya(){
        return $this->hasMany(ClientAgetatsiya::class);
    }
}
