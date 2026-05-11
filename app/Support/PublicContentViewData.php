<?php

namespace App\Support;

use App\Models\CmsKit\About;
use App\Models\CmsKit\Banner;
use App\Models\CmsKit\Blog;
use App\Models\CmsKit\Career;
use App\Models\CmsKit\CareerDepartment;
use App\Models\CmsKit\HomeBannerFilterDefinition;
use App\Models\CmsKit\Location;
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

        $pageTitleEn = trim((string) data_get($translations, 'en.page_title'));
        $pageTitleAr = trim((string) data_get($translations, 'ar.page_title'));
        $breadcrumbEn = trim((string) data_get($translations, 'en.breadcrumb_label'));
        $breadcrumbAr = trim((string) data_get($translations, 'ar.breadcrumb_label'));

        $heroBackground = null;
        if ($section && filled($section->banner)) {
            $heroBackground = media_url((string) $section->banner);
        }
        if (! filled($heroBackground)) {
            $heroBackground = '/images/inner-banner.jpg';
        }

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
            'pageTitle' => [
                'en' => $pageTitleEn,
                'ar' => $pageTitleAr,
            ],
            'breadcrumbLabel' => [
                'en' => $breadcrumbEn,
                'ar' => $breadcrumbAr,
            ],
            'heroBackgroundImage' => $heroBackground,
        ];
    }

    /**
     * Locations section label + active offices for the public Contact page.
     *
     * @return array{title: array{en: string, ar: string}, description: array{en: string, ar: string}, items: list<array<string, mixed>>}
     */
    public static function locationsSectionForSpa(): array
    {
        $fallbackTitle = [
            'en' => 'Office Locations',
            'ar' => 'مواقع المكاتب',
        ];

        $titleEn = $fallbackTitle['en'];
        $titleAr = $fallbackTitle['ar'];
        $descEn = '';
        $descAr = '';

        if (Schema::hasTable('section_labels')) {
            $locSection = SectionLabel::query()->where('section_key', 'locations')->first();
            $tr = is_array($locSection?->translations) ? $locSection->translations : [];
            $titleEn = trim((string) data_get($tr, 'en.title')) ?: $titleEn;
            $titleAr = trim((string) data_get($tr, 'ar.title')) ?: $titleAr;
            $descEn = trim((string) data_get($tr, 'en.description'));
            $descAr = trim((string) data_get($tr, 'ar.description'));
        }

        $items = [];
        if (Schema::hasTable('locations')) {
            $items = Location::query()
                ->active()
                ->orderBy('order_index')
                ->get()
                ->map(function (Location $loc) {
                    $tr = is_array($loc->translations) ? $loc->translations : [];
                    $titleEn = trim((string) data_get($tr, 'en.title'));
                    $titleAr = trim((string) data_get($tr, 'ar.title'));
                    $addrEn = trim((string) data_get($tr, 'en.address'));
                    $addrAr = trim((string) data_get($tr, 'ar.address'));
                    $countryEn = trim((string) data_get($tr, 'en.country'));
                    $countryAr = trim((string) data_get($tr, 'ar.country'));

                    $imageUrl = filled($loc->image) ? media_url((string) $loc->image) : null;
                    if (! filled($imageUrl)) {
                        $imageUrl = '/images/map-2.jpg';
                    }

                    $phones = is_array($loc->phone)
                        ? array_values(array_filter(array_map(static fn ($p) => trim((string) $p), $loc->phone)))
                        : [];
                    $emails = is_array($loc->emails)
                        ? array_values(array_filter(array_map(static fn ($e) => trim((string) $e), $loc->emails)))
                        : [];

                    $whatsappRaw = trim((string) ($loc->whatsapp ?? ''));
                    $faxRaw = trim((string) ($loc->fax ?? ''));
                    $mapLink = trim((string) ($loc->map_link ?? ''));

                    return [
                        'id' => (int) $loc->id,
                        'title' => [
                            'en' => $titleEn,
                            'ar' => $titleAr,
                        ],
                        'address' => [
                            'en' => self::joinLocationAddressLine($addrEn, $countryEn),
                            'ar' => self::joinLocationAddressLine($addrAr, $countryAr),
                        ],
                        'image' => $imageUrl,
                        'imageAlt' => self::fallbackAltText($loc->image_alt ?? null, $titleEn !== '' ? $titleEn : 'Office location'),
                        'phones' => $phones,
                        'emails' => $emails,
                        'whatsapp' => $whatsappRaw !== '' ? $whatsappRaw : null,
                        'fax' => $faxRaw !== '' ? $faxRaw : null,
                        'mapLink' => $mapLink !== '' ? $mapLink : null,
                    ];
                })
                ->values()
                ->all();
        }

        return [
            'title' => ['en' => $titleEn, 'ar' => $titleAr],
            'description' => ['en' => $descEn, 'ar' => $descAr],
            'items' => $items,
        ];
    }

    private static function joinLocationAddressLine(string $address, string $country): string
    {
        $parts = array_filter([trim($address), trim($country)]);

        return implode(', ', $parts);
    }

    /**
     * Careers common section settings + vacancy list for SPA listing/details.
     *
     * @return array<string, mixed>
     */
    public static function careersPublicForSpa(): array
    {
        $fallbackHero = '/images/inner-banner.jpg';
        $listingHeading = [
            'en' => 'Current Opening',
            'ar' => 'الوظائف المتاحة',
        ];
        $listingIntro = ['en' => '', 'ar' => ''];
        $filterEnabled = false;
        $filters = [];
        $hero = $fallbackHero;

        if (Schema::hasTable('section_labels')) {
            $section = SectionLabel::query()->where('section_key', 'careers')->first();
            $tr = is_array($section?->translations) ? $section->translations : [];
            $listingHeading['en'] = trim((string) data_get($tr, 'en.title')) ?: $listingHeading['en'];
            $listingHeading['ar'] = trim((string) data_get($tr, 'ar.title')) ?: $listingHeading['ar'];
            $listingIntro['en'] = (string) data_get($tr, 'en.description');
            $listingIntro['ar'] = (string) data_get($tr, 'ar.description');
            $extra = is_array($section?->extra_fields) ? $section->extra_fields : [];
            $filterEnabled = (bool) data_get($extra, 'filter_enabled', false);
            $filters = self::careersFilterPayloadForSpa($filterEnabled, $extra);
            if ($section && filled($section->banner)) {
                $bannerUrl = media_url((string) $section->banner);
                if (filled($bannerUrl)) {
                    $hero = $bannerUrl;
                }
            }
        }

        $vacancies = [];
        if (Schema::hasTable('careers')) {
            $vacancies = Career::query()
                ->active()
                ->ordered()
                ->get()
                ->map(fn (Career $career) => self::careerVacancyPayload($career))
                ->values()
                ->all();
        }

        return [
            'listingHeading' => $listingHeading,
            'listingIntroHtml' => $listingIntro,
            'filterEnabled' => $filterEnabled,
            'filters' => $filters,
            'heroBackgroundImage' => $hero,
            'vacancies' => $vacancies,
        ];
    }

    /**
     * @param  array<string, mixed>  $extraFields
     * @return list<array<string, mixed>>
     */
    private static function careersFilterPayloadForSpa(bool $filterEnabled, array $extraFields): array
    {
        if (! $filterEnabled) {
            return [];
        }

        $allowed = self::careersAllowedFilterColumns();
        $configured = config('cms-kit.database.careers.items.columns', []);

        $raw = data_get($extraFields, 'filters', []);
        if (! is_array($raw)) {
            return [];
        }

        $out = [];

        foreach ($raw as $row) {
            $key = trim((string) (data_get($row, 'key') ?? data_get($row, 'column') ?? ''));
            $key = $key === 'base' ? 'title' : $key;
            if ($key === '' || ! in_array($key, $allowed, true)) {
                continue;
            }

            if ($key !== 'title' && ($configured[$key] ?? true) !== true) {
                continue;
            }

            $values = self::careersDistinctColumnValues($key);

            $options = [];
            foreach ($values as $value) {
                $value = trim((string) $value);
                if ($value === '') {
                    continue;
                }
                $options[] = [
                    'value' => $value,
                    'label' => [
                        'en' => self::careersFilterOptionLabel($key, $value, 'en'),
                        'ar' => self::careersFilterOptionLabel($key, $value, 'ar'),
                    ],
                ];
            }

            $out[] = [
                'key' => $key,
                'label' => [
                    'en' => self::careersFilterColumnLabel($key, 'en'),
                    'ar' => self::careersFilterColumnLabel($key, 'ar'),
                ],
                'options' => $options,
            ];
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private static function careersAllowedFilterColumns(): array
    {
        $itemConfig = config('cms-kit.database.careers.items', []);

        return collect(config('cms-kit.database.careers.section.filterable_columns', ['title', 'job_type', 'department', 'location']))
            ->filter(function (string $column) use ($itemConfig) {
                $configuredCols = $itemConfig['columns'] ?? [];

                return ($itemConfig[$column] ?? true) && ($configuredCols[$column] ?? true);
            })
            ->values()
            ->all();
    }

    private static function careersFilterColumnLabel(string $column, string $locale): string
    {
        if ($locale === 'ar') {
            return match ($column) {
                'title' => 'المسمى الوظيفي',
                'job_type' => 'نوع الوظيفة',
                'department' => 'القسم',
                'location' => 'الموقع',
                'country' => 'البلد',
                default => Str::headline($column),
            };
        }

        return match ($column) {
            'title' => 'Job Title',
            'job_type' => 'Job Type',
            'department' => 'Department',
            'location' => 'Location',
            'country' => 'Country',
            default => Str::headline($column),
        };
    }

    private static function careersFilterOptionLabel(string $column, string $value, string $locale): string
    {
        if ($column === 'job_type') {
            return self::careersStaticOptionLabel('job_type_options', $value, $locale);
        }

        if ($column === 'title') {
            return self::careersLocalizedVacancyTitleForFilter($value, $locale);
        }

        if ($column === 'department') {
            return self::careersDepartmentLocalized($value, $locale);
        }

        return $value;
    }

    /**
     * @return list<string>
     */
    private static function careersDistinctColumnValues(string $column): array
    {
        if (! Schema::hasTable('careers')) {
            return [];
        }

        $column = $column === 'base' ? 'title' : $column;

        if ($column === 'title') {
            $fallbackLocale = config('app.fallback_locale', 'en');

            return Career::query()
                ->active()
                ->ordered()
                ->get()
                ->map(function (Career $career) use ($fallbackLocale) {
                    return trim((string) ($career->getTranslation('title', 'en')
                        ?: $career->getTranslation('title', $fallbackLocale)));
                })
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if ($column === 'job_type') {
            $configured = array_keys(config('cms-kit.database.careers.items.job_type_options', []));
            $stored = Career::query()
                ->active()
                ->whereNotNull('job_type')
                ->where('job_type', '!=', '')
                ->distinct()
                ->orderBy('job_type')
                ->pluck('job_type')
                ->all();

            return array_values(array_unique(array_merge($configured, $stored)));
        }

        return Career::query()
            ->active()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private static function careersLocalizedVacancyTitleForFilter(string $canonicalTitle, string $locale): string
    {
        if ($canonicalTitle === '') {
            return '';
        }

        $fallbackLocale = config('app.fallback_locale', 'en');

        $match = Career::query()
            ->active()
            ->get()
            ->first(function (Career $career) use ($canonicalTitle, $fallbackLocale) {
                $en = trim((string) $career->getTranslation('title', 'en'));
                $fallback = trim((string) $career->getTranslation('title', $fallbackLocale));

                return $canonicalTitle === $en || $canonicalTitle === $fallback;
            });

        if (! $match) {
            return $canonicalTitle;
        }

        return trim((string) $match->getTranslation('title', $locale))
            ?: trim((string) $match->getTranslation('title', $fallbackLocale))
            ?: $canonicalTitle;
    }

    private static function careersDepartmentLocalized(string $storedDepartment, string $locale): string
    {
        $storedDepartment = trim($storedDepartment);
        if ($storedDepartment === '') {
            return '';
        }

        if (! Schema::hasTable('career_departments')) {
            return $storedDepartment;
        }

        $fallbackLocale = config('app.fallback_locale', 'en');

        foreach (CareerDepartment::query()->where('status', true)->ordered()->get() as $department) {
            $tr = is_array($department->translations) ? $department->translations : [];
            $en = trim((string) data_get($tr, 'en.title'));
            $ar = trim((string) data_get($tr, 'ar.title'));

            if ($storedDepartment !== $en && $storedDepartment !== $ar) {
                continue;
            }

            $localized = trim((string) data_get($tr, "{$locale}.title"));

            return $localized !== '' ? $localized : ($en !== '' ? $en : $ar);
        }

        return $storedDepartment;
    }

    private static function careersStaticOptionLabel(string $optionsKey, ?string $value, string $locale): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $row = config("cms-kit.database.careers.items.{$optionsKey}.{$value}");
        if (is_array($row)) {
            $label = trim((string) ($row[$locale] ?? $row['en'] ?? ''));

            return $label !== '' ? $label : trim((string) ($row['en'] ?? ''));
        }

        return Str::headline(str_replace(['-', '_'], ' ', $value));
    }

    private static function careerPlainSummary(?string $html, int $max = 280): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $plain = preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $plain = is_string($plain) ? trim(str_replace("\xc2\xa0", ' ', $plain)) : '';

        return $plain !== '' ? Str::limit($plain, $max) : '';
    }

    private static function fallbackAltText(?string $value, string $fallback): string
    {
        $value = trim((string) $value);
        if ($value !== '') {
            return $value;
        }

        $fallback = trim($fallback);

        return $fallback !== '' ? $fallback : 'DRE image';
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @param  array{title?: string, description?: string, canonical_url?: string, og_image?: ?string}  $fallback
     * @return array<string, string|null>
     */
    private static function resolveEntitySeoPayload(array $metadata, array $fallback = []): array
    {
        $metaTitle = trim((string) ($metadata['meta_title'] ?? ''));
        $metaDescription = trim((string) ($metadata['meta_description'] ?? ''));
        $canonicalUrl = trim((string) ($metadata['canonical_url'] ?? ''));
        $ogTitle = trim((string) ($metadata['og_title'] ?? ''));
        $ogDescription = trim((string) ($metadata['og_description'] ?? ''));
        $ogImage = trim((string) ($metadata['og_image'] ?? ''));

        $fallbackTitle = trim((string) ($fallback['title'] ?? ''));
        $fallbackDescription = trim((string) ($fallback['description'] ?? ''));
        $fallbackCanonical = trim((string) ($fallback['canonical_url'] ?? ''));
        $fallbackOgImage = trim((string) ($fallback['og_image'] ?? ''));
        $ogImageUrl = $ogImage !== '' ? media_url($ogImage) : ($fallbackOgImage !== '' ? $fallbackOgImage : null);

        return [
            'metaTitle' => $metaTitle !== '' ? $metaTitle : $fallbackTitle,
            'metaDescription' => $metaDescription !== '' ? $metaDescription : $fallbackDescription,
            'metaKeywords' => trim((string) ($metadata['meta_keywords'] ?? '')) ?: null,
            'canonicalUrl' => $canonicalUrl !== '' ? $canonicalUrl : $fallbackCanonical,
            'ogTitle' => $ogTitle !== '' ? $ogTitle : ($metaTitle !== '' ? $metaTitle : $fallbackTitle),
            'ogDescription' => $ogDescription !== '' ? $ogDescription : ($metaDescription !== '' ? $metaDescription : $fallbackDescription),
            'ogImage' => $ogImageUrl,
            'otherMetaTags' => trim((string) ($metadata['other_meta_tags'] ?? '')) ?: null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function careerVacancyPayload(Career $c): array
    {
        $tr = is_array($c->translations) ? $c->translations : [];
        $fallbackLocale = config('app.fallback_locale', 'en');

        $titleEn = trim((string) data_get($tr, 'en.title'));
        $titleAr = trim((string) data_get($tr, 'ar.title'));

        $titleFb = trim((string) ($c->getTranslation('title', $fallbackLocale) ?: ''));
        if ($titleAr === '') {
            $titleAr = $titleEn !== '' ? $titleEn : $titleFb;
        }
        if ($titleEn === '') {
            $titleEn = $titleFb !== '' ? $titleFb : $titleAr;
        }

        $titleFilterKey = trim((string) ($c->getTranslation('title', 'en') ?: $titleEn ?: $titleAr));

        $locEn = trim((string) data_get($tr, 'en.location')) ?: trim((string) ($c->location ?? ''));
        $locAr = trim((string) data_get($tr, 'ar.location')) ?: $locEn;
        $summaryEn = self::careerPlainSummary(data_get($tr, 'en.short_description'));
        $summaryAr = self::careerPlainSummary(data_get($tr, 'ar.short_description'));

        $deptStored = trim((string) ($c->department ?? ''));
        $flagUrl = filled($c->flag_image)
            ? media_url((string) $c->flag_image)
            : '/images/career-location-placeholder.svg';
        $primaryTitle = $titleEn !== '' ? $titleEn : ($titleAr !== '' ? $titleAr : 'Career');
        $careerSeo = self::resolveEntitySeoPayload(
            is_array($c->metadata) ? $c->metadata : [],
            [
                'title' => $primaryTitle,
                'description' => $summaryEn !== '' ? $summaryEn : ($summaryAr !== '' ? $summaryAr : $primaryTitle),
                'canonical_url' => filled($c->slug) ? url('/career-details/'.$c->slug) : url('/career'),
                'og_image' => null,
            ]
        );

        return [
            'id' => (int) $c->id,
            'slug' => (string) ($c->slug ?? ''),
            'title' => ['en' => $titleEn, 'ar' => $titleAr],
            'titleFilterKey' => $titleFilterKey,
            'shortDescription' => [
                'en' => $summaryEn,
                'ar' => $summaryAr,
            ],
            'aboutHtml' => [
                'en' => (string) data_get($tr, 'en.about'),
                'ar' => (string) data_get($tr, 'ar.about'),
            ],
            'responsibilitiesHtml' => [
                'en' => (string) data_get($tr, 'en.responsibilities'),
                'ar' => (string) data_get($tr, 'ar.responsibilities'),
            ],
            'requirementsHtml' => [
                'en' => (string) data_get($tr, 'en.requirements'),
                'ar' => (string) data_get($tr, 'ar.requirements'),
            ],
            'joinTeamHtml' => [
                'en' => (string) data_get($tr, 'en.join_the_team'),
                'ar' => (string) data_get($tr, 'ar.join_the_team'),
            ],
            'jobTypeKey' => trim((string) ($c->job_type ?? '')),
            'jobTypeLabel' => [
                'en' => self::careersStaticOptionLabel('job_type_options', $c->job_type, 'en'),
                'ar' => self::careersStaticOptionLabel('job_type_options', $c->job_type, 'ar'),
            ],
            'baseKey' => trim((string) ($c->base ?? '')),
            'baseLabel' => [
                'en' => self::careersStaticOptionLabel('base_options', $c->base, 'en'),
                'ar' => self::careersStaticOptionLabel('base_options', $c->base, 'ar'),
            ],
            'departmentKey' => $deptStored,
            'departmentLabel' => [
                'en' => self::careersDepartmentLocalized($deptStored, 'en'),
                'ar' => self::careersDepartmentLocalized($deptStored, 'ar'),
            ],
            'locationKey' => trim((string) ($c->location ?? '')),
            'locationLine' => [
                'en' => $locEn,
                'ar' => $locAr,
            ],
            'flagImage' => $flagUrl,
            'flagAlt' => self::fallbackAltText($c->flag_alt ?? null, $locEn !== '' ? $locEn : 'Career location'),
            'publishedDate' => optional($c->published_date)->toDateString(),
            'seo' => $careerSeo,
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
            'imageAlt' => self::fallbackAltText($about->home_image_alt ?? null, $titleEn !== '' ? $titleEn : 'About image'),
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
            ['src' => asset('images/dre/about-building.jpg'), 'alt' => 'About image 1'],
            ['src' => dre_property_placeholder_image(), 'alt' => 'About image 2'],
            ['src' => dre_property_placeholder_image(), 'alt' => 'About image 3'],
            ['src' => dre_property_placeholder_image(), 'alt' => 'About image 4'],
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
                        'alt' => self::fallbackAltText($about->{"image_{$i}_alt"} ?? null, 'About image '.$i),
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
                    'sectionImageAlt' => self::fallbackAltText($section->section_image_alt ?? null, 'Why choose us image'),
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
                $placeholder = '/images/news/blog-placeholder-new.png';
                $translations = is_array($blog->translations) ? $blog->translations : [];
                $extraFieldsEn = is_array(data_get($translations, 'en.extra_fields')) ? data_get($translations, 'en.extra_fields') : [];
                $extraFieldsAr = is_array(data_get($translations, 'ar.extra_fields')) ? data_get($translations, 'ar.extra_fields') : [];
                $titleEn = trim((string) ($translations['en']['title'] ?? ''));
                $titleAr = trim((string) ($translations['ar']['title'] ?? ''));
                $contentEn = trim(strip_tags((string) ($translations['en']['content'] ?? '')));
                $contentAr = trim(strip_tags((string) ($translations['ar']['content'] ?? '')));
                $contentHtmlEn = (string) ($translations['en']['content'] ?? '');
                $contentHtmlAr = (string) ($translations['ar']['content'] ?? '');

                $image = self::existingBlogMediaUrl($blog->feature_image);
                $image = filled($image) ? $image : $placeholder;
                $detailImage = self::existingBlogMediaUrl($blog->detail_image);
                $image3 = self::existingBlogMediaUrl($blog->image_3);
                $image4 = self::existingBlogMediaUrl($blog->image_4);
                $primaryTitle = $titleEn !== '' ? $titleEn : ($titleAr !== '' ? $titleAr : 'Insight');
                $featureImageAlt = self::fallbackAltText($blog->feature_image_alt ?? null, $primaryTitle.' image');
                $detailImageAlt = self::fallbackAltText($blog->detail_image_alt ?? null, $primaryTitle.' image');
                $image3Alt = self::fallbackAltText($blog->image_3_alt ?? null, $primaryTitle.' image');
                $image4Alt = self::fallbackAltText($blog->image_4_alt ?? null, $primaryTitle.' image');
                $blogSeo = self::resolveEntitySeoPayload(
                    is_array($blog->metadata) ? $blog->metadata : [],
                    [
                        'title' => $primaryTitle,
                        'description' => $contentEn !== '' ? $contentEn : $primaryTitle,
                        'canonical_url' => filled($blog->slug) ? url('/insights-details/'.$blog->slug) : url('/insights'),
                        'og_image' => $detailImage,
                    ]
                );

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
                    'imageAlt' => $featureImageAlt,
                    'detailImage' => $detailImage,
                    'detailImageAlt' => $detailImageAlt,
                    'image3' => filled($image3) ? $image3 : null,
                    'image3Alt' => $image3Alt,
                    'image4' => filled($image4) ? $image4 : null,
                    'image4Alt' => $image4Alt,
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
                    'seo' => $blogSeo,
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

    public static function heroDataForSpa(): array
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
            $line1En = trim((string) ($banner->getTranslation('line_1', 'en') ?? ''));
            $line2En = trim((string) ($banner->getTranslation('line_2', 'en') ?? ''));
            $line1Ar = trim((string) ($banner->getTranslation('line_1', 'ar') ?? ''));
            $line2Ar = trim((string) ($banner->getTranslation('line_2', 'ar') ?? ''));

            if ($line1En === '' && $line2En === '') {
                $line1En = $fallbackSlide['line1'];
                $line2En = $fallbackSlide['line2'];
            } elseif ($line2En === '') {
                $words = preg_split('/\s+/', $line1En, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $midpoint = max(1, (int) ceil(count($words) / 2));
                $line1En = implode(' ', array_slice($words, 0, $midpoint));
                $line2En = implode(' ', array_slice($words, $midpoint));
            }

            if ($line1Ar === '' && $line2Ar === '') {
                $line1Ar = $line1En;
                $line2Ar = $line2En;
            } elseif ($line2Ar === '') {
                $words = preg_split('/\s+/', $line1Ar, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $midpoint = max(1, (int) ceil(count($words) / 2));
                $line1Ar = implode(' ', array_slice($words, 0, $midpoint));
                $line2Ar = implode(' ', array_slice($words, $midpoint));
            }

            $imagePath = (string) ($banner->image ?? '');
            $backgroundImage = $fallbackSlide['backgroundImage'];
            if ($imagePath !== '') {
                $backgroundImage = preg_match('/^https?:\/\//i', $imagePath)
                    ? $imagePath
                    : (media_url($imagePath) ?? $fallbackSlide['backgroundImage']);
            }

            $buttonsEn = (array) data_get($banner->translations, 'en.buttons', []);
            $buttonEn = $buttonsEn[0] ?? null;
            $buttonsAr = (array) data_get($banner->translations, 'ar.buttons', []);
            $buttonAr = $buttonsAr[0] ?? null;

            return [
                'id' => $banner->id,
                'line1' => [
                    'en' => $line1En,
                    'ar' => $line1Ar,
                ],
                'line2' => [
                    'en' => $line2En !== '' ? $line2En : $fallbackSlide['line2'],
                    'ar' => $line2Ar !== '' ? $line2Ar : $fallbackSlide['line2'],
                ],
                'backgroundImage' => $backgroundImage,
                'primaryActionLabel' => [
                    'en' => trim((string) data_get($buttonEn, 'label', $fallbackSlide['primaryActionLabel'])),
                    'ar' => trim((string) data_get($buttonAr, 'label', $fallbackSlide['primaryActionLabel'])),
                ],
                'primaryActionUrl' => trim((string) data_get($buttonEn, 'url', $fallbackSlide['primaryActionUrl'])) ?: '#rentals',
            ];
        })->values()->all();

        return ['slides' => $slides];
    }

    public static function homeSearchFiltersForSpa(): array
    {
        if (! Schema::hasTable('home_banner_filter_definitions') || ! Schema::hasTable('home_banner_filter_values')) {
            return self::fallbackSearchFilters();
        }

        $definitions = HomeBannerFilterDefinition::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get();

        if ($definitions->isEmpty()) {
            return self::fallbackSearchFilters();
        }

        $currentLocale = app()->getLocale();
        $filters = [];

        foreach ($definitions as $definition) {
            $queryParam = self::queryParamForFilter((string) $definition->key);
            if (! $queryParam) {
                continue;
            }

            $values = $definition->values()
                ->where('status', true)
                ->orderBy('sort_order')
                ->get(['value', 'label', 'translations']);

            $options = $values->map(function ($v) use ($currentLocale, $definition) {
                $translated = data_get($v->translations, $currentLocale, []);
                $label = data_get($translated, 'label') !== null
                    ? (string) data_get($translated, 'label')
                    : ($v->label ? (string) $v->label : (string) $v->value);

                $option = [
                    'value' => (string) $v->value,
                    'label' => $label,
                ];

                if ((string) $definition->key === 'location') {
                    $option['subtitle'] = (string) data_get($translated, 'subtitle', '');
                    $option['type'] = (string) data_get($translated, 'type', '');
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
                        : ($definition->ui_type ?: self::fallbackUiTypeForFilter((string) $definition->key))
                ),
                'queryParam' => $queryParam,
                'options' => $options,
            ];
        }

        if (empty($filters)) {
            return self::fallbackSearchFilters();
        }

        return ['filters' => $filters];
    }

    private static function queryParamForFilter(string $key): ?string
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

    private static function fallbackUiTypeForFilter(string $key): string
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

    private static function existingBlogMediaUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $path = (string) $path;

        if (! preg_match('#^https?://#i', $path)
            && MediaStorage::pathExistsIsCheap()
            && ! MediaStorage::exists($path)) {
            return null;
        }

        $url = media_url($path);

        return filled($url) ? $url : null;
    }

    private static function fallbackSearchFilters(): array
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
}
