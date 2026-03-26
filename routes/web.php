<?php

use App\Http\Controllers\CmsKit\AboutController;
use App\Http\Controllers\CmsKit\AgentController;
use App\Http\Controllers\CmsKit\MissionVisionController;
use App\Http\Controllers\CmsKit\NearbyPlaceController;
use App\Http\Controllers\CmsKit\PropertyController;
use App\Http\Controllers\CmsKit\SuccessfulJourneyController;
use App\Http\Controllers\CmsKit\WhyChooseUsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web'])->group(function () {
    Route::prefix(config('cms-kit.common.auth.prefix', 'admin'))->middleware(['cms.auth'])->group(function () {
        Route::middleware(['cms.permission:about.view'])->group(function () {
            if (config('cms-kit.common.modules.about', true)) {
                Route::get('/about', [AboutController::class, 'edit'])->name('cms.about.edit');
                Route::put('/about', [AboutController::class, 'update'])->name('cms.about.update')->middleware('cms.permission:about.edit');
            }
        });

        Route::middleware(['cms.permission:mission-vision.view'])->group(function () {
            if (config('cms-kit.common.modules.mission-vision', true)) {
                Route::get('/mission-vision', [MissionVisionController::class, 'index'])->name('cms.mission-vision.index');
                Route::get('/mission-vision/create', [MissionVisionController::class, 'create'])->name('cms.mission-vision.create')->middleware('cms.permission:mission-vision.create');
                Route::post('/mission-vision', [MissionVisionController::class, 'store'])->name('cms.mission-vision.store')->middleware('cms.permission:mission-vision.create');
                Route::get('/mission-vision/{id}/edit', [MissionVisionController::class, 'edit'])->name('cms.mission-vision.edit')->middleware('cms.permission:mission-vision.edit');
                Route::put('/mission-vision/{id}', [MissionVisionController::class, 'update'])->name('cms.mission-vision.update')->middleware('cms.permission:mission-vision.edit');
                Route::delete('/mission-vision/{id}', [MissionVisionController::class, 'destroy'])->name('cms.mission-vision.destroy')->middleware('cms.permission:mission-vision.delete');
                Route::post('/mission-vision/{id}/toggle-status', [MissionVisionController::class, 'toggleStatus'])->name('cms.mission-vision.toggle-status')->middleware('cms.permission:mission-vision.edit');
                Route::post('/mission-vision/reorder', [MissionVisionController::class, 'reorder'])->name('cms.mission-vision.reorder')->middleware('cms.permission:mission-vision.edit');
                Route::post('/mission-vision/bulk-action', [MissionVisionController::class, 'bulkAction'])->name('cms.mission-vision.bulk-action')->middleware('cms.permission:mission-vision.delete');
            }
        });

        Route::middleware(['cms.permission:why-choose-us.view'])->group(function () {
            if (config('cms-kit.common.modules.why-choose-us', true)) {
                Route::get('/why-choose-us', [WhyChooseUsController::class, 'index'])->name('cms.why-choose-us.index');
                Route::post('/why-choose-us/section', [WhyChooseUsController::class, 'updateSection'])->name('cms.why-choose-us.update-section')->middleware('cms.permission:why-choose-us.edit');
                Route::get('/why-choose-us/create', [WhyChooseUsController::class, 'create'])->name('cms.why-choose-us.create')->middleware('cms.permission:why-choose-us.create');
                Route::post('/why-choose-us', [WhyChooseUsController::class, 'store'])->name('cms.why-choose-us.store')->middleware('cms.permission:why-choose-us.create');
                Route::get('/why-choose-us/{id}/edit', [WhyChooseUsController::class, 'edit'])->name('cms.why-choose-us.edit')->middleware('cms.permission:why-choose-us.edit');
                Route::put('/why-choose-us/{id}', [WhyChooseUsController::class, 'update'])->name('cms.why-choose-us.update')->middleware('cms.permission:why-choose-us.edit');
                Route::delete('/why-choose-us/{id}', [WhyChooseUsController::class, 'destroy'])->name('cms.why-choose-us.destroy')->middleware('cms.permission:why-choose-us.delete');
                Route::post('/why-choose-us/{id}/toggle-status', [WhyChooseUsController::class, 'toggleStatus'])->name('cms.why-choose-us.toggle-status')->middleware('cms.permission:why-choose-us.edit');
                Route::post('/why-choose-us/reorder', [WhyChooseUsController::class, 'reorder'])->name('cms.why-choose-us.reorder')->middleware('cms.permission:why-choose-us.edit');
                Route::post('/why-choose-us/bulk-action', [WhyChooseUsController::class, 'bulkAction'])->name('cms.why-choose-us.bulk-action')->middleware('cms.permission:why-choose-us.delete');
            }
        });

        Route::middleware(['cms.permission:successful-journeys.view'])->group(function () {
            if (config('cms-kit.common.modules.successful-journeys', true)) {
                Route::get('/successful-journeys', [SuccessfulJourneyController::class, 'index'])->name('cms.successful-journeys.index');
                Route::get('/successful-journeys/create', [SuccessfulJourneyController::class, 'create'])->name('cms.successful-journeys.create')->middleware('cms.permission:successful-journeys.create');
                Route::post('/successful-journeys', [SuccessfulJourneyController::class, 'store'])->name('cms.successful-journeys.store')->middleware('cms.permission:successful-journeys.create');
                Route::get('/successful-journeys/{id}/edit', [SuccessfulJourneyController::class, 'edit'])->name('cms.successful-journeys.edit')->middleware('cms.permission:successful-journeys.edit');
                Route::put('/successful-journeys/{id}', [SuccessfulJourneyController::class, 'update'])->name('cms.successful-journeys.update')->middleware('cms.permission:successful-journeys.edit');
                Route::delete('/successful-journeys/{id}', [SuccessfulJourneyController::class, 'destroy'])->name('cms.successful-journeys.destroy')->middleware('cms.permission:successful-journeys.delete');
                Route::post('/successful-journeys/{id}/toggle-status', [SuccessfulJourneyController::class, 'toggleStatus'])->name('cms.successful-journeys.toggle-status')->middleware('cms.permission:successful-journeys.edit');
                Route::post('/successful-journeys/reorder', [SuccessfulJourneyController::class, 'reorder'])->name('cms.successful-journeys.reorder')->middleware('cms.permission:successful-journeys.edit');
                Route::post('/successful-journeys/bulk-action', [SuccessfulJourneyController::class, 'bulkAction'])->name('cms.successful-journeys.bulk-action')->middleware('cms.permission:successful-journeys.delete');
            }
        });

        Route::middleware(['cms.permission:property.view'])->group(function () {
            if (config('cms-kit.common.modules.properties', true)) {
                Route::get('/properties', [PropertyController::class, 'index'])->name('cms.properties.index');
                Route::get('/properties/create', [PropertyController::class, 'create'])->name('cms.properties.create')->middleware('cms.permission:property.create');
                Route::post('/properties', [PropertyController::class, 'store'])->name('cms.properties.store')->middleware('cms.permission:property.create');
                Route::get('/properties/{id}/edit', [PropertyController::class, 'edit'])->name('cms.properties.edit')->middleware('cms.permission:property.edit');
                Route::put('/properties/{id}', [PropertyController::class, 'update'])->name('cms.properties.update')->middleware('cms.permission:property.edit');
                Route::delete('/properties/{id}', [PropertyController::class, 'destroy'])->name('cms.properties.destroy')->middleware('cms.permission:property.delete');
                Route::post('/properties/{id}/toggle-status', [PropertyController::class, 'toggleStatus'])->name('cms.properties.toggle-status')->middleware('cms.permission:property.status');
                Route::post('/properties/{id}/reorder', [PropertyController::class, 'reorder'])->name('cms.properties.reorder')->middleware('cms.permission:property.order');
                Route::post('/properties/bulk-action', [PropertyController::class, 'bulkAction'])->name('cms.properties.bulk-action')->middleware('cms.permission:property.delete');
            }
        });

        Route::middleware(['cms.permission:agents.view'])->group(function () {
            if (config('cms-kit.common.modules.agents', true)) {
                Route::get('/agents', [AgentController::class, 'index'])->name('cms.agents.index');
                Route::get('/agents/create', [AgentController::class, 'create'])->name('cms.agents.create')->middleware('cms.permission:agents.create');
                Route::post('/agents', [AgentController::class, 'store'])->name('cms.agents.store')->middleware('cms.permission:agents.create');
                Route::get('/agents/{id}/edit', [AgentController::class, 'edit'])->name('cms.agents.edit')->middleware('cms.permission:agents.edit');
                Route::put('/agents/{id}', [AgentController::class, 'update'])->name('cms.agents.update')->middleware('cms.permission:agents.edit');
                Route::delete('/agents/{id}', [AgentController::class, 'destroy'])->name('cms.agents.destroy')->middleware('cms.permission:agents.delete');
                Route::post('/agents/{id}/toggle-status', [AgentController::class, 'toggleStatus'])->name('cms.agents.toggle-status')->middleware('cms.permission:agents.edit');
                Route::post('/agents/bulk-action', [AgentController::class, 'bulkAction'])->name('cms.agents.bulk-action')->middleware('cms.permission:agents.delete');
            }
        });

        Route::middleware(['cms.permission:nearby-places.view'])->group(function () {
            if (config('cms-kit.common.modules.nearby-places', true)) {
                Route::get('/nearby-places', [NearbyPlaceController::class, 'index'])->name('cms.nearby-places.index');
                Route::get('/nearby-places/create', [NearbyPlaceController::class, 'create'])->name('cms.nearby-places.create')->middleware('cms.permission:nearby-places.create');
                Route::post('/nearby-places', [NearbyPlaceController::class, 'store'])->name('cms.nearby-places.store')->middleware('cms.permission:nearby-places.create');
                Route::get('/nearby-places/{id}/edit', [NearbyPlaceController::class, 'edit'])->name('cms.nearby-places.edit')->middleware('cms.permission:nearby-places.edit');
                Route::put('/nearby-places/{id}', [NearbyPlaceController::class, 'update'])->name('cms.nearby-places.update')->middleware('cms.permission:nearby-places.edit');
                Route::delete('/nearby-places/{id}', [NearbyPlaceController::class, 'destroy'])->name('cms.nearby-places.destroy')->middleware('cms.permission:nearby-places.delete');
                Route::post('/nearby-places/{id}/toggle-status', [NearbyPlaceController::class, 'toggleStatus'])->name('cms.nearby-places.toggle-status')->middleware('cms.permission:nearby-places.edit');
                Route::post('/nearby-places/bulk-action', [NearbyPlaceController::class, 'bulkAction'])->name('cms.nearby-places.bulk-action')->middleware('cms.permission:nearby-places.delete');
            }
        });
    });
});
