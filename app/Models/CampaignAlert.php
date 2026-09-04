<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'type',
        'message',
        'data',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function campaign()
    {
        return $this->belongsTo(WhatsappCampaign::class, 'campaign_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
