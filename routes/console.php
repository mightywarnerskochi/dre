<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\CmsKit\HomeBannerFiltersController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('home-banner-filters:refresh', function () {
    $controller = app(HomeBannerFiltersController::class);
    $controller->refreshValues();

    $this->info('Home banner filter values refreshed.');
})->purpose('Refresh cached home banner filter values');
