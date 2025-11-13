<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RulesSequenceNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_section',
        'json_sequence_number'
    ];
}
