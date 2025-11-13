<?php

namespace App\Models\Conclusion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConclusionSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        "conclusion_category_id",
        "name",
        "responsible_worker",
        "tegishlilik",
        "description",
        "position_ids"
    ];

    protected $casts = [
        "position_ids" => 'array'
    ];

    public function conclusionCategory()
    {
        return $this->belongsTo(ConclusionCategory::class);
    }

    public function conclusion()
    {
        return $this->hasMany(Conclusion::class);
    }
}
