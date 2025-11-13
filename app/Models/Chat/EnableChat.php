<?php

namespace App\Models\Chat;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnableChat extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id','worker_id', 'is_closed'];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function latestChat()
    {
        return $this->hasOne(Chat::class)->latestOfMany();
    }
}
