<?php

namespace App\Models;

use App\Traits\Searchable;
use Exception;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KeyForAgetatsiyaEmployee extends Model
{
    use HasFactory, Filterable, Searchable;

    protected $fillable = [
        'name',
        'state_id',
        'region_id',
        'state_region_area_id',
        'branch_id',
        'is_empty_status',
        'description',
        'responsible_worker',
    ];

    protected $searchable = [
        'name',
        'keyAgetatsiyaWorkerHistory.worker.name'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function stateRegionArea()
    {
        return $this->belongsTo(StateRegionArea::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function keyAgetatsiyaWorkerHistory()
    {
        return $this->hasMany(KeyAgetatsiyaWorkerHistory::class, 'key_agetatsiya_employee_id');
    }

    public function clientAgetatsiya()
    {
        return $this->hasMany(ClientAgetatsiya::class);
    }

    public function workerTransferToKey()
    {
        return $this->hasMany(WorkerTransferToKey::class);
    }

    public function organizationNameForAgetatsiya(){
        return $this->hasMany(OrganizationNameForAgetatsiya::class);
    }


    public static function setKeyByChangeBranch($worker, $old_branch_id, $new_branch_id)
    {
        try {
            DB::transaction(function () use ($worker, $old_branch_id, $new_branch_id) {
                $key = KeyAgetatsiyaWorkerHistory::query()
                    ->whereHas('keyForAgetatsiyaEmployee', function ($query) use ($old_branch_id) {
                        $query->where('branch_id', $old_branch_id);
                    })
                    ->where('active_status', true)
                    ->where('worker_id', $worker->first()->id)
                    ->first();

                $key->update([
                    'end_date' => Carbon::now()->format('Y-m-d'),
                    'active_status' => false,
                ]);

                $keyForAgetatsiyaEmployee = KeyForAgetatsiyaEmployee::findOrFail($key->key_agetatsiya_employee_id);
                $keyForAgetatsiyaEmployee->update([
                    'is_empty_status' => true
                ]);

                self::createKey($worker, $new_branch_id);
            });
            return response()->json([
                'data' => "Ma'lumot saqlanmadi!",
            ], 200);
        } catch (Exception $e) {
            throw new \Exception($e);
            return response()->json([
                'data' => "Ma'lumot saqlanmadi qaytadan urunib ko'ring!",
            ], 500);
        }
    }

    public static function releaseKey($worker_id)
    {
        try {
            DB::transaction(function () use ($worker_id) {

                $key = KeyAgetatsiyaWorkerHistory::query()->where('active_status', true)->where('worker_id', $worker_id)->first();

                $key->update([
                    'end_date' => Carbon::now()->format('Y-m-d'),
                    'active_status' => false,
                ]);

                $keyForAgetatsiyaEmployee = KeyForAgetatsiyaEmployee::findOrFail($key->key_agetatsiya_employee_id);
                $keyForAgetatsiyaEmployee->update([
                    'is_empty_status' => true
                ]);
            });

            return response()->json([
                'data' => "Ma'lumot saqlanmadi!",
            ], 200);
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
            return response()->json([
                'data' => "Ma'lumot saqlanmadi qaytadan urunib ko'ring!",
            ], 500);
        }
    }

    public static function createKey($workers = [], $change_branch_id = 0)
    {
        try {
            DB::transaction(function () use ($workers, $change_branch_id) {
                foreach ($workers as $worker) {
                    $findEmptyKey = self::findEmptyKey($worker, $change_branch_id);
                    if ($findEmptyKey == null) {
                        $key = KeyForAgetatsiyaEmployee::create([
                            'name' => self::generateKeyName($worker, $change_branch_id),
                            'branch_id' => $change_branch_id == 0 ? $worker->branch_id : $change_branch_id,
                            'is_empty_status' => false,
                            'responsible_worker' => 'Api'
                        ]);

                        KeyAgetatsiyaWorkerHistory::create([
                            'worker_id' => $worker->id,
                            'start_date' => Carbon::now()->format('Y-m-d'),
                            'active_status' => true,
                            'key_agetatsiya_employee_id' => $key->id
                        ]);
                    } else {
                        $key = KeyForAgetatsiyaEmployee::findOrFail($findEmptyKey);
                        $key->update([
                            'is_empty_status' => false
                        ]);

                        KeyAgetatsiyaWorkerHistory::create([
                            'worker_id' => $worker->id,
                            'start_date' => Carbon::now()->format('Y-m-d'),
                            'active_status' => true,
                            'key_agetatsiya_employee_id' => $key->id
                        ]);
                    }
                }
            });

            return response()->json([
                'data' => "Ma'lumot saqlanmadi!",
            ], 200);
        } catch (Exception $e) {
            throw new \Exception($e);
            return response()->json([
                'data' => "Ma'lumot saqlanmadi qaytadan urunib ko'ring!",
            ], 500);
        }
    }

    public static function generateKeyName($worker, $change_branch_id = 0)
    {
        $count = KeyForAgetatsiyaEmployee::query()
            ->where('branch_id', $change_branch_id == 0 ? $worker->branch_id : $change_branch_id)
            ->count();

        $keyName = $worker->branch->name . '_' . $count + 1;

        return $keyName;
    }

    public static function findEmptyKey($worker, $change_branch_id = 0)
    {
        $findEmptyKey = KeyForAgetatsiyaEmployee::query()
            ->where('branch_id', $change_branch_id == 0 ? $worker->branch_id : $change_branch_id)
            ->where('is_empty_status', true)
            ->first()
            ->id ?? null;

        return $findEmptyKey;
    }

    public function clientAgetatsiyaPerevod()
    {
        return $this->hasMany(ClientAgetatsiyaPerevod::class);
    }
}
