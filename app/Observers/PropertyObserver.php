<?php

namespace App\Observers;

use App\Http\Controllers\CmsKit\HomeBannerFiltersController;
use App\Models\CmsKit\Property;

class PropertyObserver
{
    /**
     * Handle the Property "saved" event.
     */
    public function saved(Property $property): void
    {
        $this->refreshSiteFilters();
    }

    /**
     * Handle the Property "deleted" event.
     */
    public function deleted(Property $property): void
    {
        $this->refreshSiteFilters();
    }

    /**
     * Triggers the refresh logic for site filters.
     */
    protected function refreshSiteFilters(): void
    {
        try {
            (new HomeBannerFiltersController)->refreshValues();
        } catch (\Exception $e) {
            \Log::error('Failed to auto-refresh site filters after property update: ' . $e->getMessage());
        }
    }
}
