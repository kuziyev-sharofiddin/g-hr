<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluatedOffer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function suggestion()
    {
        return $this->belongsTo(Suggestion::class);
    }

    public function suggestionMessage()
    {
        return $this->belongsTo(SuggestionMessage::class);
    }
}
