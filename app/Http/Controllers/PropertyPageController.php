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
                    ->orWhere('address', 'like', "%{$location}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('property_type', $request->query('type'));
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
            ->where('status', true)
            ->ordered();

        $this->applyPublishedPropertyFilters($request, $query);

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
     * API: Distinct property_type / category values from published properties with CMS labels.
     *
     * @return array{property_types: array<int, array{value: string, label: string}>, categories: array<int, array{value: string, label: string}>}
     */
    public function apiFilterOptions(Request $request): JsonResponse
    {
        $this->setRequestLocale($request);

        $typeConfig = config('cms-kit.database.properties.property_types', []);
        $catConfig = config('cms-kit.database.properties.categories', []);

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

        $propertyTypes = $typesUsed
            ->map(function (string $key) use ($typeConfig) {
                $key = trim($key);

                return [
                    'value' => $key,
                    'label' => $this->optionLabel($typeConfig, $key) ?: $key,
                ];
            })
            ->values();

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

        $categories = $categoriesUsed
            ->map(function (string $key) use ($catConfig) {
                $key = trim($key);

                return [
                    'value' => $key,
                    'label' => $this->optionLabel($catConfig, $key) ?: $key,
                ];
            })
            ->values();

        return response()->json([
            'property_types' => $propertyTypes,
            'categories' => $categories,
        ]);
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
                $path = (string) ($img->image ?? '');

                return $path !== '' ? media_url($path) : null;
            })
            ->filter(fn ($url) => filled($url))
            ->values();

        if ($images->isEmpty()) {
            $images = collect([dre_property_placeholder_image()]);
        }

        $title = $property->getTranslation('title') ?: $property->title ?: 'Property';
        $location = $this->propertyLocationForLocale($property, app()->getLocale());

        $agentPhone = trim((string) ($property->agent?->phone ?? $siteInfo?->phone_1 ?? ''));
        $whatsApp = trim((string) ($property->agent?->whatsapp_number ?? $siteInfo?->whatsapp_number ?? ''));
        $email = trim((string) ($siteInfo?->email_1 ?? 'hello@distinguishedre.ae'));
        $whatsAppHref = $whatsApp !== '' ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsApp) : '#';

        $slug = trim((string) ($property->slug ?? ''));
        $detailPath = $slug !== '' ? '/property-details/'.$slug : '/our-property';

        $data = [
            'id' => $property->id,
            'title' => $title,
            'slug' => $property->slug,
            'url' => $detailPath,
            'price' => (float) $property->price,
            'currency' => $property->currency ?: 'AED',
            'location' => $location,
            'latitude' => (float) $property->latitude,
            'longitude' => (float) $property->longitude,
            'bedrooms' => (int) $property->bedrooms,
            'bathrooms' => (int) $property->bathrooms,
            'beds' => (int) $property->bedrooms,
            'baths' => (int) $property->bathrooms,
            'sqft' => (int) $property->sqft,
            'period' => $property->listing_type === 'rent' ? ' / yr' : '',
            'property_type' => $property->property_type,
            'listing_type' => $property->listing_type,
            'category' => $property->category,
            'categoryLabel' => $this->optionLabel(config('cms-kit.database.properties.categories', []), (string) $property->category),
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
                'name' => $property->agent->name,
                'designation' => $property->agent->designation,
                'experience' => $property->agent->experience,
                'languages' => $property->agent->languages,
                'image' => $property->agent->image ? media_url((string) $property->agent->image) : asset('images/dre/agent-placeholder.svg'),
                'phone' => $property->agent->phone ? 'tel:'.preg_replace('/\s+/', '', (string) $property->agent->phone) : '#',
                'whatsapp' => $whatsAppHref,
            ] : null,
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
                        'latitude' => $np->latitude !== null ? (float) $np->latitude : null,
                        'longitude' => $np->longitude !== null ? (float) $np->longitude : null,
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
                        ['label' => 'Property for Sale', 'href' => url('/properties')],
                        ['label' => 'Property for Rent', 'href' => url('/properties')],
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
