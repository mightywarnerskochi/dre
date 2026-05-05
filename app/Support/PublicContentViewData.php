<?php

namespace App\Support;

use App\Models\CmsKit\About;
use App\Models\CmsKit\Blog;
use App\Models\CmsKit\MissionVision;
use App\Models\CmsKit\Neighborhood;
use App\Models\CmsKit\Property;
use App\Models\CmsKit\SectionLabel;
use App\Models\CmsKit\SuccessfulJourney;
use App\Models\CmsKit\WhyChooseUs;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PublicContentViewData
{
    public static function rentalSectionForSpa(): array
    {
        $fallback = [
            'title' => ['en' => 'Rental Properties', 'ar' => 'عقارات للإيجار'],
            'description' => [
                'en' => 'Hand-picked homes in Dubai and Sharjah. Browse featured listings and reach us instantly.',
                'ar' => 'منازل مختارة بعناية في دبي والشارقة. تصفح العقارات المميزة وتواصل معنا بسرعة.',
            ],
            'displayHome' => true,
            'properties' => [],
        ];

        $section = Schema::hasTable('section_labels')
            ? SectionLabel::query()->where('section_key', 'properties')->first()
            : null;
        $translations = is_array($section?->translations) ? $section->translations : [];

        $titleEn = trim((string) data_get($translations, 'en.title')) ?: $fallback['title']['en'];
        $titleAr = trim((string) data_get($translations, 'ar.title')) ?: $fallback['title']['ar'];
        $descEn = trim((string) data_get($translations, 'en.description')) ?: $fallback['description']['en'];
        $descAr = trim((string) data_get($translations, 'ar.description')) ?: $fallback['description']['ar'];
        $displayHome = $section ? (bool) $section->status : true;

        if (! Schema::hasTable('properties')) {
            return [
                'title' => ['en' => $titleEn, 'ar' => $titleAr],
                'description' => ['en' => $descEn, 'ar' => $descAr],
                'displayHome' => $displayHome,
                'properties' => [],
            ];
        }

        $rows = Property::query()
            ->with(['agent', 'translations', 'details'])
            ->where('status', true)
            ->ordered()
            ->take(12)
            ->get();

        $items = $rows->map(function (Property $property) {
            $allImageUrls = collect($property->images ?? [])
                ->map(fn ($image) => media_url((string) ($image->image ?? '')))
                ->filter(fn ($url) => filled($url))
                ->values();

            $imageCount = $allImageUrls->count();
            $images = $allImageUrls->take(3)->all();

            if ($images === []) {
                $images = [dre_property_placeholder_image()];
            }

            $agentPhone = trim((string) ($property->agent?->phone ?? ''));
            $whatsApp = trim((string) ($property->agent?->whatsapp_number ?? ''));
            $virtualTour = trim((string) ($property->details?->virtual_tour_url ?? ''));
            $labels = self::rentalPropertyClassificationLabels(
                (string) ($property->property_type ?? ''),
                (string) ($property->listing_type ?? ''),
                (string) ($property->category ?? ''),
            );

            return [
                'id' => (int) $property->id,
                'title' => [
                    'en' => $property->getTranslation('title', 'en') ?: ($property->title ?: 'Property'),
                    'ar' => $property->getTranslation('title', 'ar') ?: ($property->getTranslation('title', 'en') ?: ($property->title ?: 'Property')),
                ],
                'location' => [
                    'en' => self::propertyLocationForLocale($property, 'en'),
                    'ar' => self::propertyLocationForLocale($property, 'ar'),
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
                'slug' => (string) ($property->slug ?? ''),
                'url' => filled($property->slug) ? '/property-details/'.$property->slug : null,
                'phone' => $agentPhone !== '' ? 'tel:'.preg_replace('/\s+/', '', $agentPhone) : '#',
                'whatsapp' => $whatsApp !== '' ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsApp) : '#',
                'inquireUrl' => '#',
            ];
        })->values()->all();

        return [
            'title' => ['en' => $titleEn, 'ar' => $titleAr],
            'description' => ['en' => $descEn, 'ar' => $descAr],
            'displayHome' => $displayHome,
            'properties' => $items,
        ];
    }

    private static function propertyLocationForLocale(Property $property, string $locale): string
    {
        $location = collect([
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

    public static function contactSectionForSpa(): array
    {
        $fallback = [
            'title' => ['en' => 'Get in Touch', 'ar' => 'Get in Touch'],
            'subTitle' => ['en' => 'Information Request', 'ar' => 'Information Request'],
            'content' => [
                'en' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text",
                'ar' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text",
            ],
        ];

        $section = Schema::hasTable('section_labels')
            ? SectionLabel::query()->where('section_key', 'contact')->first()
            : null;
        $translations = is_array($section?->translations) ? $section->translations : [];

        $value = static function (string $lang, string $field, string $fallbackValue) use ($translations): string {
            $raw = trim((string) data_get($translations, "{$lang}.{$field}"));

            return $raw !== '' ? $raw : $fallbackValue;
        };

        return [
            'title' => [
                'en' => $value('en', 'title', $fallback['title']['en']),
                'ar' => $value('ar', 'title', $fallback['title']['ar']),
            ],
            'subTitle' => [
                'en' => $value('en', 'sub_title', $fallback['subTitle']['en']),
                'ar' => $value('ar', 'sub_title', $fallback['subTitle']['ar']),
            ],
            'content' => [
                'en' => $value('en', 'content', $fallback['content']['en']),
                'ar' => $value('ar', 'content', $fallback['content']['ar']),
            ],
        ];
    }

    /**
     * Localized labels for home rental cards (matches CMS property / listing type config).
     *
     * @return array{propertyType: array{en: string, ar: string}, listingType: array{en: string, ar: string}}
     */
    private static function rentalPropertyClassificationLabels(string $propertyType, string $listingType, string $category = ''): array
    {
        $propertyTypes = config('cms-kit.database.properties.property_types', []);
        $listingTypes = config('cms-kit.database.properties.listing_types', []);
        $categories = config('cms-kit.database.properties.categories', []);

        $ptKey = trim($propertyType);
        $propertyTypeEn = '';
        $propertyTypeAr = '';
        if ($ptKey !== '') {
            $opt = $propertyTypes[$ptKey] ?? null;
            if (is_array($opt)) {
                $propertyTypeEn = trim((string) ($opt['en'] ?? '')) ?: Str::headline(str_replace(['-', '_'], ' ', $ptKey));
                $propertyTypeAr = trim((string) ($opt['ar'] ?? '')) ?: $propertyTypeEn;
            } elseif (is_string($opt) && trim($opt) !== '') {
                $t = trim($opt);
                $propertyTypeEn = $t;
                $propertyTypeAr = $t;
            } else {
                $propertyTypeEn = Str::headline(str_replace(['-', '_'], ' ', $ptKey));
                $propertyTypeAr = $propertyTypeEn;
            }
        }

        $ltKey = trim($listingType);
        $listingTypeEn = '';
        $listingTypeAr = '';
        if ($ltKey !== '') {
            $opt = $listingTypes[$ltKey] ?? null;
            if (is_array($opt)) {
                $listingTypeEn = trim((string) ($opt['en'] ?? '')) ?: Str::headline(str_replace(['-', '_'], ' ', $ltKey));
                $listingTypeAr = trim((string) ($opt['ar'] ?? '')) ?: $listingTypeEn;
            } elseif (is_string($opt) && trim($opt) !== '') {
                $t = trim($opt);
                $listingTypeEn = $t;
                $listingTypeAr = $t;
            } else {
                $listingTypeEn = Str::headline(str_replace(['-', '_'], ' ', $ltKey));
                $listingTypeAr = $listingTypeEn;
            }
        }

        return [
            'propertyType' => ['en' => $propertyTypeEn, 'ar' => $propertyTypeAr],
            'listingType' => ['en' => $listingTypeEn, 'ar' => $listingTypeAr],
            'category' => self::localizedOptionLabel($categories, $category),
        ];
    }

    private static function localizedOptionLabel(array $options, string $key): array
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

    public static function homeAboutForSpa(): array
    {
        if (! Schema::hasTable('abouts')) {
            return [
                'isAvailable' => false,
                'eyebrow' => ['en' => '', 'ar' => ''],
                'title' => ['en' => '', 'ar' => ''],
                'body' => ['en' => '', 'ar' => ''],
                'readMoreUrl' => '/about',
                'image' => '',
                'imageAlt' => '',
            ];
        }

        $about = About::query()->where('status', true)->first();
        if (! $about) {
            return [
                'isAvailable' => false,
                'eyebrow' => ['en' => '', 'ar' => ''],
                'title' => ['en' => '', 'ar' => ''],
                'body' => ['en' => '', 'ar' => ''],
                'readMoreUrl' => '/about',
                'image' => '',
                'imageAlt' => '',
            ];
        }

        $translations = is_array($about->translations) ? $about->translations : [];
        $displayHome = (bool) data_get($translations, '_meta.display_home', true);
        $homeImage = filled($about->home_image) ? media_url((string) $about->home_image) : null;
        $titleEn = trim((string) data_get($translations, 'en.title'));
        $titleAr = trim((string) data_get($translations, 'ar.title'));
        $bodyEn = trim(strip_tags((string) data_get($translations, 'en.description')));
        $bodyAr = trim(strip_tags((string) data_get($translations, 'ar.description')));
        $subtitleEn = trim((string) data_get($translations, 'en.subtitle'));
        $subtitleAr = trim((string) data_get($translations, 'ar.subtitle'));

        $hasText = $titleEn !== '' || $titleAr !== '' || $bodyEn !== '' || $bodyAr !== '';
        $hasImage = filled($homeImage);

        return [
            'isAvailable' => $displayHome && $hasText && $hasImage,
            'eyebrow' => ['en' => $subtitleEn, 'ar' => $subtitleAr],
            'title' => [
                'en' => $titleEn,
                'ar' => $titleAr,
            ],
            'body' => [
                'en' => $bodyEn,
                'ar' => $bodyAr,
            ],
            'readMoreUrl' => '/about',
            'image' => $homeImage ?: '',
            'imageAlt' => trim((string) ($about->home_image_alt ?? '')),
        ];
    }

    public static function neighborhoodsForSpa(): array
    {
        $fallback = [
            'title' => ['en' => 'Explore Neighborhoods', 'ar' => 'استكشف الأحياء'],
            'description' => [
                'en' => 'Discover communities we serve across the Emirates.',
                'ar' => 'اكتشف المجتمعات التي نخدمها في جميع أنحاء الإمارات.',
            ],
            'items' => [],
            'endpointBase' => url('/home/neighborhoods'),
        ];

        $titleEn = $fallback['title']['en'];
        $titleAr = $fallback['title']['ar'];
        $descEn = $fallback['description']['en'];
        $descAr = $fallback['description']['ar'];
        $displayHome = true;

        if (Schema::hasTable('section_labels')) {
            $section = SectionLabel::query()->where('section_key', 'neighborhoods')->first();
            $translations = is_array($section?->translations) ? $section->translations : [];
            $titleEn = trim((string) data_get($translations, 'en.title')) ?: $titleEn;
            $titleAr = trim((string) data_get($translations, 'ar.title')) ?: $titleAr;
            $descEn = trim((string) data_get($translations, 'en.description')) ?: $descEn;
            $descAr = trim((string) data_get($translations, 'ar.description')) ?: $descAr;
            $displayHome = (bool) data_get($section?->extra_fields, 'display_home', true);
        }

        $items = [];
        if (Schema::hasTable('neighborhoods')) {
            $items = Neighborhood::query()
                ->where('status', true)
                ->orderBy('order_index')
                ->get()
                ->map(function (Neighborhood $n) {
                    $translations = is_array($n->translations) ? $n->translations : [];
                    $labelEn = trim((string) data_get($translations, 'en.name'));
                    $labelAr = trim((string) data_get($translations, 'ar.name'));

                    return [
                        'id' => (int) $n->id,
                        'label' => [
                            'en' => $labelEn !== '' ? $labelEn : ('Neighborhood '.$n->id),
                            'ar' => $labelAr !== '' ? $labelAr : ($labelEn !== '' ? $labelEn : ('الحي '.$n->id)),
                        ],
                        'latitude' => (float) $n->latitude,
                        'longitude' => (float) $n->longitude,
                    ];
                })
                ->values()
                ->all();
        }

        return [
            'title' => ['en' => $titleEn, 'ar' => $titleAr],
            'description' => ['en' => $descEn, 'ar' => $descAr],
            'items' => $items,
            'endpointBase' => url('/home/neighborhoods'),
            'displayHome' => $displayHome,
        ];
    }

    public static function aboutPageForSpa(): array
    {
        $fallbackGallery = [
            ['src' => asset('images/dre/about-building.jpg'), 'alt' => ''],
            ['src' => dre_property_placeholder_image(), 'alt' => ''],
            ['src' => dre_property_placeholder_image(), 'alt' => ''],
            ['src' => dre_property_placeholder_image(), 'alt' => ''],
        ];
        $data = [
            'hero' => [
                'title' => ['en' => 'About Us', 'ar' => 'من نحن'],
                'breadcrumb' => ['en' => 'About Us', 'ar' => 'من نحن'],
                'backgroundImage' => asset('images/inner-banner.jpg'),
            ],
            'intro' => [
                'eyebrow' => ['en' => 'About Us', 'ar' => 'من نحن'],
                'title' => ['en' => 'Welcome to Distinguished Real Estate', 'ar' => 'مرحبا بكم في ديستنغويشد العقارية'],
                'bodyHtml' => ['en' => '', 'ar' => ''],
                'gallery' => $fallbackGallery,
            ],
            'missionVision' => [
                'title' => ['en' => 'Mission, vision, and values', 'ar' => 'مهمتنا ورؤيتنا وقيمنا'],
                'items' => [],
            ],
            'whyChooseUs' => [
                'active' => false,
                'section' => [
                    'eyebrow' => ['en' => 'Highlights', 'ar' => 'أبرز المزايا'],
                    'title' => ['en' => 'Why Choose Us', 'ar' => 'لماذا تختارنا'],
                    'intro' => ['en' => '', 'ar' => ''],
                    'sectionImage' => null,
                    'sectionImageAlt' => '',
                ],
                'items' => [],
            ],
            'journey' => [
                'title' => ['en' => 'Our Successful Journey', 'ar' => 'رحلتنا الناجحة'],
                'items' => [],
            ],
        ];

        if (Schema::hasTable('abouts')) {
            $about = About::query()->where('status', true)->first();
            if ($about) {
                $translations = is_array($about->translations) ? $about->translations : [];
                $gallery = [];
                foreach ([1, 2, 3, 4] as $i) {
                    $path = $about->{"image_{$i}"} ?? null;
                    if (! filled($path)) {
                        continue;
                    }
                    $url = media_url((string) $path);
                    if (! filled($url)) {
                        continue;
                    }
                    $gallery[] = [
                        'src' => $url,
                        'alt' => trim((string) ($about->{"image_{$i}_alt"} ?? '')),
                    ];
                }

                $data['intro'] = [
                    'eyebrow' => [
                        'en' => trim((string) data_get($translations, 'en.subtitle')) ?: $data['intro']['eyebrow']['en'],
                        'ar' => trim((string) data_get($translations, 'ar.subtitle')) ?: $data['intro']['eyebrow']['ar'],
                    ],
                    'title' => [
                        'en' => trim((string) data_get($translations, 'en.title')) ?: $data['intro']['title']['en'],
                        'ar' => trim((string) data_get($translations, 'ar.title')) ?: $data['intro']['title']['ar'],
                    ],
                    'bodyHtml' => [
                        'en' => (string) data_get($translations, 'en.description'),
                        'ar' => (string) data_get($translations, 'ar.description'),
                    ],
                    'gallery' => $gallery ?: $fallbackGallery,
                ];
            }
        }

        if (Schema::hasTable('mission_visions')) {
            $rows = MissionVision::query()->where('status', true)->orderBy('order_index')->take(3)->get();
            $items = [];
            foreach ($rows as $idx => $row) {
                $imageUrl = filled($row->image) ? media_url((string) $row->image) : null;
                $items[] = [
                    'index' => str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT),
                    'title' => [
                        'en' => trim((string) data_get($row->translations, 'en.title')),
                        'ar' => trim((string) data_get($row->translations, 'ar.title')),
                    ],
                    'body' => [
                        'en' => trim((string) data_get($row->translations, 'en.description')),
                        'ar' => trim((string) data_get($row->translations, 'ar.description')),
                    ],
                    'image' => $imageUrl,
                    'variant' => $idx === 1 && filled($imageUrl) ? 'overlay' : 'plain',
                ];
            }
            $data['missionVision']['items'] = $items;
        }

        $aboutGallerySources = collect($data['intro']['gallery'])->pluck('src')->filter()->values()->all();
        if (Schema::hasTable('section_labels')) {
            $section = SectionLabel::query()->where('section_key', 'why_choose_us')->first();
            if ($section) {
                $sectionImage = filled($section->section_image) ? (media_url((string) $section->section_image) ?: null) : null;
                $collage = [];
                if ($sectionImage) {
                    $collage[] = $sectionImage;
                }
                foreach ($aboutGallerySources as $src) {
                    if (count($collage) >= 3) {
                        break;
                    }
                    if (! in_array($src, $collage, true)) {
                        $collage[] = $src;
                    }
                }

                $data['whyChooseUs']['section'] = [
                    'eyebrow' => [
                        'en' => trim((string) data_get($section->translations, 'en.subtitle')) ?: $data['whyChooseUs']['section']['eyebrow']['en'],
                        'ar' => trim((string) data_get($section->translations, 'ar.subtitle')) ?: $data['whyChooseUs']['section']['eyebrow']['ar'],
                    ],
                    'title' => [
                        'en' => trim((string) data_get($section->translations, 'en.title')) ?: $data['whyChooseUs']['section']['title']['en'],
                        'ar' => trim((string) data_get($section->translations, 'ar.title')) ?: $data['whyChooseUs']['section']['title']['ar'],
                    ],
                    'intro' => [
                        'en' => (string) data_get($section->translations, 'en.description'),
                        'ar' => (string) data_get($section->translations, 'ar.description'),
                    ],
                    'sectionImage' => $sectionImage,
                    'sectionImageAlt' => trim((string) ($section->section_image_alt ?? '')),
                    'collage' => $collage,
                ];
                $data['whyChooseUs']['active'] = (bool) $section->status;
            }
        }

        if (Schema::hasTable('why_choose_us_items')) {
            $rows = WhyChooseUs::query()->where('status', true)->orderBy('order_index')->get();
            $items = [];
            foreach ($rows as $idx => $row) {
                $items[] = [
                    'index' => str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT),
                    'title' => [
                        'en' => trim((string) data_get($row->translations, 'en.title')),
                        'ar' => trim((string) data_get($row->translations, 'ar.title')),
                    ],
                    'body' => [
                        'en' => trim((string) data_get($row->translations, 'en.description')),
                        'ar' => trim((string) data_get($row->translations, 'ar.description')),
                    ],
                ];
            }
            $data['whyChooseUs']['items'] = $items;
        }

        if (Schema::hasTable('successful_journeys')) {
            $rows = SuccessfulJourney::query()->where('status', true)->orderBy('order_index')->get();
            $items = [];
            foreach ($rows as $row) {
                $img1 = filled($row->image_1) ? media_url((string) $row->image_1) : null;
                $img2 = filled($row->image_2) ? media_url((string) $row->image_2) : null;
                $items[] = [
                    'year' => trim((string) ($row->year ?? '')),
                    'bodyHtml' => [
                        'en' => (string) data_get($row->translations, 'en.description'),
                        'ar' => (string) data_get($row->translations, 'ar.description'),
                    ],
                    'images' => array_values(array_filter([$img1, $img2])),
                ];
            }
            $data['journey']['items'] = $items;
        }

        return $data;
    }

    /**
     * @return array{
     *   section: array<string, mixed>,
     *   items: list<array<string, mixed>>
     * }
     */
    public static function newsAndInsightsForSpa(): array
    {
        $section = self::sectionData();
        $displayHome = (bool) data_get($section, 'displayHome', true);
        $items = self::blogItems(5, false);

        return [
            'section' => $section,
            'items' => $displayHome ? $items : [],
        ];
    }

    /**
     * Full insights listing + details payload.
     *
     * @return array{
     *   section: array<string, mixed>,
     *   items: list<array<string, mixed>>
     * }
     */
    public static function insightsForSpa(): array
    {
        return [
            'section' => self::sectionData(),
            'items' => self::blogItems(null, true),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function sectionData(): array
    {
        $fallback = [
            'listingTitle' => ['en' => 'Insights', 'ar' => 'Insights'],
            'title' => ['en' => 'News & Insights', 'ar' => 'الأخبار والرؤى'],
            'description' => ['en' => 'News & Insights', 'ar' => 'الأخبار والرؤى'],
        ];

        if (! Schema::hasTable('section_labels')) {
            return $fallback;
        }

        $section = SectionLabel::query()->where('section_key', 'blogs')->first();
        if (! $section) {
            return $fallback;
        }

        $translations = is_array($section->translations) ? $section->translations : [];
        $titleEn = trim((string) ($translations['en']['title'] ?? ''));
        $titleAr = trim((string) ($translations['ar']['title'] ?? ''));
        $listingTitleEn = trim((string) ($translations['en']['listing_title'] ?? ''));
        $listingTitleAr = trim((string) ($translations['ar']['listing_title'] ?? ''));
        $descEn = trim((string) ($translations['en']['description'] ?? ''));
        $descAr = trim((string) ($translations['ar']['description'] ?? ''));

        return [
            'title' => [
                'en' => $titleEn !== '' ? $titleEn : $fallback['title']['en'],
                'ar' => $titleAr !== '' ? $titleAr : $fallback['title']['ar'],
            ],
            'listingTitle' => [
                'en' => $listingTitleEn !== '' ? $listingTitleEn : ($titleEn !== '' ? $titleEn : $fallback['listingTitle']['en']),
                'ar' => $listingTitleAr !== '' ? $listingTitleAr : ($titleAr !== '' ? $titleAr : $fallback['listingTitle']['ar']),
            ],
            'description' => [
                'en' => $descEn !== '' ? $descEn : $fallback['description']['en'],
                'ar' => $descAr !== '' ? $descAr : $fallback['description']['ar'],
            ],
            'displayHome' => (bool) data_get($section->extra_fields, 'display_home', true),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function blogItems(?int $limit = null, bool $includeContent = false): array
    {
        if (! Schema::hasTable('blogs')) {
            return [];
        }

        $query = Blog::query()
            ->where('status', true)
            ->orderBy('order_index')
            ->orderByDesc('published_at');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()
            ->map(function (Blog $blog) use ($includeContent): array {
                $placeholder = asset('public/images/news/blog-placeholder-new.png');
                $translations = is_array($blog->translations) ? $blog->translations : [];
                $extraFieldsEn = is_array(data_get($translations, 'en.extra_fields')) ? data_get($translations, 'en.extra_fields') : [];
                $extraFieldsAr = is_array(data_get($translations, 'ar.extra_fields')) ? data_get($translations, 'ar.extra_fields') : [];
                $titleEn = trim((string) ($translations['en']['title'] ?? ''));
                $titleAr = trim((string) ($translations['ar']['title'] ?? ''));
                $contentEn = trim(strip_tags((string) ($translations['en']['content'] ?? '')));
                $contentAr = trim(strip_tags((string) ($translations['ar']['content'] ?? '')));
                $contentHtmlEn = (string) ($translations['en']['content'] ?? '');
                $contentHtmlAr = (string) ($translations['ar']['content'] ?? '');

                $image = $blog->feature_image ? media_url((string) $blog->feature_image) : null;
                $image = filled($image) ? $image : $placeholder;
                $detailImage = $blog->detail_image ? media_url((string) $blog->detail_image) : null;
                $detailImage = filled($detailImage) ? $detailImage : $placeholder;
                $image3 = $blog->image_3 ? media_url((string) $blog->image_3) : null;
                $image4 = $blog->image_4 ? media_url((string) $blog->image_4) : null;

                $authorEn = trim((string) (data_get($extraFieldsEn, 'published_by')
                    ?? data_get($blog->metadata, 'author_en')
                    ?? data_get($blog->metadata, 'author')
                    ?? data_get($blog->extra_fields, 'author.en')
                    ?? data_get($blog->extra_fields, 'author')
                    ?? 'DRE Admin'));
                $authorAr = trim((string) (data_get($extraFieldsAr, 'published_by')
                    ?? data_get($blog->metadata, 'author_ar')
                    ?? data_get($blog->metadata, 'author')
                    ?? data_get($blog->extra_fields, 'author.ar')
                    ?? data_get($blog->extra_fields, 'author')
                    ?? ''));
                $typeEn = trim((string) (data_get($extraFieldsEn, 'type')
                    ?? data_get($blog->extra_fields, 'type.en')
                    ?? data_get($blog->extra_fields, 'type')
                    ?? data_get($blog->extra_fields, 'category.en')
                    ?? data_get($blog->extra_fields, 'category')
                    ?? data_get($blog->metadata, 'category')
                    ?? ''));
                $typeAr = trim((string) (data_get($extraFieldsAr, 'type')
                    ?? data_get($blog->extra_fields, 'type.ar')
                    ?? data_get($blog->extra_fields, 'type')
                    ?? data_get($blog->extra_fields, 'category.ar')
                    ?? data_get($blog->extra_fields, 'category')
                    ?? data_get($blog->metadata, 'category')
                    ?? ''));
                $secondDescriptionEn = (string) (data_get($extraFieldsEn, 'second_description')
                    ?? data_get($blog->extra_fields, 'second_description.en')
                    ?? data_get($blog->extra_fields, 'second_description')
                    ?? '');
                $secondDescriptionAr = (string) (data_get($extraFieldsAr, 'second_description')
                    ?? data_get($blog->extra_fields, 'second_description.ar')
                    ?? data_get($blog->extra_fields, 'second_description')
                    ?? '');

                $payload = [
                    'id' => (int) $blog->id,
                    'slug' => (string) ($blog->slug ?? ''),
                    'url' => filled($blog->slug) ? url('/insights-details/'.$blog->slug) : '#',
                    'author' => [
                        'en' => $authorEn !== '' ? $authorEn : 'DRE Admin',
                        'ar' => $authorAr !== '' ? $authorAr : ($authorEn !== '' ? $authorEn : 'DRE Admin'),
                    ],
                    'publishedAt' => optional($blog->published_at)->toDateString(),
                    'image' => $image,
                    'detailImage' => $detailImage,
                    'image3' => filled($image3) ? $image3 : null,
                    'image4' => filled($image4) ? $image4 : null,
                    'title' => [
                        'en' => $titleEn,
                        'ar' => $titleAr,
                    ],
                    'excerpt' => [
                        'en' => mb_substr($contentEn, 0, 140),
                        'ar' => mb_substr($contentAr, 0, 140),
                    ],
                    'type' => [
                        'en' => $typeEn,
                        'ar' => $typeAr,
                    ],
                    'category' => [
                        'en' => $typeEn,
                        'ar' => $typeAr,
                    ],
                ];

                if ($includeContent) {
                    $payload['content'] = [
                        'en' => $contentHtmlEn,
                        'ar' => $contentHtmlAr,
                    ];
                    $payload['secondDescription'] = [
                        'en' => $secondDescriptionEn,
                        'ar' => $secondDescriptionAr,
                    ];
                }

                return $payload;
            })
            ->values()
            ->all();
    }
}
