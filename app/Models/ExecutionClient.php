<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutionClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'ispolnetilniy',
        'name',
        'guid_client',
        'tel1',
        'tel2',
        'palce_of_work_id',
        'palce_of_work',
        'date_called',
        'status1',
        'status2',
        'comment1',
        'comment2',
        'guid_group',
        'guid_branch',
        'adress',
        'statu_work',
        'counterparty_type'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function group()
    {
        return $this->belongsTo(ClientBirthdayGroup::class, 'guid_group', 'guid');
    }

    public function history()
    {
        return $this->hasMany(ExecutionClientHistory::class);
    }

    /**
     * ✅ Eng oxirgi history is_call = false bo'lganlarni olish
     */
    public function scopeWithLatestUncalledHistory($query)
    {
        return $query->whereExists(function ($subQuery) {
            $subQuery->selectRaw('1')
                ->from('execution_client_histories')
                ->whereRaw('execution_client_histories.execution_client_id = execution_clients.id')
                ->whereRaw('execution_client_histories.is_call = false')
                ->whereRaw('execution_client_histories.created_at = (
                    SELECT MAX(created_at)
                    FROM execution_client_histories h2
                    WHERE h2.execution_client_id = execution_clients.id
                )');
        });
    }

    // Yoki ham qisqaroq:
    public function scopeLatestUncalledHistoryId($query)
    {
        return $query->addSelect([
            'latest_history_id' => function ($subQuery) {
                $subQuery->selectRaw('id')
                    ->from('execution_client_histories')
                    ->whereRaw('execution_client_histories.execution_client_id = execution_clients.id')
                    ->where('execution_client_histories.is_call', false)
                    ->orderByDesc('execution_client_histories.created_at')
                    ->limit(1);
            }
        ]);
    }

    /**
     * ✅ SUPER FAST - Raw SQL subquery
     *
     * Performance: ~0.2-0.3 second
     */
    public function scopeLatestUncalledHistoryFast($query)
    {
        return $query
            ->selectRaw('execution_clients.*')
            ->selectRaw(
                '(SELECT id FROM execution_client_histories
              WHERE execution_client_id = execution_clients.id
              AND is_call = false
              ORDER BY created_at DESC LIMIT 1) as latest_history_id'
            )
            ->whereRaw(
                'EXISTS (
                SELECT 1 FROM execution_client_histories
                WHERE execution_client_id = execution_clients.id
                AND is_call = false
            )'
            );
    }
}
