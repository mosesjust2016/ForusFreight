<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappCampaignRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'phone',
        'name',
        'variant',
        'status',
        'error_message',
        'sent_at',
        'external_id',
        'replied_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(WhatsappCampaign::class, 'campaign_id');
    }
}
