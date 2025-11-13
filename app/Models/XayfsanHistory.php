<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XayfsanHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'responsible_worker',
        'xayfsan_id'
    ];

    public function xayfsan()
    {
        return $this->belongsTo(Xayfsan::class);
    }
}
