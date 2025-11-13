<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutionClientHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'execution_client_id',
        'date',
        'branch_id',
        'ispolnitelniy',
        'group_guid',
        'is_call'
    ];

    public function executionClient()
    {
        return $this->belongsTo(ExecutionClient::class);
    }

    public function group()
    {
        return $this->belongsTo(ClientBirthdayGroup::class, 'group_guid', 'guid');
    }

    /**
     * ✅ Har bir execution_client_id uchun eng oxirgi record olish
     *
     * Misol: Agar bir client uchun bir oraliq'da 3 ta record bo'lsa,
     * faqat eng oxirgisini oladi (date bo'yicha)
     */
    public function scopeLatestByClient($query)
    {
        return $query->whereIn('id', function ($subQuery) {
            $subQuery->selectRaw('MAX(id)')
                ->from('execution_client_histories')
                ->groupBy('execution_client_id');
        });
    }

    public function called()
    {
        return $this->hasMany(ExecutionClientCalled::class);
    }
}
