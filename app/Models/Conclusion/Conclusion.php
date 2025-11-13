<?php

namespace App\Models\Conclusion;

use App\Models\Branch;
use App\Models\Worker;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conclusion extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'branch_id',
        'auditors',
        'detail',
        'savol_and_xodim',
        'worker_id',
        'responsible_worker',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'auditors' => 'array',
        'detail' => 'array',
        'savol_and_xodim' => 'array',
    ];


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function conclusionSubCategory()
    {
        return $this->belongsTo(ConclusionSubCategory::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
