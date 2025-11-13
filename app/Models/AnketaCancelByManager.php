<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnketaCancelByManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'responsible_manager',
        'anketa_id'
    ];

    public function anketa(){
        return $this->belongsTo(Anketa::class);
    }
}
