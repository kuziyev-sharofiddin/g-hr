<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SorovnomaQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'description_status',
        'type',
        'position_ids',
        'responsible_worker'
    ];


    public function sorovnoma()
    {
        return $this->belongsTo(Sorovnoma::class);
    }

    protected $casts = [
        'position_ids' => 'array',
    ];
}
