<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnketaWorkedBefore extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'company_name',
        'position',
        'anketa_id'
    ];

    public function anketa(){
        return $this->belongsTo(Anketa::class);
    }
}
