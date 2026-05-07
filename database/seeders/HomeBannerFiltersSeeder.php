<?php

namespace Database\Seeders;

use App\Http\Controllers\CmsKit\HomeBannerFiltersController;
use App\Models\CmsKit\HomeBannerFilterDefinition;
use App\Models\CmsKit\HomeBannerFilterValue;
use Illuminate\Database\Seeder;

class HomeBannerFiltersSeeder extends Seeder
{
    public function run(): void
    {
        // Seed initial Home Banner filter definitions + fallback values.
        // After deploy, go to CMS -> Home -> Filters and click "Refresh Values" to sync
        // distinct dropdown options from the current `properties` dataset.
        // Definitions (global for the Home page).
        $definitions = [
            [
                'key' => 'property_type',
                'label' => 'Property type',
                'translations' => [
                    'en' => ['label' => 'Property type'],
                    'ar' => ['label' => 'نوع العقار'],
                ],
                'ui_type' => 'dropdown',
                'source_table' => 'properties',
                'source_column' => 'property_type',
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'location',
                'label' => 'Location',
                'translations' => [
                    'en' => ['label' => 'Location'],
                    'ar' => ['label' => 'الموقع'],
                ],
                'ui_type' => 'dropdown',
                'source_table' => 'properties',
                'source_column' => 'city,community',
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'bedrooms',
                'label' => 'Beds',
                'translations' => [
                    'en' => ['label' => 'Beds'],
                    'ar' => ['label' => 'غرف نوم'],
                ],
                'ui_type' => 'dropdown',
                'source_table' => 'properties',
                'source_column' => 'bedrooms',
                'status' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'bathrooms',
                'label' => 'Baths',
                'translations' => [
                    'en' => ['label' => 'Baths'],
                    'ar' => ['label' => 'حمامات'],
                ],
                'ui_type' => 'dropdown',
                'source_table' => 'properties',
                'source_column' => 'bathrooms',
                'status' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'bed_and_baths',
                'label' => 'Beds & Baths',
                'translations' => [
                    'en' => ['label' => 'Beds & Baths'],
                    'ar' => ['label' => 'غرف نوم و حمامات'],
                ],
                'ui_type' => 'dropdown',
                'source_table' => 'properties',
                'source_column' => 'bedrooms,bathrooms',
                'status' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'price',
                'label' => 'Price range',
                'translations' => [
                    'en' => ['label' => 'Price range'],
                    'ar' => ['label' => 'نطاق السعر'],
                ],
                'ui_type' => 'dropdown',
                'source_table' => null,
                'source_column' => null, // Fixed values maintained manually in `home_banner_filter_values`.
                'status' => true,
                'sort_order' => 6,
            ],
        ];

        $definitionModels = [];
        foreach ($definitions as $def) {
            $definitionModels[$def['key']] = HomeBannerFilterDefinition::updateOrCreate(
                ['key' => $def['key']],
                $def,
            );
        }

        // Default dropdown entries ("All"/placeholder).
        HomeBannerFilterValue::updateOrCreate(
            ['filter_definition_id' => $definitionModels['property_type']->id, 'value' => ''],
            [
                'label' => 'All Type',
                'translations' => [
                    'en' => ['label' => 'All Type'],
                    'ar' => ['label' => 'كل الأنواع'],
                ],
                'sort_order' => 0,
                'status' => true,
            ],
        );
        HomeBannerFilterValue::updateOrCreate(
            ['filter_definition_id' => $definitionModels['location']->id, 'value' => ''],
            [
                'label' => 'Location',
                'translations' => [
                    'en' => ['label' => 'Location'],
                    'ar' => ['label' => 'الموقع'],
                ],
                'sort_order' => 0,
                'status' => true,
            ],
        );
        HomeBannerFilterValue::updateOrCreate(
            ['filter_definition_id' => $definitionModels['bedrooms']->id, 'value' => ''],
            [
                'label' => 'Beds',
                'translations' => [
                    'en' => ['label' => 'Beds'],
                    'ar' => ['label' => 'غرف نوم'],
                ],
                'sort_order' => 0,
                'status' => true,
            ],
        );
        HomeBannerFilterValue::updateOrCreate(
            ['filter_definition_id' => $definitionModels['bathrooms']->id, 'value' => ''],
            [
                'label' => 'Baths',
                'translations' => [
                    'en' => ['label' => 'Baths'],
                    'ar' => ['label' => 'حمامات'],
                ],
                'sort_order' => 0,
                'status' => true,
            ],
        );
        HomeBannerFilterValue::updateOrCreate(
            ['filter_definition_id' => $definitionModels['bed_and_baths']->id, 'value' => ''],
            [
                'label' => 'Beds & Baths',
                'translations' => [
                    'en' => ['label' => 'Beds & Baths'],
                    'ar' => ['label' => 'غرف نوم و حمامات'],
                ],
                'sort_order' => 0,
                'status' => true,
            ],
        );
        HomeBannerFilterValue::updateOrCreate(
            ['filter_definition_id' => $definitionModels['price']->id, 'value' => ''],
            [
                'label' => 'Min-Max Price',
                'translations' => [
                    'en' => ['label' => 'Min-Max Price'],
                    'ar' => ['label' => 'السعر (نطاق)'],
                ],
                'sort_order' => 0,
                'status' => true,
            ],
        );

        // Fallback options (used until refresh computes values from `properties`).
        $propertyTypeLabels = config('cms-kit.database.properties.property_types', []);
        foreach ($propertyTypeLabels as $value => $labelsByLang) {
            $enLabel = $labelsByLang['en'] ?? $value;
            $arLabel = $labelsByLang['ar'] ?? $value;
            HomeBannerFilterValue::updateOrCreate(
                ['filter_definition_id' => $definitionModels['property_type']->id, 'value' => (string) $value],
                [
                    'label' => $enLabel,
                    'translations' => [
                        'en' => ['label' => $enLabel],
                        'ar' => ['label' => $arLabel],
                    ],
                    'sort_order' => 1,
                    'status' => true,
                ],
            );
        }

        $fallbackLocations = [
            'dubai-marina' => 'Dubai Marina',
            'downtown' => 'Downtown Dubai',
            'palm' => 'Palm Jumeirah',
            'sharjah' => 'Sharjah — Al Majaz',
            'abu-dhabi' => 'Abu Dhabi — Saadiyat',
        ];
        $i = 1;
        foreach ($fallbackLocations as $value => $label) {
            $arLabel = $label; // fallback, will be overridden by refresh with real translations
            HomeBannerFilterValue::updateOrCreate(
                ['filter_definition_id' => $definitionModels['location']->id, 'value' => (string) $value],
                [
                    'label' => $label,
                    'translations' => [
                        'en' => ['label' => $label],
                        'ar' => ['label' => $arLabel],
                    ],
                    'sort_order' => $i,
                    'status' => true,
                ],
            );
            $i++;
        }

        $priceRanges = [
            '0-100000' => 'Up to 100,000 AED / yr',
            '100000-200000' => '100,000 – 200,000 AED',
            '200000-500000' => '200,000 – 500,000 AED',
            '500000+' => '500,000+ AED',
        ];
        $i = 1;
        foreach ($priceRanges as $value => $label) {
            $arLabel = $label; // keep same until proper Arabic mapping is added
            HomeBannerFilterValue::updateOrCreate(
                ['filter_definition_id' => $definitionModels['price']->id, 'value' => (string) $value],
                [
                    'label' => $label,
                    'translations' => [
                        'en' => ['label' => $label],
                        'ar' => ['label' => $arLabel],
                    ],
                    'sort_order' => $i,
                    'status' => true,
                ],
            );
            $i++;
        }

        // Populate cached distinct options for auto-sourced filters.
        app(HomeBannerFiltersController::class)->refreshValues($definitionModels['property_type']->id);
        app(HomeBannerFiltersController::class)->refreshValues($definitionModels['location']->id);
        app(HomeBannerFiltersController::class)->refreshValues($definitionModels['bedrooms']->id);
        app(HomeBannerFiltersController::class)->refreshValues($definitionModels['bathrooms']->id);
        app(HomeBannerFiltersController::class)->refreshValues($definitionModels['bed_and_baths']->id);
    }
}

