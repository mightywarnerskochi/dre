<?php

namespace App\Models\CmsKit;

use Database\Factories\CmsKit\NearbyPlaceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NearbyPlace extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected static function newFactory()
    {
        return NearbyPlaceFactory::new();
    }

    protected $fillable = [
        'name',
        'type',
        'latitude',
        'longitude',
        'address',
        'translations',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'translations' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
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

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_nearby_places', 'place_id', 'property_id')
            ->withPivot(['distance', 'order']);
    }
}
