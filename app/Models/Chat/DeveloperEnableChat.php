<?php

namespace App\Models\Chat;

use App\Models\Chat\DeveloperChat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperEnableChat extends Model
{
    use HasFactory;
    protected $fillable = ['developer_id','user_id', 'is_closed'];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
    public function developerChats()
    {
        return $this->hasMany(DeveloperChat::class);
    }

    public function latestChat()
    {
        return $this->hasOne(DeveloperChat::class)->latestOfMany();
    }
}
