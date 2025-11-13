<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SorovnomaDetail extends Model
{
    use HasFactory;

     protected $fillable = [
         'sorovnoma_question_id',
         'sorovnoma_id',
         'mark',
         'variant',
         'description'
     ];

    public function sorovnoma()
    {
        return $this->belongsTo(Sorovnoma::class);
    }

    public function sorovnomaQuestion()
    {
        return $this->belongsTo(SorovnomaQuestion::class);
    }
}
