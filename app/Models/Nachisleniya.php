<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nachisleniya extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'branch_guid',
        'document_guid',
        'status',
        'date'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
