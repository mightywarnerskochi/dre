<?php

namespace App\Services;

use CMS\SiteManager\Services\StaticTranslationService;

class ProjectStaticTranslationService extends StaticTranslationService
{
    public function directory(): string
    {
        return resource_path('js/locales');
    }

    public function packageDefaultEnglishPath(): string
    {
        return resource_path('js/locales/en.json');
    }
}
