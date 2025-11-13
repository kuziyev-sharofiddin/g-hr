<?php

namespace App\Models;

use App\Http\Resources\SuggestionTitleResource;
use App\QueryFilter\SuggestionTitle\SuggestionTitleName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pipeline\Pipeline;

class SuggestionTitle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'responsible_worker'
    ];

    static function suggestionTitleSearch($request)
    {
        $data = app(Pipeline::class)
            ->send(
                SuggestionTitle::query()
            )
            ->through([
                SuggestionTitleName::class
            ])
            ->thenReturn()
            ->get();

        return SuggestionTitleResource::collection($data);
    }

    public function suggestion()
    {
        return $this->hasMany(Suggestion::class);
    }
}
