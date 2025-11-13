<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVideoPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_id',
        'responsible_worker',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
