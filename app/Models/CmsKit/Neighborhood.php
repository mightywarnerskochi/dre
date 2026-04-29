<?php

namespace App\Models\CmsKit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'translations',
        'latitude',
        'longitude',
        'order_index',
        'status',
    ];

    protected $casts = [
        'translations' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'status' => 'boolean',
    ];

    public function getTranslation(string $attribute, ?string $lang = null): ?string
    {
        $lang = $lang ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        return $this->translations[$lang][$attribute]
            ?? $this->translations[$fallback][$attribute]
            ?? null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
