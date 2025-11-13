<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeApplicationMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'status',
        'read_status', // 1-o'qilmagan, 2-o'qilgan
        'application_id'
    ];

    public function employeeApplication(){
        return $this->belongsTo(EmployeeApplication::class, 'application_id');
    }
}
