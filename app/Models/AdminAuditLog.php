<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    protected $fillable = [
        'actor_id', 'action', 'subject_type', 'subject_id', 'metadata', 'ip_address',
    ];

    protected $casts = ['metadata' => 'array'];
}
