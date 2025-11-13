<?php

namespace App\Models\Chat;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatForDeveloper extends Model
{
    use HasFactory;

    protected $fillable = ['worker_id', 'responsible_worker'];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

}
