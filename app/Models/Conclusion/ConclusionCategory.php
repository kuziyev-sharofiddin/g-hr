<?php

namespace App\Models\Conclusion;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConclusionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "responsible_worker"
    ];

    public function conclusionSubCategory()
    {
        return $this->hasMany(ConclusionSubCategory::class);
    }
}
