<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'title', 'meta_description', 'meta_keywords',
        'status', 'sections', 'last_edited_by',
    ];

    protected $casts = [
        'sections' => 'array',
    ];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getSection(string $key, mixed $default = null): mixed
    {
        return data_get($this->sections, $key, $default);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
