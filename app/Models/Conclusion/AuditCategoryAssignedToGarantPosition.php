<?php

namespace App\Models\Conclusion;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditCategoryAssignedToGarantPosition extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'audit_category_ids' => 'array',
        'audit_sub_category_ids' => 'array'
      ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
