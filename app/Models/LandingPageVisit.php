<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageVisit extends Model
{
    protected $fillable = ['landing_page_id', 'source', 'medium', 'campaign', 'session_key', 'duration_seconds', 'bounced', 'cta_clicked', 'cta_type'];
    protected $casts = ['bounced' => 'boolean', 'cta_clicked' => 'boolean'];
}
