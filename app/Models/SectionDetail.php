<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'position_id',
        'section_id'
    ];

    public function position(){
        return $this->belongsTo(Position::class);
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }
}
