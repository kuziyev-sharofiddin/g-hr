<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shtatka extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $fillable = [
        'branch_id',
        'section_id',
        'position_id',
        'worker_count',
        'responsible_worker'
    ];

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }

    public function position(){
        return $this->belongsTo(Position::class);
    }
}
