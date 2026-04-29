<?php

namespace App\Models\CmsKit;

use Illuminate\Database\Eloquent\Model;

class WhyChooseUs extends Model
{
    protected $table = 'why_choose_us_items';

    protected $fillable = [
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
