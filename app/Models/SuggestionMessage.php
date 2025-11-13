<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuggestionMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'text',
        'images',
        'status',
        'read_status', // 1-o'qilmagan, 2-o'qilgan
        'suggestion_id'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function suggestion()
    {
        return $this->belongsTo(Suggestion::class);
    }

    public function evaluatedOffer()
    {
        return $this->belongsTo(EvaluatedOffer::class, 'suggestion_message_id');
    }
}
