<?php

namespace App\Models;

use App\Models\Worker;
use App\Models\Section;
use App\Models\Shtatka;
use App\Models\SectionDetail;
use App\Models\ChangePosition;
use App\Models\DismissedWorker;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\PositionResource;
use App\QueryFilter\Position\PositionName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use  SoftDeletes;

    protected $fillable = [
        'name',
        'guid',
        'code',
        'does_it_belong_to_the_curator',
        'responsible_worker',
        'section_id'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
