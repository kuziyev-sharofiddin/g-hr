<?php

namespace App\Models;

use App\Models\Conclusion\ConclusionCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditShablon extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'conclusion_sub_category_ids' => 'array',
        'auditors' => 'array',
    ];

    public function conclusionCategory()
    {
        return $this->belongsTo(ConclusionCategory::class, 'conclusion_category_id');
    }



}
