<?php

namespace App\Http\Controllers;

use App\Models\CmsKit\Banner;
use App\Models\CmsKit\Property;
use App\Models\CmsKit\SiteInformation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * API: List properties with filters and pagination.
     */
    public function apiList(Request $request): JsonResponse
    {
        $query = Property::query()
            ->with(['agent', 'translations'])
            ->where('status', true)
            ->ordered();

        // Filtering
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

        if ($request->filled('beds')) {
            $query->where('bedrooms', '>=', (int) $request->query('beds'));
        }

        if ($request->filled('baths')) {
            $query->where('bathrooms', '>=', (int) $request->query('baths'));
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->query('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->query('price_max'));
        }

        if ($request->filled('price')) {
            $this->applyPriceRangeFilter($query, (string) $request->query('price'));
        }

        $properties = $query->paginate(12);

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
        $property = Property::query()
            ->with(['agent', 'details', 'translations', 'nearbyPlaces'])
            ->where('status', true)
            ->findOrFail($id);

        $siteInfo = $this->getSiteInfo();
        $formatted = $this->formatProperty($property, true, $siteInfo);

        // Similar Listings
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

        return response()->json([
            'property' => $formatted,
            'similar' => $similarListings,
        ]);
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
        $location = collect([$property->community, $property->city, $property->country])
            ->filter(fn ($value) => filled($value))
            ->implode(', ');
        if ($location === '') {
            $location = trim((string) ($property->getTranslation('address') ?: $property->address ?: ''));
        }
        if ($location === '') {
            $location = 'United Arab Emirates';
        }

        $agentPhone = trim((string) ($property->agent?->phone ?? $siteInfo?->phone_1 ?? ''));
        $whatsApp = trim((string) ($property->agent?->whatsapp_number ?? $siteInfo?->whatsapp_number ?? ''));
        $email = trim((string) ($siteInfo?->email_1 ?? 'hello@distinguishedre.ae'));
        $whatsAppHref = $whatsApp !== '' ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsApp) : '#';

        $data = [
            'id' => $property->id,
            'title' => $title,
            'slug' => $property->slug,
            'url' => route('properties.show', $property->slug),
            'price' => (float) $property->price,
            'currency' => $property->currency ?: 'AED',
            'location' => $location,
            'bedrooms' => (int) $property->bedrooms,
            'bathrooms' => (int) $property->bathrooms,
            'beds' => (int) $property->bedrooms,
            'baths' => (int) $property->bathrooms,
            'sqft' => (int) $property->sqft,
            'period' => $property->listing_type === 'rent' ? ' / yr' : '',
            'property_type' => $property->property_type,
            'listing_type' => $property->listing_type,
            'images' => $images->all(),
            'featured_image' => $images->first(),
            'phone' => $agentPhone !== '' ? 'tel:'.preg_replace('/\s+/', '', $agentPhone) : '#contact',
            'whatsapp' => $whatsAppHref,
            'inquireUrl' => 'mailto:'.rawurlencode($email).'?subject='.rawurlencode('Inquiry - '.$title),
            'agent' => $property->agent ? [
                'name' => $property->agent->name,
                'designation' => $property->agent->designation,
                'image' => $property->agent->image ? media_url((string) $property->agent->image) : asset('images/dre/agent-placeholder.jpg'),
                'phone' => $property->agent->phone ? 'tel:'.preg_replace('/\s+/', '', (string) $property->agent->phone) : '#',
                'whatsapp' => $whatsAppHref,
            ] : null,
        ];

        if ($full) {
            $data['description'] = $property->getTranslation('description') ?: $property->details?->description;
            $data['details_grid'] = $property->details?->property_attributes ?: [];
            $data['amenities'] = $property->details?->amenities ?: [];
            $data['features'] = $property->details?->key_features ?: [];
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale', 'en');
            $translation = $property->translations->firstWhere('language_code', $locale)
                ?? $property->translations->firstWhere('language_code', $fallbackLocale)
                ?? $property->translations->first();

            $cmsEasyRows = is_array($translation?->easy_to_access) ? $translation->easy_to_access : [];
            $cmsEasyCards = collect($cmsEasyRows)
                ->map(function ($row) {
                    $label = trim((string) ($row['label'] ?? ''));
                    if ($label === '') {
                        return null;
                    }
                    $iconPath = trim((string) ($row['icon'] ?? ''));
                    $iconUrl = $iconPath !== '' ? media_url($iconPath) : null;

                    return [
                        'name' => $label,
                        'distance' => null,
                        'icon' => filled($iconUrl) ? $iconUrl : null,
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
        $links = collect([
            ['network' => 'facebook', 'href' => $siteInfo?->facebook, 'label' => 'Facebook'],
            ['network' => 'twitter', 'href' => $siteInfo?->twitter, 'label' => 'X'],
            ['network' => 'linkedin', 'href' => $siteInfo?->linkedin, 'label' => 'LinkedIn'],
            ['network' => 'instagram', 'href' => $siteInfo?->instagram, 'label' => 'Instagram'],
            ['network' => 'youtube', 'href' => $siteInfo?->youtube, 'label' => 'YouTube'],
        ])
            ->filter(fn ($link) => filled($link['href']))
            ->values()
            ->all();

        if (empty($links)) {
            $links = [
                ['network' => 'facebook', 'href' => '#', 'label' => 'Facebook'],
                ['network' => 'instagram', 'href' => '#', 'label' => 'Instagram'],
                ['network' => 'linkedin', 'href' => '#', 'label' => 'LinkedIn'],
            ];
        }

        return [
            'title' => 'Follow Us',
            'links' => $links,
        ];
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
            'contact' => [
                'phone' => $siteInfo?->phone_1 ?: '+971 4 343 8302',
                'phoneAlt' => $siteInfo?->phone_2 ?: ($siteInfo?->whatsapp_number ?: ''),
                'email' => $siteInfo?->email_1 ?: 'info@dreuae.ae',
            ],
            'copyright' => '© '.date('Y').' '.($siteInfo?->company_name ?: 'Distinguished Real Estate').'. All rights reserved.',
        ];
    }
}
