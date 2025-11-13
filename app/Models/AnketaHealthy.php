<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnketaHealthy extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'status',
        'description',
        'anketa_id' 
    ];

    public function anketa(){
        return $this->belongsTo(Anketa::class);
    }
}
