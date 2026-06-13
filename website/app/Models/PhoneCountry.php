<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneCountry extends Model
{
    protected $fillable = ['name', 'dial_code', 'flag', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function activeDialCodes(): array
    {
        return static::where('is_active', true)->pluck('dial_code')->toArray();
    }

    public static function buildRegex(): string
    {
        $codes = implode('|', static::activeDialCodes());
        return '/^\+?(' . $codes . ')\d{7,12}$/';
    }
}
