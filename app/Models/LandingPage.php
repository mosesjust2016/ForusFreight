<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'template', 'content', 'campaign_source', 'campaign_medium',
        'views', 'submissions', 'status',
        'hero_title', 'hero_text', 'hero_image_url', 'cta_label', 'cta_url',
        'form_title', 'form_description', 'primary_color', 'accent_color', 'show_form', 'form_fields', 'gallery_images',
    ];

    protected $casts = ['show_form' => 'boolean', 'form_fields' => 'array', 'gallery_images' => 'array'];

    public function getConversionRateAttribute(): float
    {
        if ($this->views == 0) return 0;
        return round(($this->submissions / $this->views) * 100, 2);
    }
}
