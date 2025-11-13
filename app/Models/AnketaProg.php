<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnketaProg extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'anketa_id'
    ];

    public function anketa(){
        return $this->belongsTo(Anketa::class);
    }
}
