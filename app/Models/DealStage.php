<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DealStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'color', 'position', 'win_probability', 'is_closed', 'is_won',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'is_won' => 'boolean',
        'win_probability' => 'decimal:2',
    ];

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
