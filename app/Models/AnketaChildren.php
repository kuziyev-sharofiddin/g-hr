<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnketaChildren extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'date',
        'anketa_id'
    ];

    public function anketa(){
        return $this->belongsTo(Anketa::class);
    }
}
