<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalControlTemplate extends Model
{
    use HasFactory, Searchable;
    protected $fillable = [
        'name',
        'responsible_worker',
    ];

    protected $searchable = ['name'];

    protected $casts = [
        'responsible_worker' => 'array'
    ];

    public function internalControlTemplateDetail()
    {
        return $this->hasMany(InternalControlTemplateDetail::class, 'internal_control_template_id');
    }
}
