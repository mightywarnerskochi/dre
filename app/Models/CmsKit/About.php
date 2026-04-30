<?php

namespace App\Models\CmsKit;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = [
        'image_1',
        'image_1_alt',
        'image_2',
        'image_2_alt',
        'image_3',
        'image_3_alt',
        'image_4',
        'image_4_alt',
        'home_image',
        'home_image_alt',
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
