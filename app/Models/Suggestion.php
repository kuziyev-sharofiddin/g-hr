<?php

namespace App\Models;

use EloquentFilter\Filterable;
use App\Models\SuggestionMessage;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\SuggestionResource;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\QueryFilter\RelationWorker\WorkerName;
use App\Http\Resources\SuggestionReportAppResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Suggestion extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'worker_id',
        'suggestion_title_id',
        'type',
        'secretive',
        'whom',
        'status', // 1-ko'rilmagan, 2-yopilgan, 3-admin javob berdi, 4-user javob berdi
    ];

    public static function filterSearch($request)
    {
        $list = app(Pipeline::class)
            ->send(
                Suggestion::filter($request->all())
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return SuggestionResource::collection($list);
    }

    public static function filterSearchForReportApp($request)
    {
        $list = app(Pipeline::class)
            ->send(
                Suggestion::filter($request->all())
                    ->orderByRaw("FIELD(status, '1', '4', '3', '2') ASC")
                    // ->orderBy('created_at', 'DESC')
                    ->orderBy(
                        SuggestionMessage::select('created_at')
                            ->whereColumn('suggestions.id', 'suggestion_messages.suggestion_id')
                            ->latest()
                            ->take(1),
                        'DESC'
                    )
            )
            ->through([
                WorkerName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return SuggestionReportAppResource::collection($list);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function suggestionMessage()
    {
        return $this->hasMany(SuggestionMessage::class)->latest();
    }

    public function suggestionTitle()
    {
        return $this->belongsTo(SuggestionTitle::class);
    }
}
