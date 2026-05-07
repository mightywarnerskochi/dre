<?php

use App\Http\Controllers\PropertyPageController;
use App\Support\LocaleJsonManager;
use App\Support\PublicSpaBootData;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/public/bootstrap', function (LocaleJsonManager $localeJsonManager) {
        return response()
            ->json(PublicSpaBootData::apiPayload($localeJsonManager))
            ->header('Cache-Control', 'public, max-age=60, stale-while-revalidate=300');
    });

    Route::get('/properties/map-markers', [PropertyPageController::class, 'apiMapMarkers']);
    Route::get('/properties/filter-options', [PropertyPageController::class, 'apiFilterOptions']);
    Route::get('/properties/by-slug/{slug}', [PropertyPageController::class, 'apiDetailBySlug'])
        ->where('slug', '[A-Za-z0-9\-]+');
    Route::get('/properties/{id}', [PropertyPageController::class, 'apiDetail'])
        ->whereNumber('id');
    Route::get('/properties', [PropertyPageController::class, 'apiList']);
});
