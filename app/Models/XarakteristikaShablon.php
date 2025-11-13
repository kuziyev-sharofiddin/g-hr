<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XarakteristikaShablon extends Model
{
    use HasFactory;

    protected $fillable = [
        'shablon_name',
        'shablon_title_json',
        'responsible_worker'
    ];

    public function xarakteristikaSettingDetail()
    {
        return $this->hasMany(XarakteristikaSettingDetail::class);
    }
}
