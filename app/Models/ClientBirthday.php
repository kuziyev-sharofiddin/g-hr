<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBirthday extends Model
{
    use HasFactory;

    protected $fillable = [
        'GUIDGroup',
        'GUIDBranch',
        'name',
        'GUIDClient',
        'tel1',
        'tel2',
        'palce_of_work',
        'birthday',

        'contract_guid',
        'contract',
        'contract_sum',
        'adress',
        'statu_work',
        'counterparty_type',
        'days_overdue',
        'debt',
        'sud',

        'status',
        'comment',
        'responsible_worker',
        'responsible_worker_guid',
        'sotrudnik',
        'GUIDsotrudnik',
        'congratulation_date',
        'call_images',
    ];

    // App\Models\ClientBirthday.php
    public function scopeWhereBirthdayBetweenDayMonth($query, $start, $end)
    {
        return $query->whereRaw("DATE_FORMAT(birthday, '%m-%d') BETWEEN ? AND ?", [
            $start->format('m-d'),
            $end->format('m-d')
        ]);
    }


    protected $casts = [
        'call_images' => 'array',
    ];

    public function clientBirthdayGroup()
    {
        return $this->belongsTo(ClientBirthdayGroup::class, 'GUIDGroup', 'guid');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'GUIDBranch', 'guid');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'responsible_worker_guid', 'guid');
    }
}
