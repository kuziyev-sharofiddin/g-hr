<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalControlResponseDetail extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'internal_control_response_id',
        'internal_control_question_id',
        'rate',
        'description',
        'responsible_worker'
    ];

    protected $casts = [
        'responsible_worker' => 'array'
    ];

    public function internalControlResponse()
    {
        return $this->belongsTo(InternalControlResponse::class);
    }

    public function internalControlQuestion()
    {
        return $this->belongsTo(InternalControlQuestion::class);
    }
}
