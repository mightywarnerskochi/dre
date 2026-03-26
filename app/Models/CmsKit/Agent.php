<?php

namespace App\Models\CmsKit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'image',
        'image_alt',
        'designation',
        'experience',
        'description',
        'translations',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'translations' => 'array',
        'created_at' => 'datetime',
    ];

    public function getTranslation(string $attribute, ?string $lang = null): ?string
    {
        $lang = $lang ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        return $this->translations[$lang][$attribute]
            ?? $this->translations[$fallback][$attribute]
            ?? $this->{$attribute}
            ?? null;
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
