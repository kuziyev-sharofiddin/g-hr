<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssambledGoodsWorker extends Model
{
    use HasFactory;
    protected $table = 'assambled_goods_workers';
    protected $fillable = [
        'worker_id',
        'responsible_worker', // JSONB field for storing responsible worker data
    ];
    protected $casts = [
        'responsible_worker' => 'array', // Cast to array for JSONB field
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
