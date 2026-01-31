<?php

namespace App\Models;

use App\Jobs\ProcessDocumentForRag;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'title',
        'file_path',
    ];

    protected static function booted()
    {
        static::created(function ($document) {
            ProcessDocumentForRag::dispatch($document);
        });
    }
}
