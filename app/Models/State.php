<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name'
    ];

    public function region(){
        return $this->hasMany(Region::class);
    }

    public function branch(){
        return $this->hasMany(Branch::class);
    }

    public function anketa(){
        return $this->hasMany(Anketa::class);
    }

    public function worker(){
        return $this->hasMany(Worker::class);
    }

    public function keyForAgetatsiyaEmployee(){
        return $this->hasMany(KeyForAgetatsiyaEmployee::class);
    }

    public function stateRegionArea(){
        return $this->hasMany(StateRegionArea::class);
    }

    public function clientAgetatsiya(){
        return $this->hasMany(ClientAgetatsiya::class);
    }

    public function organizationNameForAgetatsiya(){
        return $this->hasMany(OrganizationNameForAgetatsiya::class);
    }
}
