<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'service_type', 'full_name', 'company', 'email', 'phone', 'pickup',
        'delivery', 'weight', 'dimensions', 'details', 'status', 'admin_notes',
        'assigned_to', 'contacted_at',
    ];

    protected $casts = ['contacted_at' => 'datetime', 'weight' => 'decimal:2'];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
