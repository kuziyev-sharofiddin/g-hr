<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBirthdayGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guid'
    ];

    public function clientBithdayGroupWorker()
    {
        return $this->hasMany(ClientBirthdayGroupWorker::class);
    }

    public function clientBirthday()
    {
        return $this->hasMany(ClientBirthday::class, 'GUIDGroup', 'guid');
    }
}
