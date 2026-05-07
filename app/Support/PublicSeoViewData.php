<?php

namespace App\Support;

use App\Models\CmsKit\Metadata;
use Illuminate\Support\Facades\Schema;

class PublicSeoViewData
{
    /**
     * @return array{pages: array<string, array<string, mixed>>}
     */
    public static function forSpa(): array
    {
        return [
            'pages' => self::pageMetadata(),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function pageMetadata(): array
    {
        if (! Schema::hasTable('metadata')) {
            return [];
        }

        return Metadata::query()
            ->get()
            ->mapWithKeys(function (Metadata $row): array {
                $key = trim((string) $row->page_key);
                if ($key === '') {
                    return [];
                }

                return [
                    $key => [
                        'pageName' => self::normalizeTranslatable($row->page_name),
                        'canonicalUrl' => self::normalizeTranslatable($row->canonical_url),
                        'metaTitle' => self::normalizeTranslatable($row->meta_title),
                        'metaDescription' => self::normalizeTranslatable($row->meta_description),
                        'metaKeywords' => self::normalizeTranslatable($row->meta_keywords),
                        'ogTitle' => self::normalizeTranslatable($row->og_title),
                        'ogDescription' => self::normalizeTranslatable($row->og_description),
                        'otherMetaTags' => self::normalizeTranslatable($row->other_meta_tags),
                        'ogImage' => filled($row->og_image) ? media_url((string) $row->og_image) : null,
                    ],
                ];
            })
            ->all();
    }

    /**
     * @param  mixed  $value
     * @return array<string, string>
     */
    private static function normalizeTranslatable(mixed $value): array
    {
        if (! is_array($value)) {
            $stringValue = trim((string) $value);

            return $stringValue !== '' ? ['en' => $stringValue] : [];
        }

        $normalized = [];
        foreach ($value as $locale => $text) {
            $locale = strtolower(trim((string) $locale));
            if ($locale === '') {
                continue;
            }

            $stringValue = trim((string) $text);
            if ($stringValue === '') {
                continue;
            }

            $normalized[$locale] = $stringValue;
        }

        return $normalized;
    }
}

