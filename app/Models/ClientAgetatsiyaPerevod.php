<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAgetatsiyaPerevod extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_agetatsiya_id',
        'old_key_id',
        'new_key_id',
        'description',
        'responsible_worker'
    ];

    public function clientAgetatsiya(){
        return $this->belongsTo(ClientAgetatsiya::class);
    }

    public function oldKey(){
        return $this->belongsTo(KeyForAgetatsiyaEmployee::class, 'old_key_id');
    }

    public function newKey(){
        return $this->belongsTo(KeyForAgetatsiyaEmployee::class, 'new_key_id');
    }
}
