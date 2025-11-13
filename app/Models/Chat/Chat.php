<?php

namespace App\Models\Chat;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['enable_chat_id', 'message','is_admin','parent_id','is_read'];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_read' => 'boolean',
    ];

    public function enableChat()
    {
        return $this->belongsTo(EnableChat::class);
    }

    public function replies()
    {
        return $this->hasMany(Chat::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Chat::class, 'parent_id');
    }
}
