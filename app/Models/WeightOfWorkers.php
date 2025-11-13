<?php /** @noinspection ALL */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightOfWorkers extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'weight',
        'height',
        'created_at'
    ];

    public function workers()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

}
