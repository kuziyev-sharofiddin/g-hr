<?php

namespace App\Models\Conclusion;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditAttachBranchToTheCurator extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'branch_ids' => 'array',
      ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

}
