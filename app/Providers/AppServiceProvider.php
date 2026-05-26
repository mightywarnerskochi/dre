<?php

namespace App\Providers;

use App\Models\CmsKit\Language;
use App\Observers\LanguageObserver;
use App\Services\SitemapService as ProjectSitemapService;
use App\Services\ProjectStaticTranslationService;
use CMS\SiteManager\Services\SitemapService as PackageSitemapService;
use CMS\SiteManager\Services\StaticTranslationService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StaticTranslationService::class, ProjectStaticTranslationService::class);
        $this->app->singleton(PackageSitemapService::class, ProjectSitemapService::class);

        if (is_file(config_path('cms/sitemap.php'))) {
            config(['cms.sitemap' => require config_path('cms/sitemap.php')]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\CmsKit\Property::observe(\App\Observers\PropertyObserver::class);
        Language::observe(LanguageObserver::class);

        $this->app->booted(function (): void {
            Route::prefix(config('cms-kit.common.auth.prefix', 'admin'))
                ->middleware(['web', 'cms.auth', 'cms.permission:sitemap.view'])
                ->group(function (): void {
                    Route::get('/sitemap/generate', [\App\Http\Controllers\CmsKit\SitemapController::class, 'generate'])
                        ->name('cms.sitemap.generate')
                        ->middleware('cms.permission:sitemap.edit');
                });
        });
    }
}
