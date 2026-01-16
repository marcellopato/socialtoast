<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'path',
        'original_name',
        'mime_type',
        'status',
        'audit_result',
        'audit_reason',
    ];

    protected $casts = [
        'audit_result' => 'array',
    ];
}
