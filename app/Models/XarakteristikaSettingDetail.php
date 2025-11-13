<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XarakteristikaSettingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'positions_json',
        'lifetime',
        'lifetime_hard',
        'xarakteristika_shablon_id',
        'xarakteristika_setting_id',
    ];

    public function xarakteristikaShablon()
    {
        return $this->belongsTo(XarakteristikaShablon::class);
    }

    public function xarakteristikaSetting()
    {
        return $this->belongsTo(XarakteristikaSetting::class);
    }
}
