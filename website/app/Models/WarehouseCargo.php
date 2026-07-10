<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseCargo extends Model
{
    use HasFactory;

    protected $table = 'warehouse_cargo';
    
    protected $fillable = [
        'inventory_number',
        'warehouse_entry_number',
        'entry_date',
        'customer_code',
        'user_id',
        'receiver_name',
        'receiver_phone',
        'cargo_name_chinese',
        'cargo_name_english',
        'cartons',
        'gross_weight',
        'volume',
        'driver_info',
        'tracking_number',
        'status',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'gross_weight' => 'decimal:2',
        'volume' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
