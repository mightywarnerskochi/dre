<?php

namespace App\Http\Controllers;

use App\Models\CmsKit\Banner;
use App\Models\CmsKit\Property;
use App\Models\CmsKit\SiteInformation;
use App\Support\PublicSiteViewData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PropertyPageController extends Controller
{
    /**
     * Show the property listing page.
     */
    public function index(): View
    {
        $siteInfo = $this->getSiteInfo();

        return view('properties.listing', [
            'pageData' => array_merge($this->sharedShellPageData($siteInfo), [
                'hero' => [
                    'title' => 'Our Properties',
                    'backgroundImage' => $this->listingHeroBackground(),
                ],
                'search' => $this->listingSearchFilters(),
            ]),
        ]);
    }

    /**
     * Show the property detail page.
     */
    public function show(string $slug): View
    {
        $property = Property::query()
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $siteInfo = $this->getSiteInfo();

        return view('properties.detail', [
            'propertyId' => $property->id,
            'pageData' => array_merge($this->sharedShellPageData($siteInfo), [
                'hero' => [
                    'title' => $property->getTranslation('title') ?: $property->title,
                    'backgroundImage' => $this->propertyHeroBackground($property),
                ],
            ]),
        ]);
    }

    /**
     * Site header/footer/social payload aligned with home Vue components.
     *
     * @return array<string, mixed>
     */
    protected function sharedShellPageData(?SiteInformation $siteInfo): array
    {
        return [
            'site' => $this->siteData($siteInfo),
            'header' => $this->headerData($siteInfo),
            'social' => $this->socialData($siteInfo),
            'footer' => $this->footerData($siteInfo),
        ];
    }

    /**
     * Apply the same filters as the public listing / map search.
     *
     * @param  Builder<Property>  $query
     */
    protected function applyPublishedPropertyFilters(Request $request, $query): void
    {
        if ($request->filled('location')) {
            $location = $request->query('location');
            $query->where(function ($q) use ($location) {
                $q->where('city', 'like', "%{$location}%")
                    ->orWhere('community', 'like', "%{$location}%")
                    ->orWhere('address', 'like', "%{$location}%")
                    ->orWhere('title', 'like', "%{$location}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('property_type', $request->query('type'));
        }

        if ($request->filled('listing_type')) {
            $query->where('listing_type', (string) $request->query('listing_type'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->filled('beds')) {
            $query->where('bedrooms', '>=', (int) $request->query('beds'));
        }

        if ($request->filled('baths')) {
            $query->where('bathrooms', '>=', (int) $request->query('baths'));
        }

        $priceMin = $request->query('price_min', $request->query('min_price'));
        $priceMax = $request->query('price_max', $request->query('max_price'));
        if (filled($priceMin) && is_numeric($priceMin)) {
            $query->where('price', '>=', (float) $priceMin);
        }

        if (filled($priceMax) && is_numeric($priceMax)) {
            $query->where('price', '<=', (float) $priceMax);
        }

        if ($request->filled('price')) {
            $this->applyPriceRangeFilter($query, (string) $request->query('price'));
        }
    }

    /**
     * API: List properties with filters and pagination.
     */
    public function apiList(Request $request): JsonResponse
    {
        $this->setRequestLocale($request);

        $query = Property::query()
            ->with(['agent', 'translations', 'details'])
            ->where('status', true);

        $this->applyPublishedPropertyFilters($request, $query);
        $this->applyPublishedPropertySort($request, $query);

        $perPage = (int) $request->query('per_page', 6);
        $perPage = max(1, min(48, $perPage));

        $properties = $query->paginate($perPage)->withQueryString();

        $siteInfo = $this->getSiteInfo();
        $properties->getCollection()->transform(function ($property) use ($siteInfo) {
            /** @var Property $property */
            return $this->formatProperty($property, false, $siteInfo);
        });

        return response()->json($properties);
    }

    /**
     * @param  Builder<Property>  $query
     */
    protected function applyPublishedPropertySort(Request $request, $query): void
    {
        match ((string) $request->query('sort', 'newest')) {
            'price_asc' => $query->orderBy('price')->orderByDesc('published_at')->orderByDesc('id'),
            'price_desc' => $query->orderByDesc('price')->orderByDesc('published_at')->orderByDesc('id'),
            'area_desc' => $query->orderByDesc('sqft')->orderByDesc('published_at')->orderByDesc('id'),
            default => $query->ordered(),
        };
    }

    /**
     * API: Single property detail.
     */
    public function apiDetail(int $id): JsonResponse
    {
        $this->setRequestLocale(request());

        $property = Property::query()
            ->with(['agent', 'details', 'translations', 'nearbyPlaces'])
            ->where('status', true)
            ->findOrFail($id);

        return response()->json($this->propertyDetailPayload($property));
    }

    /**
     * API: Property detail resolved by public slug (SPA route).
     */
    public function apiDetailBySlug(string $slug): JsonResponse
    {
        $this->setRequestLocale(request());

        $property = Property::query()
            ->with(['agent', 'details', 'translations', 'nearbyPlaces'])
            ->where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return response()->json($this->propertyDetailPayload($property));
    }

    /**
     * API: Map markers for all published properties with coordinates.
     *
     * @return array{markers: array<int, array<string, mixed>>}
     */
    public function apiMapMarkers(Request $request): JsonResponse
    {
        $this->setRequestLocale($request);

        $siteInfo = $this->getSiteInfo();
        $query = Property::query()
            ->with(['agent', 'translations', 'details'])
            ->where('status', true)
            ->ordered();

        $this->applyPublishedPropertyFilters($request, $query);
        $this->applyMapBoundsFilter($request, $query);

        $markers = $query
            ->get()
            ->filter(function (Property $property) {
                $lat = (float) $property->latitude;
                $lng = (float) $property->longitude;

                return abs($lat) > 1e-7 || abs($lng) > 1e-7;
            })
            ->map(function (Property $property) use ($siteInfo) {
                $row = $this->formatProperty($property, false, $siteInfo);
                $row['latitude'] = (float) $property->latitude;
                $row['longitude'] = (float) $property->longitude;

                return $row;
            })
            ->values();

        return response()->json(['markers' => $markers]);
    }

    /**
     * Restrict map markers to current map viewport when bounds are provided.
     *
     * @param  Builder<Property>  $query
     */
    protected function applyMapBoundsFilter(Request $request, $query): void
    {
        $latMin = $request->query('lat_min');
        $latMax = $request->query('lat_max');
        $lngMin = $request->query('lng_min');
        $lngMax = $request->query('lng_max');

        if (! is_numeric($latMin) || ! is_numeric($latMax) || ! is_numeric($lngMin) || ! is_numeric($lngMax)) {
            return;
        }

        $south = min((float) $latMin, (float) $latMax);
        $north = max((float) $latMin, (float) $latMax);
        $west = min((float) $lngMin, (float) $lngMax);
        $east = max((float) $lngMin, (float) $lngMax);

        $query->whereBetween('latitude', [$south, $north]);
        $query->whereBetween('longitude', [$west, $east]);
    }

    /**
     * API: Distinct property_type / category values from published properties with CMS labels.
     *
     * @return array{property_types: array<int, array{value: string, label: string}>, categories: array<int, array{value: string, label: string}>}
     */
    public function apiFilterOptions(Request $request): JsonResponse
    {
        $this->setRequestLocale($request);
        $locale = app()->getLocale();

        $filters = [];
        $definitions = \App\Models\CmsKit\HomeBannerFilterDefinition::query()
            ->with(['values' => fn ($q) => $q->where('status', true)->orderBy('sort_order')])
            ->where('status', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($definitions as $definition) {
            $options = $definition->values->map(function ($val) use ($locale) {
                $translated = $val->translations[$locale] ?? [];
                $label = data_get($translated, 'label') ?? $val->label;

                return [
                    'value' => $val->value,
                    'label' => $label,
                    'subtitle' => data_get($translated, 'subtitle', ''),
                    'type' => data_get($translated, 'type', ''),
                ];
            })->values()->all();

            if ($definition->key === 'location') {
                $options = collect($options)
                    ->filter(function (array $option) {
                        $type = Str::lower((string) ($option['type'] ?? ''));

                        return in_array($type, ['city', 'community'], true);
                    })
                    ->values()
                    ->all();
            }

            if ($definition->key === 'property_type') {
                $filters['property_types'] = $options;
            } elseif ($definition->key === 'category') {
                $filters['categories'] = $options;
            } elseif ($definition->key === 'location') {
                $filters['locations'] = $options;
            }
        }

        $appendUsedOptions = function (array $options, string $column, array $config): array {
            $seen = collect($options)
                ->mapWithKeys(fn ($option) => [Str::lower(trim((string) ($option['value'] ?? ''))) => true])
                ->all();

            $usedValues = Property::query()
                ->select([$column, 'metadata'])
                ->where('status', true)
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->orderBy($column)
                ->get();

            foreach ($usedValues as $property) {
                $value = trim((string) $property->{$column});
                $key = Str::lower($value);

                if ($value === '' || isset($seen[$key])) {
                    continue;
                }

                $options[] = [
                    'value' => $value,
                    'label' => $this->propertyClassificationLabel($property, $column, $config) ?: $value,
                ];
                $seen[$key] = true;
            }

            return $options;
        };

        if (isset($filters['property_types'])) {
            $filters['property_types'] = $appendUsedOptions(
                $filters['property_types'],
                'property_type',
                config('cms-kit.database.properties.property_types', [])
            );
        }

        if (isset($filters['categories'])) {
            $filters['categories'] = $appendUsedOptions(
                $filters['categories'],
                'category',
                config('cms-kit.database.properties.categories', [])
            );
        }

        // Fallback for property_types if not configured
        if (! isset($filters['property_types'])) {
            $typeConfig = config('cms-kit.database.properties.property_types', []);
            $typesUsed = Property::query()
                ->where('status', true)
                ->whereNotNull('property_type')
                ->where('property_type', '!=', '')
                ->distinct()
                ->orderBy('property_type')
                ->pluck('property_type');

            if ($typesUsed->isEmpty()) {
                $typesUsed = collect(array_keys($typeConfig));
            }

            $filters['property_types'] = $typesUsed
                ->map(function (string $key) use ($typeConfig) {
                    $key = trim($key);
                    return [
                        'value' => $key,
                        'label' => $this->optionLabel($typeConfig, $key) ?: $key,
                    ];
                })
                ->values();
        }

        // Fallback for categories if not configured
        if (! isset($filters['categories'])) {
            $catConfig = config('cms-kit.database.properties.categories', []);
            $categoriesUsed = Property::query()
                ->where('status', true)
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category');

            if ($categoriesUsed->isEmpty()) {
                $categoriesUsed = collect(array_keys($catConfig));
            }

            $filters['categories'] = $categoriesUsed
                ->map(function (string $key) use ($catConfig) {
                    $key = trim($key);
                    return [
                        'value' => $key,
                        'label' => $this->optionLabel($catConfig, $key) ?: $key,
                    ];
                })
                ->values();
        }

        {
            $locations = collect($filters['locations'] ?? []);
            $seen = $locations
                ->mapWithKeys(fn ($location) => [Str::lower((string) ($location['value'] ?? '')) => true])
                ->all();
            $fallbackCountry = 'United Arab Emirates';

            $addLocation = function (?string $value, ?string $label, string $type, ?string $subtitle = '') use (&$seen, $locations) {
                $value = trim((string) $value);
                $label = trim((string) ($label ?: $value));
                if ($value === '' || $label === '') {
                    return;
                }

                $key = Str::lower($value);
                if (isset($seen[$key])) {
                    return;
                }

                $seen[$key] = true;
                $locations->push([
                    'value' => $value,
                    'label' => $label,
                    'subtitle' => trim((string) $subtitle),
                    'type' => $type,
                ]);
            };

            Property::query()
                ->where('status', true)
                ->with('translations')
                ->orderByDesc('is_featured')
                ->ordered()
                ->take(200)
                ->get()
                ->each(function (Property $property) use ($locale, $fallbackCountry, $addLocation) {
                    $community = $property->getTranslation('community', $locale) ?: $property->community;
                    $city = $property->getTranslation('city', $locale) ?: $property->city;
                    $country = $property->getTranslation('country', $locale) ?: $property->country ?: $fallbackCountry;

                    $addLocation($property->community, $community, 'Community', collect([$city, $country])->filter()->implode(', '));
                    $addLocation($property->city, $city, 'City', $country);
                });

            $filters['locations'] = $locations->values()->all();
        }

        return response()->json($filters);
    }

    /**
     * @return array{property: array<string, mixed>, similar: Collection}
     */
    protected function propertyDetailPayload(Property $property): array
    {
        $siteInfo = $this->getSiteInfo();
        $formatted = $this->formatProperty($property, true, $siteInfo);

        $similarListings = Property::query()
            ->where('id', '!=', $property->id)
            ->where('status', true)
            ->where('property_type', $property->property_type)
            ->ordered()
            ->take(4)
            ->get()
            ->map(function ($p) use ($siteInfo) {
                /** @var Property $p */
                return $this->formatProperty($p, false, $siteInfo);
            });

        return [
            'property' => $formatted,
            'similar' => $similarListings,
        ];
    }

    protected function formatProperty(Property $property, bool $full = false, ?SiteInformation $siteInfo = null): array
    {
        $siteInfo = $siteInfo ?? $this->getSiteInfo();

        $images = collect($property->images ?? [])
            ->map(function ($img) {
                $path = '';
                if (is_string($img)) {
                    $path = trim($img);
                } elseif (is_array($img)) {
                    $path = trim((string) ($img['image'] ?? $img['url'] ?? $img['src'] ?? $img['path'] ?? ''));
                } elseif (is_object($img)) {
                    $path = trim((string) ($img->image ?? $img->url ?? $img->src ?? $img->path ?? ''));
                }

                if ($path === '') {
                    return null;
                }

                if (Str::startsWith($path, ['http://', 'https://'])) {
                    return $path;
                }

                return media_url($path);
            })
            ->filter(fn ($url) => filled($url))
            ->unique()
            ->values();

        if ($images->isEmpty()) {
            $images = collect([dre_property_placeholder_image()]);
        }

        $title = $property->getTranslation('title') ?: $property->title ?: 'Property';
        $location = $this->propertyLocationForLocale($property, app()->getLocale());
        $fullAddress = $this->propertyFullAddressForLocale($property, app()->getLocale());

        $agentPhone = trim((string) ($property->agent?->phone ?? $siteInfo?->phone_1 ?? ''));
        $whatsApp = trim((string) ($property->agent?->whatsapp_number ?? $siteInfo?->whatsapp_number ?? ''));
        $email = trim((string) ($siteInfo?->email_1 ?? 'hello@distinguishedre.ae'));
        $whatsAppHref = $whatsApp !== '' ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsApp) : '#';

        $slug = trim((string) ($property->slug ?? ''));
        $detailPath = $slug !== '' ? '/property-details/'.$slug : '/our-property';
        $descriptionText = $property->getTranslation('description') ?: ($property->details?->description ?? '');
        $seoPayload = $this->propertySeoPayload(
            $property,
            $title,
            $detailPath,
            (string) ($images->first() ?? ''),
            $descriptionText
        );

        $data = [
            'id' => $property->id,
            'prop_id' => $property->prop_id,
            'title' => $title,
            'slug' => $property->slug,
            'url' => $detailPath,
            'price' => (float) $property->price,
            'currency' => $property->currency ?: 'AED',
            'location' => $location,
            'address' => trim((string) ($property->getTranslation('address', app()->getLocale()) ?: $property->address)),
            'full_address' => $fullAddress,
            'latitude' => (float) $property->latitude,
            'longitude' => (float) $property->longitude,
            'bedrooms' => (int) $property->bedrooms,
            'bathrooms' => (int) $property->bathrooms,
            'beds' => (int) $property->bedrooms,
            'baths' => (int) $property->bathrooms,
            'sqft' => (int) $property->sqft,
            'period' => $property->listing_type === 'rent' ? ' / yr' : '',
            'property_type' => $property->property_type,
            'propertyTypeLabel' => $this->propertyClassificationLabel($property, 'property_type', config('cms-kit.database.properties.property_types', [])),
            'listing_type' => $property->listing_type,
            'listingTypeLabel' => $this->optionLabel(config('cms-kit.database.properties.listing_types', []), (string) $property->listing_type),
            'category' => $property->category,
            'categoryLabel' => $this->propertyClassificationLabel($property, 'category', config('cms-kit.database.properties.categories', [])),
            'images' => $images->all(),
            'image_count' => $images->count(),
            'featured_image' => $images->first(),
            'virtual_tour_url' => trim((string) ($property->details?->virtual_tour_url ?? '')) ?: null,
            'isFeatured' => (bool) $property->is_featured,
            'is_featured' => (bool) $property->is_featured,
            'phone' => $agentPhone !== '' ? 'tel:'.preg_replace('/\s+/', '', $agentPhone) : '#contact',
            'whatsapp' => $whatsAppHref,
            'inquireUrl' => 'mailto:'.rawurlencode($email).'?subject='.rawurlencode('Inquiry - '.$title),
            'agent' => $property->agent ? [
                'name' => $property->agent->getTranslation('name'),
                'designation' => $property->agent->getTranslation('designation'),
                'experience' => $property->agent->getTranslation('experience'),
                'languages' => $property->agent->getTranslation('languages'),
                'image' => $property->agent->image ? media_url((string) $property->agent->image) : asset('images/dre/agent-placeholder.svg'),
                'imageAlt' => trim((string) ($property->agent->name ?? '')) ?: 'Property agent',
                'phone' => $property->agent->phone ? 'tel:'.preg_replace('/\s+/', '', (string) $property->agent->phone) : '#',
                'whatsapp' => $whatsAppHref,
            ] : null,
            'seo' => $seoPayload,
        ];

        if ($full) {
            $data['description'] = $property->getTranslation('description') ?: $property->details?->description;
            $data['year_built'] = $property->details?->year_built;
            $deposit = $property->details?->security_deposit;
            $data['security_deposit'] = $deposit !== null && $deposit !== '' ? (float) $deposit : null;
            $data['direct_from_owner'] = trim((string) ($property->details?->direct_from_owner ?? '')) ?: null;
            $data['details_grid'] = $this->localizedPropertySectionRows($property, 'property_attributes', 'name');
            $data['amenities'] = $this->localizedPropertySectionRows($property, 'amenities', 'name');
            $data['features'] = $property->details?->key_features ?: [];

            $cmsEasyCards = collect($this->localizedPropertySectionRows($property, 'easy_to_access', 'label'))
                ->map(function ($row) {
                    $label = trim((string) ($row['label'] ?? ''));
                    if ($label === '') {
                        return null;
                    }

                    return [
                        'name' => $label,
                        'distance' => null,
                        'icon' => $row['icon'] ?? null,
                    ];
                })
                ->filter()
                ->values();

            $nearbyCards = $property->nearbyPlaces
                ? $property->nearbyPlaces->map(function ($np) {
                    $dist = $np->pivot->distance;
                    $distLabel = 'Nearby';
                    if ($dist !== null && $dist !== '') {
                        $distLabel = is_numeric($dist)
                            ? trim((string) $dist).' km away'
                            : trim((string) $dist);
                    }

                    return [
                        'name' => $np->getTranslation('name') ?: $np->name,
                        'distance' => $distLabel,
                        'icon' => $np->icon ? media_url((string) $np->icon) : null,
                    ];
                })->values()
                : collect();

            $data['easy_access'] = $cmsEasyCards->isNotEmpty()
                ? $cmsEasyCards->all()
                : $nearbyCards->all();
            $data['easy_access_from_nearby'] = $cmsEasyCards->isEmpty() && $nearbyCards->isNotEmpty();

            $data['nearby_places'] = $property->nearbyPlaces
                ? $property->nearbyPlaces->map(function ($np) {
                    return [
                        'id' => (int) $np->id,
                        'name' => $np->getTranslation('name') ?: $np->name,
                        'type' => strtolower((string) ($np->type ?? '')),
                        'latitude' => $np->latitude !== null && $np->latitude !== '' ? (float) $np->latitude : null,
                        'longitude' => $np->longitude !== null && $np->longitude !== '' ? (float) $np->longitude : null,
                        'distance' => $np->pivot->distance,
                        'icon' => $np->icon ? media_url((string) $np->icon) : null,
                        'address' => trim((string) ($np->getTranslation('address') ?: ($np->address ?? ''))),
                    ];
                })->values()->all()
                : [];
            $data['latitude'] = (float) $property->latitude;
            $data['longitude'] = (float) $property->longitude;
        }

        return $data;
    }

    protected function localizedPropertySectionRows(Property $property, string $section, string $textField): array
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        $translations = $property->relationLoaded('translations')
            ? $property->translations
            : $property->translations()->get();

        $localeRows = (array) ($translations->firstWhere('language_code', $locale)?->{$section} ?? []);
        $fallbackRows = $this->firstSectionRowsWithContent([
            (array) ($translations->firstWhere('language_code', $fallbackLocale)?->{$section} ?? []),
            (array) ($property->details?->{$section} ?? []),
        ], $textField);

        if ($locale === $fallbackLocale) {
            return $this->normalizedDisplaySectionRows($fallbackRows ?: $localeRows, $textField);
        }

        $rowCount = max(count($localeRows), count($fallbackRows));
        $mergedRows = [];

        for ($index = 0; $index < $rowCount; $index++) {
            $localeRow = is_array($localeRows[$index] ?? null) ? $localeRows[$index] : [];
            $fallbackRow = is_array($fallbackRows[$index] ?? null) ? $fallbackRows[$index] : [];
            $localeText = $this->sectionRowText($localeRow, $textField);
            $fallbackText = $this->sectionRowText($fallbackRow, $textField);
            $icon = trim((string) ($localeRow['icon'] ?? $fallbackRow['icon'] ?? ''));

            $mergedRows[] = [
                $textField => $localeText !== '' ? $localeText : $fallbackText,
                'icon' => $icon,
            ];
        }

        return $this->normalizedDisplaySectionRows($mergedRows, $textField);
    }

    protected function firstSectionRowsWithContent(array $candidates, string $textField): array
    {
        foreach ($candidates as $rows) {
            $normalized = $this->normalizedDisplaySectionRows(is_array($rows) ? $rows : [], $textField);
            if ($normalized !== []) {
                return is_array($rows) ? array_values($rows) : [];
            }
        }

        return [];
    }

    protected function sectionRowText(array $row, string $textField): string
    {
        return trim((string) ($row[$textField] ?? $row['label'] ?? $row['name'] ?? $row['title'] ?? $row['value'] ?? ''));
    }

    protected function normalizedDisplaySectionRows(array $rows, string $textField): array
    {
        return collect($rows)
            ->map(function ($row) use ($textField) {
                if (is_string($row)) {
                    $text = trim($row);
                    $icon = '';
                } else {
                    $row = is_array($row) ? $row : [];
                    $text = trim((string) ($row[$textField] ?? $row['label'] ?? $row['name'] ?? $row['title'] ?? $row['value'] ?? ''));
                    $icon = trim((string) ($row['icon'] ?? ''));
                }

                if ($text === '') {
                    return null;
                }

                $iconUrl = $icon !== '' ? media_url($icon) : null;

                return [
                    'label' => $text,
                    'name' => $text,
                    'title' => $text,
                    'value' => $text,
                    'icon' => filled($iconUrl) ? $iconUrl : null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<string, string|null>
     */
    protected function propertySeoPayload(
        Property $property,
        string $fallbackTitle,
        string $detailPath,
        string $fallbackImage,
        ?string $fallbackDescription = null
    ): array {
        $metadata = is_array($property->metadata) ? $property->metadata : [];
        $metaTitle = $this->trimSeoText(data_get($metadata, 'meta_title')) ?: $fallbackTitle;
        $metaDescription = $this->trimSeoText(data_get($metadata, 'meta_description'));
        if (! $metaDescription) {
            $metaDescription = $this->trimSeoText(strip_tags((string) $fallbackDescription)) ?: $fallbackTitle;
        }
        $canonicalUrl = $this->trimSeoText(data_get($metadata, 'canonical_url')) ?: url($detailPath);
        $ogImage = $this->trimSeoText(data_get($metadata, 'og_image'));
        $ogImageUrl = $ogImage ? media_url($ogImage) : $fallbackImage;

        return [
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'metaKeywords' => $this->trimSeoText(data_get($metadata, 'meta_keywords')),
            'canonicalUrl' => $canonicalUrl,
            'ogTitle' => $this->trimSeoText(data_get($metadata, 'og_title')) ?: $metaTitle,
            'ogDescription' => $this->trimSeoText(data_get($metadata, 'og_description')) ?: $metaDescription,
            'ogImage' => $ogImageUrl,
            'otherMetaTags' => $this->trimSeoText(data_get($metadata, 'other_meta_tags')),
        ];
    }

    protected function trimSeoText(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : null;
    }

    protected function setRequestLocale(Request $request): void
    {
        $requestedLang = strtolower((string) $request->query('lang', ''));
        if (in_array($requestedLang, ['en', 'ar'], true)) {
            app()->setLocale($requestedLang);
        }
    }

    protected function propertyLocationForLocale(Property $property, string $locale): string
    {
        $location = collect([
            // $property->getTranslation('address', $locale) ?: $property->address,
            $property->getTranslation('community', $locale) ?: $property->community,
            $property->getTranslation('city', $locale) ?: $property->city,
            $property->getTranslation('country', $locale) ?: $property->country,
        ])
            ->filter(fn ($value) => filled($value))
            ->implode(', ');

        if ($location !== '') {
            return $location;
        }

        return trim((string) ($property->getTranslation('address', $locale) ?: $property->address ?: 'United Arab Emirates'));
    }

    protected function propertyFullAddressForLocale(Property $property, string $locale): string
    {
        $address = trim((string) ($property->getTranslation('address', $locale) ?: $property->address));
        $parts = collect([
            $property->getTranslation('community', $locale) ?: $property->community,
            $property->getTranslation('city', $locale) ?: $property->city,
            $property->getTranslation('postal_code', $locale) ?: $property->postal_code,
            $property->getTranslation('country', $locale) ?: $property->country,
        ])
            ->map(fn ($value) => trim((string) $value))
            ->filter(fn ($value) => $value !== '')
            ->reject(fn ($value) => $address !== '' && Str::contains(Str::lower($address), Str::lower($value)))
            ->prepend($address)
            ->filter(fn ($value) => $value !== '')
            ->implode(', ');

        return $parts !== '' ? $parts : $this->propertyLocationForLocale($property, $locale);
    }

    protected function optionLabel(array $options, string $key): string
    {
        $key = trim($key);
        if ($key === '') {
            return '';
        }

        $option = $options[$key] ?? null;
        if (is_array($option)) {
            $locale = app()->getLocale();
            $fallback = config('app.fallback_locale', 'en');

            return trim((string) ($option[$locale] ?? $option[$fallback] ?? reset($option) ?: Str::headline(str_replace(['-', '_'], ' ', $key))));
        }

        if (is_string($option) && trim($option) !== '') {
            return trim($option);
        }

        return Str::headline(str_replace(['-', '_'], ' ', $key));
    }

    protected function propertyClassificationLabel(Property $property, string $field, array $options): string
    {
        $key = trim((string) $property->{$field});
        if ($key === '') {
            return '';
        }

        $metadata = is_array($property->metadata) ? $property->metadata : [];
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');
        $savedLabels = data_get($metadata, "classification_labels.{$field}.{$key}", []);

        if (is_array($savedLabels)) {
            $label = trim((string) ($savedLabels[$locale] ?? $savedLabels[$fallback] ?? reset($savedLabels) ?: ''));
            if ($label !== '') {
                return $label;
            }
        }

        return $this->optionLabel($options, $key);
    }

    protected function getSiteInfo()
    {
        return Schema::hasTable('site_information') ? SiteInformation::query()->first() : null;
    }

    protected function listingHeroBackground(): string
    {
        $fallback = asset('images/dre/hero-dubai.jpg');

        if (! Schema::hasTable('banners')) {
            return $fallback;
        }

        $banner = Banner::query()->where('status', true)->orderBy('order_index')->first();
        if (! $banner || ! filled($banner->image)) {
            return $fallback;
        }

        $path = (string) $banner->image;

        return preg_match('/^https?:\/\//i', $path)
            ? $path
            : (media_url($path) ?? $fallback);
    }

    protected function propertyHeroBackground(Property $property): string
    {
        $fallback = asset('images/dre/hero-dubai.jpg');
        $images = $property->images ?? [];
        if ($images === [] || ! isset($images[0])) {
            return $fallback;
        }

        $path = (string) ($images[0]->image ?? '');
        if ($path === '') {
            return $fallback;
        }

        return media_url($path) ?? $fallback;
    }

    /**
     * Same shape as the homepage search bar (CMS definitions optional later).
     *
     * @return array{filters: array<int, array<string, mixed>>}
     */
    protected function listingSearchFilters(): array
    {
        return [
            'filters' => [
                [
                    'key' => 'location',
                    'label' => 'Location',
                    'uiType' => 'text',
                    'queryParam' => 'location',
                    'options' => [],
                ],
                [
                    'key' => 'property_type',
                    'label' => 'Property Type',
                    'uiType' => 'dropdown',
                    'queryParam' => 'type',
                    'options' => [
                        ['value' => '', 'label' => 'Property Type'],
                        ['value' => 'apartment', 'label' => 'Apartment'],
                        ['value' => 'villa', 'label' => 'Villa'],
                        ['value' => 'townhouse', 'label' => 'Townhouse'],
                    ],
                ],
                [
                    'key' => 'category',
                    'label' => 'Category',
                    'uiType' => 'dropdown',
                    'queryParam' => 'category',
                    'options' => [
                        ['value' => '', 'label' => 'Category'],
                        ['value' => 'residential', 'label' => 'Residential'],
                        ['value' => 'commercial', 'label' => 'Commercial'],
                        ['value' => 'luxury', 'label' => 'Luxury'],
                        ['value' => 'off-plan', 'label' => 'Off-plan'],
                    ],
                ],
                [
                    'key' => 'bedrooms',
                    'label' => 'Bedrooms',
                    'uiType' => 'dropdown',
                    'queryParam' => 'beds',
                    'options' => [
                        ['value' => '', 'label' => 'Bedrooms'],
                        ['value' => '1', 'label' => '1 Bedroom'],
                        ['value' => '2', 'label' => '2 Bedrooms'],
                        ['value' => '3', 'label' => '3 Bedrooms'],
                        ['value' => '4', 'label' => '4 Bedrooms'],
                        ['value' => '5', 'label' => '5+ Bedrooms'],
                    ],
                ],
                [
                    'key' => 'bathrooms',
                    'label' => 'Bathrooms',
                    'uiType' => 'dropdown',
                    'queryParam' => 'baths',
                    'options' => [
                        ['value' => '', 'label' => 'Bathrooms'],
                        ['value' => '1', 'label' => '1 Bathroom'],
                        ['value' => '2', 'label' => '2 Bathrooms'],
                        ['value' => '3', 'label' => '3 Bathrooms'],
                        ['value' => '4', 'label' => '4+ Bathrooms'],
                    ],
                ],
                [
                    'key' => 'price',
                    'label' => 'Price Range',
                    'uiType' => 'dropdown',
                    'queryParam' => 'price',
                    'options' => [
                        ['value' => '', 'label' => 'Price Range'],
                        ['value' => '0-50000', 'label' => 'Up to 50,000'],
                        ['value' => '50000-100000', 'label' => '50,000 - 100,000'],
                        ['value' => '100000-250000', 'label' => '100,000 - 250,000'],
                    ],
                ],
            ],
        ];
    }

    protected function applyPriceRangeFilter($query, string $price): void
    {
        if ($price === '' || ! str_contains($price, '-')) {
            return;
        }

        [$min, $max] = array_pad(explode('-', $price, 2), 2, null);
        if (is_numeric($min)) {
            $query->where('price', '>=', (float) $min);
        }
        if (is_numeric($max)) {
            $query->where('price', '<=', (float) $max);
        }
    }

    protected function siteData(?SiteInformation $siteInfo): array
    {
        $companyName = trim((string) ($siteInfo?->company_name ?? ''));

        return [
            'name' => Str::upper(Str::substr($companyName !== '' ? $companyName : 'DRE', 0, 3)),
            'fullName' => $companyName !== '' ? $companyName : 'DISTINGUISHED REAL ESTATE',
            'logoUrl' => filled($siteInfo?->logo) ? media_url((string) $siteInfo->logo) : null,
            'logoAlt' => trim((string) ($siteInfo?->logo_alt ?? $companyName ?: 'Site logo')),
            'faviconUrl' => filled($siteInfo?->favicon) ? media_url((string) $siteInfo->favicon) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function headerData(?SiteInformation $siteInfo): array
    {
        return [
            'listPropertyUrl' => url('/').'#contact',
            'navItems' => [
                ['label' => 'Home', 'href' => url('/')],
                ['label' => 'Properties', 'href' => url('/properties')],
                ['label' => 'About', 'href' => url('/about')],
                ['label' => 'Map Search', 'href' => url('/map-search')],
                ['label' => 'Contact', 'href' => url('/').'#contact'],
            ],
        ];
    }

    protected function socialData(?SiteInformation $siteInfo): array
    {
        return PublicSiteViewData::followUsHybridPayload($siteInfo);
    }

    protected function footerData(?SiteInformation $siteInfo): array
    {
        $privacy = filled($siteInfo?->privacy_policy) ? $siteInfo->privacy_policy : '#';
        $terms = filled($siteInfo?->terms_and_conditions) ? $siteInfo->terms_and_conditions : '#';

        return [
            'columns' => [
                [
                    'title' => 'Quick Links',
                    'links' => [
                        ['label' => 'Home', 'href' => url('/')],
                        ['label' => 'About Us', 'href' => url('/about')],
                        ['label' => 'Our Properties', 'href' => url('/properties')],
                        ['label' => 'Map Search', 'href' => url('/map-search')],
                    ],
                ],
                [
                    'title' => 'Our Company',
                    'links' => [
                        ['label' => 'Property for Sale', 'href' => url('/properties?listing_type=sale')],
                        ['label' => 'Property for Rent', 'href' => url('/properties?listing_type=rent')],
                        ['label' => 'Tenant Portal', 'href' => url('/').'#contact'],
                    ],
                ],
                [
                    'title' => 'Policies',
                    'links' => [
                        ['label' => 'Privacy Policy', 'href' => $privacy],
                        ['label' => 'Terms & Conditions', 'href' => $terms],
                        ['label' => 'Cookies Policy', 'href' => $privacy],
                    ],
                ],
            ],
            'contact' => PublicSiteViewData::footerContactBlock($siteInfo),
            'copyright' => '© '.date('Y').' '.($siteInfo?->company_name ?: 'Distinguished Real Estate').'. All rights reserved.',
        ];
    }
}
