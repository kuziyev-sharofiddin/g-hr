<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBirthdayGroupWorker extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_name',
        'worker_guid',
        'client_birthday_group_id',
    ];

    public function clientBirthdayGroup()
    {
        return $this->belongsTo(ClientBirthdayGroup::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_guid', 'guid');
    }

}
