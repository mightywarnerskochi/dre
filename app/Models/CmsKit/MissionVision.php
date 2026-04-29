<?php

namespace App\Models\CmsKit;

use Illuminate\Database\Eloquent\Model;

class MissionVision extends Model
{
    protected $fillable = [
        'image',
        'image_alt',
        'order_index',
        'status',
        'translations',
    ];

    protected $casts = [
        'status' => 'boolean',
        'translations' => 'array',
    ];

    public function getTranslation(string $attribute, ?string $lang = null): ?string
    {
        $lang = $lang ?? app()->getLocale();

        return $this->translations[$lang][$attribute]
            ?? $this->translations[config('app.fallback_locale')][$attribute]
            ?? null;
    }
}
