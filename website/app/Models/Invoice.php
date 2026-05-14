<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id', 'shipment_id', 'invoice_number', 'amount', 'currency', 'status', 'due_date', 'paid_at'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    protected static function booted()
    {
        static::created(function ($invoice) {
            if ($invoice->amount >= 5000) {
                \App\Services\ObservabilityService::highValueTransaction(
                    'Invoice Generation',
                    $invoice->amount,
                    "Invoice #{$invoice->invoice_number} created for user ID: {$invoice->user_id}"
                );
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
