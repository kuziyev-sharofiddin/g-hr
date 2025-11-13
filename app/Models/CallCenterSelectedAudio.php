<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallCenterSelectedAudio extends Model
{
    use HasFactory;

    protected $table = 'call_center_selected_audio';

    protected $fillable = [
        'uniqueid',
        'disposition',
        'out_and_in',
        'description',
        'responsible_worker',
    ];

    protected $casts = [
        'responsible_worker' => 'array',
    ];
}
