<?php

namespace App\Support;

use App\Models\CmsKit\Language;
use App\Models\CmsKit\SiteInformation;
use Illuminate\Support\Facades\Schema;

class PublicSiteViewData
{
    /**
     * @return array{
     *   phone1: ?string,
     *   phone2: ?string,
     *   email: ?string,
     *   logoUrl: ?string,
     *   colourLogoUrl: ?string,
     *   logoAlt: ?string,
     *   whatsappNumber: ?string,
     *   social: list<array{network: string, href: string}>,
     *   legalPages: array<string, mixed>,
     *   tracking: array{
     *     gtmContainerIds: list<string>,
     *     customHeadScript: ?string,
     *     customBodyScript: ?string
     *   }
     * }
     */
    public static function forSpa(): array
    {
        if (! Schema::hasTable('site_information')) {
            return self::empty();
        }

        $info = SiteInformation::query()->first();

        return self::fromModel($info);
    }

    /**
     * @return array{
     *   phone1: ?string,
     *   phone2: ?string,
     *   email: ?string,
     *   logoUrl: ?string,
     *   colourLogoUrl: ?string,
     *   logoAlt: ?string,
     *   whatsappNumber: ?string,
     *   social: list<array{network: string, href: string}>,
     *   legalPages: array<string, mixed>,
     *   tracking: array{
     *     gtmContainerIds: list<string>,
     *     customHeadScript: ?string,
     *     customBodyScript: ?string
     *   }
     * }
     */
    public static function fromModel(?SiteInformation $info): array
    {
        if (! $info) {
            return self::empty();
        }

        $extraFields = is_array($info->extra_fields) ? $info->extra_fields : [];

        $socialRows = [
            ['network' => 'facebook', 'url' => $info->facebook],
            ['network' => 'twitter', 'url' => $info->twitter],
            ['network' => 'linkedin', 'url' => $info->linkedin],
            ['network' => 'instagram', 'url' => $info->instagram],
            ['network' => 'youtube', 'url' => $info->youtube],
            ['network' => 'tiktok', 'url' => $info->tiktok],
            ['network' => 'snapchat', 'url' => $info->snapchat],
            ['network' => 'pinterest', 'url' => $info->pinterest],
            ['network' => 'vimeo', 'url' => $info->vimeo],
            ['network' => 'skype', 'url' => $info->skype],
            ['network' => 'whatsapp', 'url' => $info->whatsapp_social],
        ];

        $social = collect($socialRows)
            ->map(fn (array $row) => [
                'network' => $row['network'],
                'href' => self::normalizeUrl($row['url']),
            ])
            ->filter(fn (array $row) => $row['href'] !== null)
            ->values()
            ->all();

        $languages = [];
        if (Schema::hasTable('languages')) {
            $languages = Language::query()
                ->active()
                ->get()
                ->map(fn ($lang) => [
                    'name' => $lang->name,
                    'code' => $lang->code,
                    'flagImage' => filled($lang->flag_image) ? self::mediaPathUrl($lang->flag_image) : null,
                    'flagAlt' => $lang->flag_alt,
                    'isDefault' => (bool) $lang->is_default,
                ])
                ->values()
                ->all();
        }

        return [
            'phone1' => self::nonEmptyString($info->phone_1 ?? null),
            'phone2' => self::nonEmptyString($info->phone_2 ?? null),
            'whatsappNumber' => self::nonEmptyString($info->whatsapp_number ?? null),
            'email' => self::nonEmptyString($info->email_1 ?? null),
            'logoUrl' => self::mediaPathUrl($info->logo ?? null),
            'colourLogoUrl' => self::mediaPathUrl(data_get($extraFields, 'colour_logo')),
            'logoAlt' => self::nonEmptyString($info->logo_alt ?? null) ?: self::nonEmptyString($info->company_name ?? null),
            'social' => $social,
            'legalPages' => self::legalPages($info),
            'tracking' => self::tracking($info),
            'languages' => $languages,
            'appLinks' => [
                'qrCodeUrl' => self::mediaPathUrl(data_get($extraFields, 'qr_code')),
                'googlePlayUrl' => self::normalizeUrl(data_get($extraFields, 'google_play_link')),
                'appStoreUrl' => self::normalizeUrl(data_get($extraFields, 'app_store_link')),
            ],
        ];
    }

    /**
     * @return array{
     *   phone1: null,
     *   phone2: null,
     *   email: null,
     *   logoUrl: null,
     *   colourLogoUrl: null,
     *   logoAlt: null,
     *   whatsappNumber: null,
     *   social: array{},
     *   legalPages: array{},
     *   tracking: array{
     *     gtmContainerIds: array{},
     *     customHeadScript: null,
     *     customBodyScript: null
     *   },
     *   languages: array{}
     * }
     */
    public static function empty(): array
    {
        return [
            'phone1' => null,
            'phone2' => null,
            'whatsappNumber' => null,
            'email' => null,
            'logoUrl' => null,
            'colourLogoUrl' => null,
            'logoAlt' => null,
            'social' => [],
            'legalPages' => [],
            'tracking' => [
                'gtmContainerIds' => [],
                'customHeadScript' => null,
                'customBodyScript' => null,
            ],
            'languages' => [],
            'appLinks' => [
                'qrCodeUrl' => null,
                'googlePlayUrl' => null,
                'appStoreUrl' => null,
            ],
        ];
    }

