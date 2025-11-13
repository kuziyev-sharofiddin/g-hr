<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallCenterComplaintToAnswer extends Model
{
    use HasFactory;
    protected $table = 'call_center_complaint_to_answers';
    protected $fillable = [
        'status', // Status of the answer, e.g., 'completed', 'rejected'
        'call_center_complaint_id', // ID of the related complaint
        'answer_text', // Text of the answer
        'images', // JSONB for images related to the answer
        'worker_id', // ID of the worker who answered
        'responsible_worker', // JSONB for responsible worker details
        'my_reeds'
    ];
    protected $casts = [
        'my_reeds' => 'array',
        'images' => 'array', // Assuming images is an array of strings (file paths or URLs)
        'responsible_worker' => 'array', // Assuming responsible_worker is an array of details
    ];
    public function complaint()
    {
        return $this->belongsTo(CallCenterComplaint::class, 'call_center_complaint_id');
    }
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
