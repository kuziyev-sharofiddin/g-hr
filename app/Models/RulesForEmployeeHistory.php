<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RulesForEmployeeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_text',
        'responsible_worker',
        'rules_for_employee_id'
    ];

    public function rulesForEmployee(){
        return $this->belongsTo(RulesForEmployee::class);
    }
}
