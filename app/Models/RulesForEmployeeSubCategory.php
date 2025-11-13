<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RulesForEmployeeSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'responsible_worker'
    ];

    public function rulesForEmployee()
    {
        return $this->hasMany(RulesForEmployee::class);
    }
}
