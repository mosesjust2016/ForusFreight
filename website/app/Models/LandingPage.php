<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'content', 'campaign_source', 'campaign_medium',
        'views', 'submissions', 'status',
    ];

    public function getConversionRateAttribute(): float
    {
        if ($this->views == 0) return 0;
        return round(($this->submissions / $this->views) * 100, 2);
    }
}
