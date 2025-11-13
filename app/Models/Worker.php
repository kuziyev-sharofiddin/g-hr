<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends Model
{
    use  SoftDeletes;

    protected $fillable = [
        'state_id',
        'region_id',
        'branch_id',
        'section_id',
        'position_id',
        'pasport_type',
        'name',
        'birthday',
        'gender',
        'nationality',
        'passport_series',
        'from_whom_given',
        'when_given',
        'jshr_number',
        'inn_number',
        'inps_number',
        'address',
        'phone_number',
        'image',
        'passport_pdf',
        'status',
        'status_order_worker',
        'responsible_worker',
        'education',
        'marital_status',
        'language',
        'is_car',
        'use_car',
        'allowed_interview',
        'mib_checked',
        'guid',
        'anketa_id',
        'reserve_status',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
