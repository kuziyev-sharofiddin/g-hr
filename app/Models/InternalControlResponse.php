<?php

namespace App\Models;

use App\Traits\Searchable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalControlResponse extends Model
{
    use HasFactory, SoftDeletes, Searchable, Filterable;

    protected $fillable = [
        'worker_id',
        'internal_control_template_id',
        'average_score',
        'final_conclusion',
        'responsible_worker'
    ];

    protected $casts = [
        'responsible_worker' => 'array'
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function internal_control_template()
    {
        return $this->belongsTo(InternalControlTemplate::class);
    }

    public function internalControlResponseDetail()
    {
        return $this->hasMany(InternalControlResponseDetail::class);
    }
}
