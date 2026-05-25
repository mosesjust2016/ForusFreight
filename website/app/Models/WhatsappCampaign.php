<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'message',
        'total_recipients',
        'sent_count',
        'failed_count',
        'opted_out_count',
        'status',
        'delay_min',
        'delay_max',
        'daily_limit',
        'started_at',
        'completed_at',
        'scheduled_at',
        'is_ab_test',
        'variant_a_message',
        'variant_b_message',
        'split_percent',
        'variant_a_sent',
        'variant_b_sent',
        'alert_threshold',
        'auto_send_winner',
        'winner_decision_min_replies',
        'winner_decision_delay_hours',
        'winner_variant',
        'winner_declared_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'winner_declared_at' => 'datetime',
        'is_ab_test' => 'boolean',
        'auto_send_winner' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipients()
    {
        return $this->hasMany(WhatsappCampaignRecipient::class, 'campaign_id');
    }

    public function alerts()
    {
        return $this->hasMany(CampaignAlert::class, 'campaign_id');
    }

    public function pendingRecipients()
    {
        return $this->hasMany(WhatsappCampaignRecipient::class, 'campaign_id')->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['queued', 'sending']);
    }

    public function progressPercent(): int
    {
        if ($this->total_recipients === 0) return 0;
        return (int) round((($this->sent_count + $this->failed_count + $this->opted_out_count) / $this->total_recipients) * 100);
    }

    public function isComplete(): bool
    {
        return in_array($this->status, ['completed', 'cancelled']);
    }
}
