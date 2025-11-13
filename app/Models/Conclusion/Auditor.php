<?php /** @noinspection ALL */

namespace App\Models\Conclusion;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
