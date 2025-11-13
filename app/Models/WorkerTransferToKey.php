<?php

namespace App\Models;

use App\Http\Resources\WorkerTransferKeyResource;
use App\QueryFilter\WorkerTransferKey\WorkerName;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkerTransferToKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_key_for_agetatsiya_employee_id',
        'new_key_for_agetatsiya_employee_id',
        'worker_id',
        'description',
        'responsible_worker'
    ];

    public function oldKeyForAgetatsiyaEmployee(){
        return $this->belongsTo(KeyForAgetatsiyaEmployee::class, 'old_key_for_agetatsiya_employee_id');
    }

    public function newKeyForAgetatsiyaEmployee(){
        return $this->belongsTo(KeyForAgetatsiyaEmployee::class, 'new_key_for_agetatsiya_employee_id');
    }

    public function worker(){
        return $this->belongsTo(Worker::class);
    }

    public static function search(){
        $data = app(Pipeline::class)
            ->send(
                    WorkerTransferToKey::query()
                )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->orderBy('id', 'desc')
            ->paginate(20);

        return WorkerTransferKeyResource::collection($data);
    }
}
