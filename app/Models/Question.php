<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\QuestionResource;
use App\QueryFilter\Question\QuestionName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'category',
        'question',
        'answer',
        'option_1',
        'option_2',
        'option_3',
        'option_4',
        'responsible_worker'
    ];

    public static function questionFilterSearch($request){
        $question = app(Pipeline::class)
            ->send(
                    Question::filter($request->all())
                )
            ->through([
                QuestionName::class,
            ])
            ->thenReturn()
            ->latest()
            ->paginate(20);

        return QuestionResource::collection($question);
    }
}
