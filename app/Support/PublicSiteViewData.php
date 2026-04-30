<?php

namespace App\Support;

use App\Models\CmsKit\SiteInformation;
use Illuminate\Support\Facades\Schema;

class PublicSiteViewData
{
    /**
     * @return array{phone1: ?string, phone2: ?string, email: ?string, social: list<array{network: string, href: string}>}
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
     * @return array{phone1: ?string, phone2: ?string, email: ?string, social: list<array{network: string, href: string}>}
     */
    public static function fromModel(?SiteInformation $info): array
    {
        if (! $info) {
            return self::empty();
        }

        $socialRows = [
            ['network' => 'facebook', 'url' => $info->facebook],
            ['network' => 'twitter', 'url' => $info->twitter],
            ['network' => 'linkedin', 'url' => $info->linkedin],
            ['network' => 'instagram', 'url' => $info->instagram],
            ['network' => 'youtube', 'url' => $info->youtube],
        ];

        $social = collect($socialRows)
            ->map(fn (array $row) => [
                'network' => $row['network'],
                'href' => self::normalizeUrl($row['url']),
            ])
            ->filter(fn (array $row) => $row['href'] !== null)
            ->values()
            ->all();

        return [
            'phone1' => self::nonEmptyString($info->phone_1 ?? null),
            'phone2' => self::nonEmptyString($info->phone_2 ?? null),
            'email' => self::nonEmptyString($info->email_1 ?? null),
            'social' => $social,
        ];
    }

    /**
     * @return array{phone1: null, phone2: null, email: null, social: array{}}
     */
    public static function empty(): array
    {
        return [
            'phone1' => null,
            'phone2' => null,
            'email' => null,
            'social' => [],
        ];
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