    private static function legalPages(SiteInformation $info): array
    {
        $translations = is_array($info->translations) ? $info->translations : [];
        $extraFields = is_array($info->extra_fields) ? $info->extra_fields : [];

        return [
            'privacy-policy' => [
                'title' => ['en' => 'Privacy Policy', 'ar' => 'سياسة الخصوصية'],
                'content' => [
                    'en' => (string) data_get($translations, 'en.privacy_policy', $info->privacy_policy ?? ''),
                    'ar' => (string) data_get($translations, 'ar.privacy_policy', data_get($translations, 'en.privacy_policy', $info->privacy_policy ?? '')),
                ],
            ],
            'terms-conditions' => [
                'title' => ['en' => 'Terms & Conditions', 'ar' => 'الشروط والأحكام'],
                'content' => [
                    'en' => (string) data_get($translations, 'en.terms_and_conditions', $info->terms_and_conditions ?? ''),
                    'ar' => (string) data_get($translations, 'ar.terms_and_conditions', data_get($translations, 'en.terms_and_conditions', $info->terms_and_conditions ?? '')),
                ],
            ],
            'disclaimer' => [
                'title' => ['en' => 'Disclaimer', 'ar' => 'إخلاء المسؤولية'],
                'content' => [
                    'en' => (string) data_get($translations, 'en.disclaimer', $info->disclaimer ?? ''),
                    'ar' => (string) data_get($translations, 'ar.disclaimer', data_get($translations, 'en.disclaimer', $info->disclaimer ?? '')),
                ],
            ],
            'cookie-policy' => [
                'title' => ['en' => 'Cookie Policy', 'ar' => 'سياسة ملفات تعريف الارتباط'],
                'content' => [
                    'en' => (string) data_get($translations, 'en.extra_fields.cookie_policy', data_get($extraFields, 'cookie_policy', '')),
                    'ar' => (string) data_get($translations, 'ar.extra_fields.cookie_policy', data_get($translations, 'en.extra_fields.cookie_policy', data_get($extraFields, 'cookie_policy', ''))),
                ],
            ],
        ];
    }

    private static function tracking(SiteInformation $info): array
    {
        return [
            'gtmContainerIds' => self::extractGtmContainerIds((string) ($info->gtag ?? '')),
            'customHeadScript' => self::nonEmptyString((string) ($info->custom_head_script ?? '')),
            'customBodyScript' => self::nonEmptyString((string) ($info->custom_body_script ?? '')),
        ];
    }

    /**
     * @return list<string>
     */
    private static function extractGtmContainerIds(string $raw): array
    {
        return collect(preg_split('/[\r\n,]+/', $raw) ?: [])
            ->map(fn ($id) => strtoupper(trim((string) $id)))
            ->filter(fn ($id) => preg_match('/^GTM-[A-Z0-9]+$/', $id) === 1)
            ->unique()
            ->values()
            ->all();
    }

    private static function nonEmptyString(?string $value): ?string
    {
        $t = trim((string) $value);

        return $t !== '' ? $t : null;
    }

    private static function normalizeUrl(mixed $url): ?string
    {
        $t = trim((string) $url);
        if ($t === '' || $t === '#') {
            return null;
        }
        if (! preg_match('#^https?://#i', $t)) {
            return 'https://'.$t;
        }

        return $t;
    }

    private static function mediaPathUrl(mixed $path): ?string
    {
        $t = trim((string) $path);
        if ($t === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $t)) {
            return $t;
        }
        if (function_exists('media_url')) {
            return media_url($t);
        }

        return $t;
    }

    /**
     * Follow-us block for Blade/hybrid shells (CMS kit pages).
     *
     * @return array{title: string, links: list<array{network: string, href: string, label: string}>}
     */
    public static function followUsHybridPayload(?SiteInformation $info): array
    {
        $labels = [
            'facebook' => 'Facebook',
            'twitter' => 'X',
            'linkedin' => 'LinkedIn',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
            'snapchat' => 'Snapchat',
            'pinterest' => 'Pinterest',
            'vimeo' => 'Vimeo',
            'skype' => 'Skype',
            'whatsapp' => 'WhatsApp',
        ];

        $links = collect(self::fromModel($info)['social'])
            ->map(fn (array $row) => [
                'network' => $row['network'],
                'href' => $row['href'],
                'label' => $labels[$row['network']] ?? ucfirst($row['network']),
            ])
            ->values()
            ->all();

        return [
            'title' => 'Follow Us',
            'links' => $links,
        ];
    }

    /**
     * Contact strip for dre-footer hybrid component (nullable fields).
     *
     * @return array{phone: ?string, phoneAlt: ?string, email: ?string}
     */
    public static function footerContactBlock(?SiteInformation $info): array
    {
        $m = self::fromModel($info);

        return [
            'phone' => $m['phone1'],
            'phoneAlt' => $m['phone2'],
            'email' => $m['email'],
        ];
    }
}
