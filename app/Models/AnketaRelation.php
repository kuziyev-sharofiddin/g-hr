<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnketaRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'who',
        'date',
        'job',
        'anketa_id'
    ];

    public function anketa(){
        return $this->belongsTo(Anketa::class);
    }
}
