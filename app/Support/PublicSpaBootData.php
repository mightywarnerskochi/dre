<?php

namespace App\Support;

class PublicSpaBootData
{
    /**
     * Lightweight payload kept in initial HTML (needed for server-rendered head SEO tags).
     */
    public static function initialViewContent(): array
    {
        return [
            'seo' => PublicSeoViewData::forSpa(),
        ];
    }

    /**
     * Full SPA content payload (loaded via API).
     */
    public static function fullContent(): array
    {
        return [
            'hero' => PublicContentViewData::heroDataForSpa(),
            'search' => PublicContentViewData::homeSearchFiltersForSpa(),
            'rentalSection' => PublicContentViewData::rentalSectionForSpa(),
            'neighborhoods' => PublicContentViewData::neighborhoodsForSpa(),
            'newsInsights' => PublicContentViewData::newsAndInsightsForSpa(),
            'insights' => PublicContentViewData::insightsForSpa(),
            'homeAbout' => PublicContentViewData::homeAboutForSpa(),
            'aboutPage' => PublicContentViewData::aboutPageForSpa(),
            'contactSection' => PublicContentViewData::contactSectionForSpa(),
            'locationsSection' => PublicContentViewData::locationsSectionForSpa(),
            'careers' => PublicContentViewData::careersPublicForSpa(),
            'seo' => PublicSeoViewData::forSpa(),
        ];
    }

    /**
     * Full boot payload for frontend app initialization.
     */
    public static function apiPayload(LocaleJsonManager $localeManager): array
    {
        return [
            'sitePublic' => PublicSiteViewData::forSpa(),
            'contentPublic' => self::fullContent(),
            'i18nMessages' => $localeManager->allLocaleMessages(),
        ];
    }
}

