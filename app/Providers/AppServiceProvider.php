<?php

namespace App\Providers;

use App\Models\CmsKit\Language;
use App\Observers\LanguageObserver;
use App\Services\ProjectStaticTranslationService;
use CMS\SiteManager\Services\StaticTranslationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StaticTranslationService::class, ProjectStaticTranslationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\CmsKit\Property::observe(\App\Observers\PropertyObserver::class);
        Language::observe(LanguageObserver::class);
    }
}
