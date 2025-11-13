<?php

namespace App\Models\Chat;

use App\Models\Chat\DeveloperEnableChat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeveloperChat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['developer_enable_chat_id', 'message','image','is_developer','read_developer_ids','parent_id','is_read'];

    protected $casts = [
        'is_developer' => 'boolean',
        'is_read' => 'boolean',
        'read_developer_ids' => 'array'
    ];

    public function developerEnableChat()
    {
        return $this->belongsTo(DeveloperEnableChat::class);
    }

    public function replies()
    {
        return $this->hasMany(DeveloperChat::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(DeveloperChat::class, 'parent_id');
    }
}
