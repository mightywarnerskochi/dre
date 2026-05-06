<?php

namespace App\Http\Controllers;

use App\Models\CmsKit\About;
use App\Models\CmsKit\Banner;
use App\Models\CmsKit\HomeBannerFilterDefinition;
use App\Models\CmsKit\MissionVision;
use App\Models\CmsKit\Neighborhood;
use App\Models\CmsKit\Property;
use App\Models\CmsKit\SectionLabel;
use App\Models\CmsKit\SiteInformation;
use App\Models\CmsKit\SuccessfulJourney;
use App\Models\CmsKit\WhyChooseUs;
use App\Support\PublicSiteViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'pageData' => $this->pageData(),
        ]);
    }

    public function mapPage(): View
    {
        return view('map-search', [
            'pageData' => $this->mapPageData(),
        ]);
    }

    public function aboutPage(): View
    {
        return view('about', [
            'pageData' => $this->aboutPageData(),
        ]);
    }

    /**
     * Homepage payload for the Vue app.
     *
     * @return array<string, mixed>
     */
    protected function pageData(): array
    {
        $siteInfo = Schema::hasTable('site_information') ? SiteInformation::query()->first() : null;

        return [
            'site' => $this->siteData($siteInfo),
            'header' => [
                'listPropertyUrl' => '#contact',
            ],
            'hero' => $this->heroData(),
            'search' => $this->homeSearchFilters(),
            'rentalSection' => $this->rentalSectionData($siteInfo),
            'neighborhoods' => $this->neighborhoodsData(),
            'about' => $this->aboutData(),
            'articlesSection' => [
                'eyebrow' => 'News & Insights',
                'title' => 'Insights',
                'articles' => [
                    [
                        'id' => 1,
                        'category' => 'Real Estate',
                        'title' => 'UAE rental trends to watch this quarter',
                        'excerpt' => 'Supply in prime waterfront districts remains tight while inner-community villas see renewed interest from families.',
                        'image' => asset('images/dre/news-1.jpg'),
                        'url' => '#',
                    ],
                    [
                        'id' => 2,
                        'category' => 'Real Estate',
                        'title' => 'Why skyline living continues to attract investors',
                        'excerpt' => 'From service charges to views - what buyers weigh before committing to high-rise residences in central districts.',
                        'image' => asset('images/dre/news-2.jpg'),
                        'url' => '#',
                    ],
                ],
            ],
            'appCta' => [
                'title' => "Download the UAE's most trusted property search app",
                'subtitle' => 'Search, save, and chat with agents on the go.',
                'playStoreUrl' => '#',
                'appStoreUrl' => '#',
                'qrCodeUrl' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.urlencode(config('app.url')),
            ],
            'social' => $this->socialData($siteInfo),
            'footer' => $this->footerData($siteInfo),
        ];
    }

    protected function mapPageData(): array
    {
        $siteInfo = Schema::hasTable('site_information') ? SiteInformation::query()->first() : null;
        $fallbackCenter = ['lat' => 25.2048, 'lng' => 55.2708, 'zoom' => 11];

        $firstNeighborhood = Schema::hasTable('neighborhoods')
            ? Neighborhood::query()->where('status', true)->orderBy('order_index')->first()
            : null;

        return [
            'site' => $this->siteData($siteInfo),
            'header' => [
                'listPropertyUrl' => '#contact',
            ],
            'hero' => [
                'title' => 'Our Properties',
                'breadcrumb' => 'Our Properties',
            ],
            'search' => $this->homeSearchFilters(),
            'map' => [
                'endpoint' => route('map-search.properties'),
                'defaultCenter' => [
                    'lat' => (float) ($firstNeighborhood?->latitude ?? $fallbackCenter['lat']),
                    'lng' => (float) ($firstNeighborhood?->longitude ?? $fallbackCenter['lng']),
                    'zoom' => $fallbackCenter['zoom'],
                ],
                'locale' => app()->getLocale(),
            ],
            'social' => $this->socialData($siteInfo),
            'footer' => $this->footerData($siteInfo),
        ];
    }

    /**
     * About page payload (CMS: about, mission & vision, why choose us, successful journeys).
     *
     * @return array<string, mixed>
     */
    protected function aboutPageData(): array
    {
        $siteInfo = Schema::hasTable('site_information') ? SiteInformation::query()->first() : null;
        $intro = $this->aboutPageIntroData();

        return [
            'site' => $this->siteData($siteInfo),
            'header' => [
                'listPropertyUrl' => url('/').'#contact',
                'navItems' => [
                    ['label' => 'Home', 'href' => url('/')],
                    ['label' => 'Properties', 'href' => url('/properties')],
                    ['label' => 'About', 'href' => url('/about')],
                    ['label' => 'Map Search', 'href' => url('/map-search')],
                    ['label' => 'Contact', 'href' => url('/').'#contact'],
                ],
            ],
            'hero' => $this->aboutHeroData(),
            'about' => $intro,
            'missionVision' => $this->missionVisionPageData(),
            'whyChooseUs' => $this->whyChooseUsPageData($intro['gallery'] ?? []),
            'journey' => $this->successfulJourneyPageData(),
            'social' => $this->socialData($siteInfo),
            'footer' => $this->publicSiteFooterData($siteInfo),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function aboutHeroData(): array
    {
        $fallbackBg = asset('images/dre/hero-dubai.jpg');
        $backgroundImage = $fallbackBg;

        if (Schema::hasTable('banners')) {
            $banner = Banner::query()->where('status', true)->orderBy('order_index')->first();
            if ($banner && filled($banner->image)) {
                $path = (string) $banner->image;
                $backgroundImage = preg_match('/^https?:\/\//i', $path)
                    ? $path
                    : (media_url($path) ?? $fallbackBg);
            }
        }

        return [
            'title' => 'About Us',
            'breadcrumbLabel' => 'About Us',
            'backgroundImage' => $backgroundImage,
        ];
    }

    /**
     * @return array{eyebrow: string, title: string, bodyHtml: string, gallery: array<int, array{src: string, alt: string}>}
     */
    protected function aboutPageIntroData(): array
    {
        $fallback = [
            'eyebrow' => 'About Us',
            'title' => 'Welcome to Distinguished Real Estate',
            'bodyHtml' => '<p>For over a decade we have connected discerning clients with exceptional homes and investments across the Emirates.</p>',
            'gallery' => [
                ['src' => asset('images/dre/about-building.jpg'), 'alt' => ''],
                ['src' => dre_property_placeholder_image(), 'alt' => ''],
                ['src' => dre_property_placeholder_image(), 'alt' => ''],
                ['src' => dre_property_placeholder_image(), 'alt' => ''],
            ],
        ];

        if (! Schema::hasTable('abouts')) {
            return $fallback;
        }

        $about = About::query()->where('status', true)->first();
        if (! $about) {
            return $fallback;
        }

        $title = trim((string) ($about->getTranslation('title') ?? ''));
        $subtitle = trim((string) ($about->getTranslation('subtitle') ?? ''));
        $description = (string) ($about->getTranslation('description') ?? '');
        $bodyHtml = trim($description) !== '' ? $description : $fallback['bodyHtml'];

        $gallery = [];
        foreach ([1, 2, 3, 4] as $i) {
            $path = $about->{"image_$i"} ?? null;
            if (filled($path)) {
                $url = media_url((string) $path);
                if ($url) {
                    $gallery[] = [
                        'src' => $url,
                        'alt' => trim((string) ($about->{"image_{$i}_alt"} ?? '')),
                    ];
                }
            }
        }

        if (empty($gallery)) {
            $gallery = $fallback['gallery'];
        }

        return [
            'eyebrow' => $subtitle !== '' ? $subtitle : $fallback['eyebrow'],
            'title' => $title !== '' ? $title : $fallback['title'],
            'bodyHtml' => $bodyHtml,
            'gallery' => $gallery,
        ];
    }

    /**
     * @return array{items: array<int, array<string, mixed>>}
     */
    protected function missionVisionPageData(): array
    {
        if (! Schema::hasTable('mission_visions')) {
            return ['items' => []];
        }

        $rows = MissionVision::query()
            ->where('status', true)
            ->orderBy('order_index')
            ->take(3)
            ->get();

        $items = [];
        foreach ($rows as $idx => $mv) {
            $n = $idx + 1;
            $imagePath = (string) ($mv->image ?? '');
            $imageUrl = $imagePath !== '' ? media_url($imagePath) : null;
            $useOverlay = $idx === 1 && filled($imageUrl);

            $items[] = [
                'index' => str_pad((string) $n, 2, '0', STR_PAD_LEFT),
                'title' => trim((string) ($mv->getTranslation('title') ?? '')),
                'body' => trim((string) ($mv->getTranslation('description') ?? '')),
                'image' => $imageUrl,
                'variant' => $useOverlay ? 'overlay' : 'plain',
            ];
        }

        return ['items' => $items];
    }

    /**
     * @param  array<int, array{src: string, alt: string}>  $aboutGallery
     * @return array{active: bool, section: ?array<string, mixed>, items: array<int, array<string, mixed>>, collageImages: array<int, string>}
     */
    protected function whyChooseUsPageData(array $aboutGallery): array
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');

        $section = Schema::hasTable('section_labels')
            ? SectionLabel::query()->where('section_key', 'why_choose_us')->first()
            : null;

        $sectionImage = null;
        $sectionImageAlt = '';
        $eyebrow = 'Strengths';
        $title = 'Why Choose Us';
        $intro = '';
        $sectionActive = true;

        if ($section) {
            $sectionActive = (bool) $section->status;
            $eyebrow = (string) (data_get($section->translations, $locale.'.subtitle')
                ?: data_get($section->translations, $fallbackLocale.'.subtitle')
                ?: $eyebrow);
            $title = (string) (data_get($section->translations, $locale.'.title')
                ?: data_get($section->translations, $fallbackLocale.'.title')
                ?: $title);
            $intro = (string) (data_get($section->translations, $locale.'.description')
                ?: data_get($section->translations, $fallbackLocale.'.description')
                ?: '');
            if (filled($section->section_image)) {
                $sectionImage = media_url((string) $section->section_image) ?: null;
            }
            $sectionImageAlt = trim((string) ($section->section_image_alt ?? ''));
        }

        $items = [];
        if (Schema::hasTable('why_choose_us_items')) {
            $rows = WhyChooseUs::query()
                ->where('status', true)
                ->orderBy('order_index')
                ->get();

            foreach ($rows as $idx => $row) {
                $items[] = [
                    'index' => str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT),
                    'title' => trim((string) ($row->getTranslation('title') ?? '')),
                    'body' => trim((string) ($row->getTranslation('description') ?? '')),
                ];
            }
        }

        $collage = [];
        if ($sectionImage) {
            $collage[] = $sectionImage;
        }
        foreach ($aboutGallery as $img) {
            if (count($collage) >= 3) {
                break;
            }
            $src = is_array($img) ? (string) ($img['src'] ?? '') : '';
            if ($src !== '' && ! in_array($src, $collage, true)) {
                $collage[] = $src;
            }
        }
        while (count($collage) < 3) {
            $collage[] = dre_property_placeholder_image();
        }

        return [
            'active' => $sectionActive && (count($items) > 0 || $title !== ''),
            'section' => [
                'eyebrow' => $eyebrow,
                'title' => $title,
                'intro' => $intro,
                'sectionImage' => $sectionImage,
                'sectionImageAlt' => $sectionImageAlt,
            ],
            'items' => $items,
            'collageImages' => array_slice($collage, 0, 3),
        ];
    }

    /**
     * @return array{title: string, items: array<int, array<string, mixed>>}
     */
    protected function successfulJourneyPageData(): array
    {
        $out = [
            'title' => 'Our Successful Journey',
            'items' => [],
        ];

        if (! Schema::hasTable('successful_journeys')) {
            return $out;
        }

        $rows = SuccessfulJourney::query()
            ->where('status', true)
            ->orderBy('order_index')
            ->get();

        foreach ($rows as $row) {
            $description = (string) ($row->getTranslation('description') ?? '');
            $img1 = filled($row->image_1) ? media_url((string) $row->image_1) : null;
            $img2 = filled($row->image_2) ? media_url((string) $row->image_2) : null;

            $out['items'][] = [
                'year' => trim((string) ($row->year ?? '')),
                'bodyHtml' => $description,
                'images' => array_values(array_filter([$img1, $img2])),
            ];
        }

        return $out;
    }

    /**
     * Footer link columns for multi-page public routes.
     *
     * @return array<string, mixed>
     */
    protected function publicSiteFooterData(?SiteInformation $siteInfo): array
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

    protected function neighborhoodsData(): array
    {
        $fallback = [
            'title' => 'Explore Neighborhoods',
            'description' => 'Discover communities we serve across the Emirates.',
            'items' => [
                ['id' => 1, 'label' => 'Dubai', 'latitude' => 25.2048, 'longitude' => 55.2708],
                ['id' => 2, 'label' => 'Sharjah', 'latitude' => 25.3463, 'longitude' => 55.4209],
            ],
            'endpointBase' => url('/home/neighborhoods'),
            'locale' => app()->getLocale(),
        ];

        if (! Schema::hasTable('neighborhoods')) {
            return $fallback;
        }

        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        $section = Schema::hasTable('section_labels')
            ? SectionLabel::query()->where('section_key', 'neighborhoods')->first()
            : null;

        $title = data_get($section?->translations, $locale.'.title')
            ?: data_get($section?->translations, $fallbackLocale.'.title')
            ?: $fallback['title'];
        $description = data_get($section?->translations, $locale.'.description')
            ?: data_get($section?->translations, $fallbackLocale.'.description')
            ?: $fallback['description'];

        $items = Neighborhood::query()
            ->where('status', true)
            ->orderBy('order_index')
            ->get()
            ->map(function (Neighborhood $n) use ($locale, $fallbackLocale) {
                $label = data_get($n->translations, $locale.'.name')
                    ?: data_get($n->translations, $fallbackLocale.'.name')
                    ?: ('Neighborhood '.$n->id);

                return [
                    'id' => (int) $n->id,
                    'label' => (string) $label,
                    'latitude' => (float) $n->latitude,
                    'longitude' => (float) $n->longitude,
                ];
            })
            ->values()
            ->all();

        return [
            'title' => $title,
            'description' => $description,
            'items' => $items ?: $fallback['items'],
            'endpointBase' => url('/home/neighborhoods'),
            'locale' => $locale,
        ];
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

    protected function rentalSectionData(?SiteInformation $siteInfo): ?array
    {
        $fallback = [
            'title' => 'Rental Properties',
            'description' => 'Hand-picked homes in Dubai and Sharjah. Browse featured listings and reach us instantly.',
            'properties' => [],
        ];
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        $section = Schema::hasTable('section_labels')
            ? SectionLabel::query()->where('section_key', 'properties')->first()
            : null;
        $sectionTitle = data_get($section?->translations, $locale.'.title')
            ?: data_get($section?->translations, $fallbackLocale.'.title')
            ?: $fallback['title'];
        $sectionDescription = data_get($section?->translations, $locale.'.description')
            ?: data_get($section?->translations, $fallbackLocale.'.description')
            ?: $fallback['description'];
        $displayOnHome = $section ? (bool) $section->status : true;

        if (! $displayOnHome) {
            return null;
        }

        if (! Schema::hasTable('properties')) {
            return [
                'title' => $sectionTitle,
                'description' => $sectionDescription,
                'properties' => [],
            ];
        }

        $properties = Property::query()
            ->with(['agent', 'details', 'translations'])
            ->where('status', true)
            ->ordered()
            ->take(6)
            ->get();

        if ($properties->isEmpty()) {
            return [
                'title' => $sectionTitle,
                'description' => $sectionDescription,
                'properties' => [],
            ];
        }

        return [
            'title' => $sectionTitle,
            'description' => $sectionDescription,
            'properties' => $properties->map(function (Property $property) use ($siteInfo) {
                $title = [
                    'en' => $property->getTranslation('title', 'en') ?: $property->title ?: 'Property',
                    'ar' => $property->getTranslation('title', 'ar') ?: ($property->getTranslation('title', 'en') ?: $property->title ?: 'Property'),
                ];

                $imageUrls = collect($property->images ?? [])
                    ->map(function ($image) {
                        $path = (string) ($image->image ?? '');

                        return $path !== '' ? media_url($path) : null;
                    })
                    ->filter(fn ($url) => filled($url))
                    ->values();

                $imageCount = $imageUrls->count();
                $images = $imageUrls->all();

                if ($images === []) {
                    $images = [dre_property_placeholder_image()];
                }

                $agentPhone = trim((string) ($property->agent?->phone ?? $siteInfo?->phone_1 ?? ''));
                $whatsApp = trim((string) ($property->agent?->whatsapp_number ?? $siteInfo?->whatsapp_number ?? ''));
                $email = trim((string) ($siteInfo?->email_1 ?? 'hello@distinguishedre.ae'));
                $virtualTour = trim((string) ($property->details?->virtual_tour_url ?? ''));
                $labels = $this->rentalPropertyClassificationLabels(
                    (string) ($property->property_type ?? ''),
                    (string) ($property->listing_type ?? ''),
                    (string) ($property->category ?? ''),
                );

                return [
                    'id' => $property->id,
                    'title' => $title,
                    'location' => [
                        'en' => $this->propertyLocationForLocale($property, 'en'),
                        'ar' => $this->propertyLocationForLocale($property, 'ar'),
                    ],
                    'price' => (float) ($property->price ?? 0),
                    'period' => $property->listing_type === 'rent' ? ' / yr' : '',
                    'beds' => (int) ($property->bedrooms ?? 0),
                    'baths' => (int) ($property->bathrooms ?? 0),
                    'sqft' => (int) ($property->sqft ?? 0),
                    'images' => $images,
                    'imageCount' => $imageCount,
                    'isFeatured' => (bool) $property->is_featured,
                    'virtualTourUrl' => $virtualTour !== '' ? $virtualTour : null,
                    'propertyTypeLabel' => $labels['propertyType'],
                    'listingTypeLabel' => $labels['listingType'],
                    'categoryLabel' => $labels['category'],
                    'url' => filled($property->slug) ? route('properties.show', $property->slug) : null,
                    'phone' => $agentPhone !== '' ? 'tel:'.preg_replace('/\s+/', '', $agentPhone) : '#contact',
                    'whatsapp' => $whatsApp !== '' ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsApp) : '#contact',
                    'inquireUrl' => 'mailto:'.rawurlencode($email).'?subject='.rawurlencode('Inquiry - '.$title['en']),
                ];
            })->all(),
        ];
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

    protected function rentalPropertyClassificationLabels(string $propertyType, string $listingType, string $category = ''): array
    {
        return [
            'propertyType' => $this->localizedOptionLabel(
                config('cms-kit.database.properties.property_types', []),
                $propertyType
            ),
            'listingType' => $this->localizedOptionLabel(
                config('cms-kit.database.properties.listing_types', []),
                $listingType
            ),
            'category' => $this->localizedOptionLabel(
                config('cms-kit.database.properties.categories', []),
                $category
            ),
        ];
    }

    protected function localizedOptionLabel(array $options, string $key): array
    {
        $key = trim($key);
        if ($key === '') {
            return ['en' => '', 'ar' => ''];
        }

        $option = $options[$key] ?? null;
        if (is_array($option)) {
            $en = trim((string) ($option['en'] ?? '')) ?: Str::headline(str_replace(['-', '_'], ' ', $key));
            $ar = trim((string) ($option['ar'] ?? '')) ?: $en;

            return ['en' => $en, 'ar' => $ar];
        }

        $label = is_string($option) && trim($option) !== ''
            ? trim($option)
            : Str::headline(str_replace(['-', '_'], ' ', $key));

        return ['en' => $label, 'ar' => $label];
    }

    protected function aboutData(): array
    {
        $fallback = [
            'eyebrow' => 'About Us',
            'title' => 'Welcome to Distinguished Real Estate',
            'body' => 'For over a decade we have connected discerning clients with exceptional homes and investments across the Emirates.',
            'readMoreUrl' => '#contact',
            'image' => asset('images/dre/about-building.jpg'),
            'imageAlt' => 'About Distinguished Real Estate',
        ];

        if (! Schema::hasTable('abouts')) {
            return $fallback;
        }

        $about = About::query()->where('status', true)->first();

        if (! $about) {
            return $fallback;
        }

        $title = trim((string) ($about->getTranslation('title') ?? ''));
        $subtitle = trim((string) ($about->getTranslation('subtitle') ?? ''));
        $description = trim(strip_tags((string) ($about->getTranslation('description') ?? '')));
        $homeImage = $about->home_image ? media_url((string) $about->home_image) : null;
        $image = filled($homeImage)
            ? $homeImage
            : ($about->image_1 ? (media_url((string) $about->image_1) ?? $fallback['image']) : $fallback['image']);
        $imageAlt = trim((string) ($about->home_image_alt ?? ''));

        return [
            'eyebrow' => $subtitle !== '' ? $subtitle : $fallback['eyebrow'],
            'title' => $title !== '' ? $title : $fallback['title'],
            'body' => $description !== '' ? $description : $fallback['body'],
            'readMoreUrl' => '#contact',
            'image' => $image,
            'imageAlt' => $imageAlt !== '' ? $imageAlt : $fallback['imageAlt'],
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
                    'title' => 'Our Services',
                    'links' => [
                        ['label' => 'Properties for Rent', 'href' => '#rentals'],
                        ['label' => 'About Us', 'href' => '#about'],
                        ['label' => 'Neighborhoods', 'href' => '#neighborhoods'],
                    ],
                ],
                [
                    'title' => 'Quick Links',
                    'links' => [
                        ['label' => 'Home', 'href' => '/'],
                        ['label' => 'Latest News', 'href' => '#news'],
                        ['label' => 'Contact', 'href' => '#contact'],
                    ],
                ],
                [
                    'title' => 'Policy',
                    'links' => [
                        ['label' => 'Privacy Policy', 'href' => $privacy],
                        ['label' => 'Terms & Conditions', 'href' => $terms],
                    ],
                ],
            ],
            'contact' => PublicSiteViewData::footerContactBlock($siteInfo),
            'copyright' => '© '.date('Y').' '.($siteInfo?->company_name ?: 'Distinguished Real Estate').'. All rights reserved.',
        ];
    }

    protected function homeSearchFilters(): array
    {
        if (! Schema::hasTable('home_banner_filter_definitions') || ! Schema::hasTable('home_banner_filter_values')) {
            return $this->fallbackSearchFilters();
        }

        $definitions = HomeBannerFilterDefinition::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get();

        if ($definitions->isEmpty()) {
            return $this->fallbackSearchFilters();
        }

        $currentLocale = app()->getLocale();
        $filters = [];

        foreach ($definitions as $definition) {
            $queryParam = $this->queryParamForFilter($definition->key);
            if (! $queryParam) {
                continue;
            }

            $values = $definition->values()
                ->where('status', true)
                ->orderBy('sort_order')
                ->get(['value', 'label', 'translations']);

            $options = $values->map(function ($v) use ($currentLocale, $definition) {
                $trans = data_get($v->translations, $currentLocale, []);
                $label = data_get($trans, 'label') !== null
                    ? (string) data_get($trans, 'label')
                    : ($v->label ? (string) $v->label : (string) $v->value);

                $option = [
                    'value' => (string) $v->value,
                    'label' => $label,
                ];

                if ((string) $definition->key === 'location') {
                    $option['subtitle'] = (string) data_get($trans, 'subtitle', '');
                    $option['type'] = (string) data_get($trans, 'type', '');
                }

                return $option;
            })->values()->all();

            $label = data_get($definition->translations, $currentLocale.'.label')
                ?: (string) $definition->label;

            if (! collect($options)->contains(fn ($opt) => (string) $opt['value'] === '')) {
                array_unshift($options, [
                    'value' => '',
                    'label' => $label,
                ]);
            }

            $filters[] = [
                'key' => (string) $definition->key,
                'label' => $label,
                'uiType' => (string) (
                    (string) $definition->key === 'location'
                        ? 'text'
                        : ($definition->ui_type ?: $this->fallbackUiTypeForFilter((string) $definition->key))
                ),
                'queryParam' => $queryParam,
                'options' => $options,
            ];
        }

        if (empty($filters)) {
            return $this->fallbackSearchFilters();
        }

        return ['filters' => $filters];
    }

    protected function heroData(): array
    {
        $fallbackSlide = [
            'line1' => 'Discover Your',
            'line2' => 'Perfect Living Spot',
            'backgroundImage' => asset('images/dre/hero-dubai.jpg'),
            'primaryActionLabel' => 'Explore Rentals',
            'primaryActionUrl' => '#rentals',
        ];

        if (! Schema::hasTable('banners')) {
            return ['slides' => [$fallbackSlide]];
        }

        $banners = Banner::query()
            ->where('status', true)
            ->orderBy('order_index')
            ->get();

        if ($banners->isEmpty()) {
            return ['slides' => [$fallbackSlide]];
        }

        $slides = $banners->map(function (Banner $banner) use ($fallbackSlide) {
            $line1 = trim((string) ($banner->getTranslation('line_1') ?? ''));
            $line2 = trim((string) ($banner->getTranslation('line_2') ?? ''));

            if ($line1 === '' && $line2 === '') {
                $line1 = $fallbackSlide['line1'];
                $line2 = $fallbackSlide['line2'];
            } elseif ($line2 === '') {
                $words = preg_split('/\s+/', $line1, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $midpoint = max(1, (int) ceil(count($words) / 2));
                $line1 = implode(' ', array_slice($words, 0, $midpoint));
                $line2 = implode(' ', array_slice($words, $midpoint));
            }

            $imagePath = (string) ($banner->image ?? '');
            $backgroundImage = $fallbackSlide['backgroundImage'];
            if ($imagePath !== '') {
                $backgroundImage = preg_match('/^https?:\/\//i', $imagePath)
                    ? $imagePath
                    : (media_url($imagePath) ?? $fallbackSlide['backgroundImage']);
            }

            $buttons = (array) data_get($banner->translations, app()->getLocale().'.buttons', []);
            $button = $buttons[0] ?? null;

            return [
                'id' => $banner->id,
                'line1' => $line1,
                'line2' => $line2 !== '' ? $line2 : $fallbackSlide['line2'],
                'backgroundImage' => $backgroundImage,
                'primaryActionLabel' => trim((string) data_get($button, 'label', $fallbackSlide['primaryActionLabel'])),
                'primaryActionUrl' => trim((string) data_get($button, 'url', $fallbackSlide['primaryActionUrl'])) ?: '#rentals',
            ];
        })->values()->all();

        return ['slides' => $slides];
    }

    protected function queryParamForFilter(string $key): ?string
    {
        return [
            'location' => 'location',
            'property_type' => 'type',
            'bedrooms' => 'beds',
            'bathrooms' => 'baths',
            'bed_and_baths' => 'beds_baths',
            'price' => 'price',
        ][$key] ?? null;
    }

    protected function fallbackUiTypeForFilter(string $key): string
    {
        return [
            'location' => 'dropdown',
            'property_type' => 'dropdown',
            'bedrooms' => 'dropdown',
            'bathrooms' => 'dropdown',
            'bed_and_baths' => 'dropdown',
            'price' => 'dropdown',
        ][$key] ?? 'dropdown';
    }

    protected function fallbackSearchFilters(): array
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

    public function neighborhoodProperties(Request $request, int $id): JsonResponse
    {
        $requestedLang = strtolower((string) $request->query('lang', ''));
        if (in_array($requestedLang, ['en', 'ar'], true)) {
            app()->setLocale($requestedLang);
        }
        $lite = $request->boolean('lite');

        if (! Schema::hasTable('neighborhoods') || ! Schema::hasTable('properties')) {
            return response()->json(['neighborhood' => null, 'properties' => []]);
        }

        $neighborhood = Neighborhood::query()
            ->where('status', true)
            ->findOrFail($id);

        $radiusKm = (float) $request->query('radius_km', 15);
        $radiusKm = max(1, min($radiusKm, 100));

        $lat = (float) $neighborhood->latitude;
        $lng = (float) $neighborhood->longitude;
        $distanceExpr = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';
        $north = $request->query('north');
        $south = $request->query('south');
        $east = $request->query('east');
        $west = $request->query('west');
        $hasBounds = is_numeric($north) && is_numeric($south) && is_numeric($east) && is_numeric($west);

        $baseSelect = ['id', 'latitude', 'longitude'];
        if (! $lite) {
            $baseSelect = ['id', 'title', 'price', 'currency', 'city', 'community', 'latitude', 'longitude'];
        }

        $query = Property::query()
            ->with($lite ? [] : ['translations'])
            ->select($baseSelect)
            ->selectRaw($distanceExpr.' as distance_km', [$lat, $lng, $lat])
            ->where('status', true)
            ->whereNotNull('published_at')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($hasBounds) {
            $north = (float) $north;
            $south = (float) $south;
            $east = (float) $east;
            $west = (float) $west;

            $maxLat = max($north, $south);
            $minLat = min($north, $south);
            $maxLng = max($east, $west);
            $minLng = min($east, $west);

            $query->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLng, $maxLng]);
        } else {
            $query->having('distance_km', '<=', $radiusKm);
        }

        $rows = $query->orderBy('distance_km')
            ->limit(80)
            ->get();

        $properties = $rows->map(function (Property $property) use ($lite) {
            if ($lite) {
                return [
                    'id' => (int) $property->id,
                    'latitude' => (float) $property->latitude,
                    'longitude' => (float) $property->longitude,
                ];
            }

            $title = (string) ($property->getTranslation('title') ?: $property->title ?: 'Property');
            $city = (string) ($property->getTranslation('city') ?: $property->city ?: '');
            $community = (string) ($property->getTranslation('community') ?: $property->community ?: '');

            return [
                'id' => (int) $property->id,
                'title' => $title,
                'price' => (float) $property->price,
                'priceLabel' => ($this->isArabicLocale() ? 'من ' : 'From ').$this->compactPrice((float) $property->price),
                'currency' => (string) ($property->currency ?: 'AED'),
                'latitude' => (float) $property->latitude,
                'longitude' => (float) $property->longitude,
                'location' => trim((string) ($community.(($community && $city) ? ', ' : '').$city)),
                'distanceKm' => round((float) $property->distance_km, 2),
            ];
        })->values();

        return response()->json([
            'neighborhood' => [
                'id' => (int) $neighborhood->id,
                'label' => (string) ($neighborhood->getTranslation('name') ?: ('Neighborhood '.$neighborhood->id)),
                'latitude' => $lat,
                'longitude' => $lng,
            ],
            'properties' => $properties,
        ]);
    }

    public function mapProperties(Request $request): JsonResponse
    {
        $requestedLang = strtolower((string) $request->query('lang', ''));
        if (in_array($requestedLang, ['en', 'ar'], true)) {
            app()->setLocale($requestedLang);
        }

        if (! Schema::hasTable('properties')) {
            return response()->json(['properties' => [], 'count' => 0]);
        }

        $query = Property::query()
            ->with('translations')
            ->where('status', true)
            ->whereNotNull('published_at')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        $north = $request->query('north');
        $south = $request->query('south');
        $east = $request->query('east');
        $west = $request->query('west');
        if (is_numeric($north) && is_numeric($south) && is_numeric($east) && is_numeric($west)) {
            $maxLat = max((float) $north, (float) $south);
            $minLat = min((float) $north, (float) $south);
            $maxLng = max((float) $east, (float) $west);
            $minLng = min((float) $east, (float) $west);

            $query->whereBetween('latitude', [$minLat, $maxLat])
                ->whereBetween('longitude', [$minLng, $maxLng]);
        }

        $this->applyMapFilters($query, $request);

        $rows = $query->orderByDesc('published_at')->limit(300)->get();

        $properties = $rows->map(function (Property $property) {
            $title = (string) ($property->getTranslation('title') ?: $property->title ?: 'Property');
            $city = (string) ($property->getTranslation('city') ?: $property->city ?: '');
            $community = (string) ($property->getTranslation('community') ?: $property->community ?: '');
            $image = collect($property->images ?? [])
                ->map(fn ($item) => $item->image ?? null)
                ->filter()
                ->map(fn ($path) => media_url((string) $path))
                ->filter(fn ($url) => filled($url))
                ->first() ?: dre_property_placeholder_image();

            return [
                'id' => (int) $property->id,
                'title' => $title,
                'price' => (float) $property->price,
                'priceLabel' => ($this->isArabicLocale() ? 'من ' : 'From ').$this->compactPrice((float) $property->price),
                'currency' => (string) ($property->currency ?: 'AED'),
                'bedrooms' => (int) ($property->bedrooms ?? 0),
                'bathrooms' => (int) ($property->bathrooms ?? 0),
                'latitude' => (float) $property->latitude,
                'longitude' => (float) $property->longitude,
                'location' => trim((string) ($community.(($community && $city) ? ', ' : '').$city)),
                'image' => $image,
            ];
        })->values();

        return response()->json([
            'properties' => $properties,
            'count' => $properties->count(),
        ]);
    }

    protected function applyMapFilters($query, Request $request): void
    {
        $location = trim((string) $request->query('location', ''));
        if ($location !== '') {
            $query->where(function ($q) use ($location) {
                $q->where('city', 'like', '%'.$location.'%')
                    ->orWhere('community', 'like', '%'.$location.'%')
                    ->orWhere('title', 'like', '%'.$location.'%');
            });
        }

        $propertyType = trim((string) $request->query('type', ''));
        if ($propertyType !== '') {
            $query->where('property_type', $propertyType);
        }

        $beds = trim((string) $request->query('beds', ''));
        if ($beds !== '' && is_numeric($beds)) {
            $query->where('bedrooms', '>=', (int) $beds);
        }

        $baths = trim((string) $request->query('baths', ''));
        if ($baths !== '' && is_numeric($baths)) {
            $query->where('bathrooms', '>=', (int) $baths);
        }

        $bedsBaths = trim((string) $request->query('beds_baths', ''));
        if ($bedsBaths !== '') {
            [$bbBeds, $bbBaths] = array_pad(explode('|', $bedsBaths, 2), 2, null);
            if (is_numeric($bbBeds)) {
                $query->where('bedrooms', '>=', (int) $bbBeds);
            }
            if (is_numeric($bbBaths)) {
                $query->where('bathrooms', '>=', (int) $bbBaths);
            }
        }

        $price = trim((string) $request->query('price', ''));
        if ($price !== '') {
            if (str_contains($price, '-')) {
                [$min, $max] = array_pad(explode('-', $price, 2), 2, null);
                if (is_numeric($min)) {
                    $query->where('price', '>=', (float) $min);
                }
                if (is_numeric($max)) {
                    $query->where('price', '<=', (float) $max);
                }
            } elseif (str_ends_with($price, '+')) {
                $min = rtrim($price, '+');
                if (is_numeric($min)) {
                    $query->where('price', '>=', (float) $min);
                }
            }
        }
    }

    protected function compactPrice(float $price): string
    {
        if ($price >= 1000000) {
            return number_format($price / 1000000, 1).'M';
        }
        if ($price >= 1000) {
            return number_format($price / 1000, 0).'K';
        }

        return number_format($price, 0);
    }

    protected function isArabicLocale(): bool
    {
        return str_starts_with((string) app()->getLocale(), 'ar');
    }
}
