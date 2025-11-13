<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalControlTemplateDetail extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'internal_control_template_id',
        'internal_control_question_id',
        'index',
        'responsible_worker'
    ];

    protected $casts = [
        'responsible_worker' => 'array'
    ];

    public function internalControlTemplate()
    {
        return $this->belongsTo(InternalControlTemplate::class);
    }

    public function internalControlQuestion()
    {
        return $this->belongsTo(InternalControlQuestion::class);
    }
}
