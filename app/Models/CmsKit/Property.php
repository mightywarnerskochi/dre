<?php

namespace App\Models\CmsKit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    protected $fillable = [
        'prop_id',
        'title',
        'slug',
        'property_type',
        'listing_type',
        'source_type',
        'price',
        'currency',
        'bedrooms',
        'bathrooms',
        'sqft',
        'address',
        'full_address',
        'zip_code',
        'city',
        'community',
        'country',
        'latitude',
        'longitude',
        'agent_id',
        'status',
        'order',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'status' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function details(): HasOne
    {
        return $this->hasOne(PropertyDetail::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('order')->orderBy('id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PropertyTranslation::class);
    }

    public function nearbyPlaces(): BelongsToMany
    {
        return $this->belongsToMany(NearbyPlace::class, 'property_nearby_places', 'property_id', 'place_id')
            ->withPivot(['distance', 'order'])
            ->orderByPivot('order');
    }

    public function featuredImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->where('is_featured', true)->orderBy('order');
    }

    public function getTranslation(string $field, ?string $lang = null): ?string
    {
        $lang = $lang ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get();

        $current = $translations->firstWhere('language_code', $lang);
        $fallbackTranslation = $translations->firstWhere('language_code', $fallback);

        if ($field === 'address') {
            return $current?->full_address
                ?? $current?->address
                ?? $fallbackTranslation?->full_address
                ?? $fallbackTranslation?->address
                ?? $this->full_address
                ?? $this->address;
        }

        return $current?->{$field}
            ?? $fallbackTranslation?->{$field}
            ?? match ($field) {
                'title' => $this->title,
                'description' => $this->details?->description,
                default => null,
            };
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->orderByDesc('published_at')->orderByDesc('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeSimilarListings(Builder $query, self $property): Builder
    {
        return $query
            ->where('property_type', $property->property_type)
            ->where('city', $property->city)
            ->where('id', '!=', $property->id);
    }
}
