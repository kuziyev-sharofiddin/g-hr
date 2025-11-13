<?php /** @noinspection ALL */

namespace App\Models\Conclusion;

use App\Models\Branch;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttachCuratorBranchCategory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
      'branch_ids' => 'array',
      'conclusion_category_ids' => 'array'
    ];

    public function worker()
    {
      return $this->belongsTo(Worker::class);
    }

    public function branches()
    {
       return $this->belongsTo(Branch::class);
    }

    public function conclusionCategories()
    {
       return $this->belongsTo(ConclusionCategory::class);
    }
}
