<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssambledGood extends Model
{
    use HasFactory;
    protected $table = 'assambled_goods';

    protected $fillable = [
        'status_1c', // true - yuklandi, false - yuklanmadi
        'date', // active, archived, deleted
        'hodim', // UUID of the worker
        'hodim_photo', // Photo of the worker
        'document', // Document file path or name
        'document_nomer', // Document number
        'document_guid', // UUID of the document
        'klient', // Client name
        'klient_guid', // UUID of the client
        'klient_nomer', // Client number
        'klient_address', // Client address
        'document_summa', // Total amount of the document
        'nomer_sud', // Court number
        'date_sud', // Date of the court
        'sud_organi', // Court organization
        'item', // JSONB for items
        'images', // JSONB for images
        'shartnoma_guid', // UUID of the contract
        'comment', // Additional comments
        'lat', // Location of the assembled goods
        'long', // Location of the assembled goods
        'responsible_worker', // JSONB for responsible worker details
    ];

    protected $casts = [
        'images' => 'array', // Assuming images is an array of strings (file paths or URLs)
        'item' => 'array',
        'responsible_worker' => 'array',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'hodim', 'guid');
    }
}
