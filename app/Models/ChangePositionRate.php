<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangePositionRate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rate',
        'description',
        'change_position_id'
    ];

    public function changePosition(){
        return $this->belongsTo(ChangePosition::class);
    }
}
