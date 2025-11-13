<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallCenterComplaint extends Model
{
    use HasFactory;

    protected $table = 'call_center_complaints';
    protected $fillable = [
        'type',
        'status',
        'client_name',
        'client_phone_one',
        'client_phone_two',
        'complaint_text',
        'rent_number',
        'branch_id',
        'images',
        'worker_id',
        'responsible_worker',
        'complated_at'
    ];
    protected $casts = [
        'images' => 'array', // Assuming images is an array of strings (file paths or URLs)
        'responsible_worker' => 'array', // Assuming responsible_worker is an array of details
        'complated_at' => 'datetime'
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class,);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class,);
    }

    public function callCenterComplaintToAnswer()
    {
        return $this->hasMany(CallCenterComplaintToAnswer::class, 'call_center_complaint_id');
    }
}
