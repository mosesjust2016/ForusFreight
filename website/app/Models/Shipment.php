<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tracking_number', 'status', 'from', 'to', 'service', 'history'
    ];

    protected $casts = [
        'history' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
