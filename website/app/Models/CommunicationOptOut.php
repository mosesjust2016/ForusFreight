<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationOptOut extends Model
{
    use HasFactory;

    protected $fillable = ['phone', 'channel', 'user_id', 'reason', 'opted_out_at'];

    protected $casts = [
        'opted_out_at' => 'datetime',
    ];
}
