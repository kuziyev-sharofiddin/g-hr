<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use  SoftDeletes;

    protected $fillable = [
        'name',
        'state_id',
        'region_id',
        'address',
        'phone_number',
        'target',
        'location',
        'responsible_worker',
        'guid',
        'code'
    ];
}
