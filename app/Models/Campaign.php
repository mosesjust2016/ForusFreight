<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'type', 'status', 'start_date', 'end_date',
        'budget', 'spent', 'leads_generated', 'conversions', 'target_audience',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function getRoiAttribute(): ?float
    {
        if (!$this->budget || $this->budget == 0) return null;
        return (($this->conversions * 100) / $this->budget); // simplified ROI metric
    }
}
