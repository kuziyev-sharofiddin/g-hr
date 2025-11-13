<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutionClientCalled extends Model
{
    use HasFactory;

    protected $fillable = [
        'execution_client_id',
        'execution_client_history_id',
        'status',
        "guid_branch",
        'guid_employee',
        'guid_client',
        'guid_group',
        'tel',
        'images',
        'comment',
        'date',
        'responsible_worker',
        '1C_status'
    ];

    protected $casts = [
        'images' => 'array',
        'responsible_worker' => 'array'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'guid_branch', 'guid');
    }

    public function group()
    {
        return $this->belongsTo(ClientBirthdayGroup::class, 'guid_group', 'guid');
    }

    public function client()
    {
        return $this->belongsTo(ExecutionClient::class, 'execution_client_id', 'id');
    }

    public function history()
    {
        return $this->belongsTo(ExecutionClientHistory::class, 'execution_client_history_id', 'id');
    }
}
