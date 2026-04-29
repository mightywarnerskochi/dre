<?php

namespace App\Models\CmsKit;

use App\Support\MediaStorage;
use Database\Factories\CmsKit\PropertyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return PropertyFactory::new();
    }

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
        'postal_code',
        'city',
        'community',
        'country',
        'latitude',
        'longitude',
        'agent_id',
        'status',
        'order',
        'published_at',
        'images_directory',
        'images',
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

    public function getSanitizedPropIdAttribute(): string
    {
        // Replace everything that's not a letter, digit or hyphen with a hyphen
        $sanitized = preg_replace('/[^A-Za-z0-9\-]/', '-', $this->prop_id ?? '');

        return trim($sanitized, '-') ?: "PROP-{$this->id}";
    }

    public function getImagesAttribute(): array
    {
        if (! $this->images_directory || ! ($this->attributes['images'] ?? null)) {
            return [];
        }

        $suffixes = explode(',', $this->attributes['images'] ?? '');
        $images = [];
        $sanitizedPropId = $this->sanitized_prop_id;

        foreach ($suffixes as $suffix) {
            $index = trim($suffix);
            if ($index === '') {
                continue;
            }

            $primaryPath = "{$this->images_directory}/property_{$sanitizedPropId}_{$index}.jpg";
            $fallbackPath = "{$this->images_directory}/property_{$this->id}_{$index}.jpg";
            $resolvedPath = MediaStorage::exists($primaryPath)
                ? $primaryPath
                : (MediaStorage::exists($fallbackPath) ? $fallbackPath : $primaryPath);

            $images[] = (object) [
                'id' => $index,
                'image' => $resolvedPath,
                'order' => (int) $index,
                'is_featured' => $index == '1',
            ];
        }

        return $images;
    }

    public function getFeaturedImageAttribute(): ?object
    {
        $images = $this->images;

        return collect($images)->firstWhere('is_featured', true);
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
