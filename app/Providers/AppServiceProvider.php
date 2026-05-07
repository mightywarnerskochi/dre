<?php

namespace App\Providers;

use App\Models\CmsKit\Language;
use App\Observers\LanguageObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
