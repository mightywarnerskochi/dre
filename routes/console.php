<?php

use App\Http\Controllers\CmsKit\HomeBannerFiltersController;
use App\Services\PropertyFinderListingSyncService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('home-banner-filters:refresh', function () {
    $controller = app(HomeBannerFiltersController::class);
    $controller->refreshValues();

    $this->info('Home banner filter values refreshed.');
})->purpose('Refresh cached home banner filter values');

Artisan::command('property-finder:sync-regular', function (PropertyFinderListingSyncService $syncService) {
    $summary = $syncService->regularSync();

    $this->info(json_encode($summary, JSON_PRETTY_PRINT));
})->purpose('Run the scheduled regular Property Finder listings sync');

Artisan::command('property-finder:sync-full', function (PropertyFinderListingSyncService $syncService) {
    $summary = $syncService->fullSync();

    $this->info(json_encode($summary, JSON_PRETTY_PRINT));
})->purpose('Run the one-time full Property Finder listings sync');


foreach (['00:00', '05:00', '10:00', '15:00', '20:00'] as $time) {
    Schedule::command('property-finder:sync-regular')
        ->dailyAt($time)
        ->withoutOverlapping();
}

