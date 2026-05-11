<?php

namespace Database\Seeders;

use App\Http\Controllers\CmsKit\HomeBannerFiltersController;
use App\Models\CmsKit\HomeBannerFilterDefinition;
use App\Models\CmsKit\HomeBannerFilterValue;
use App\Models\CmsKit\Language;
use App\Models\CmsKit\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class HomeBannerFiltersSeeder extends Seeder
{
    public function run(): void
    {
        // Definitions: property type, category, location. Location option rows are built here
        // (label + subtitle + type) from published `properties` / `property_translations` so they
        // match the hero/listing UI: Building → subtitle full address; City → "City, Country";
        // Community → "Community, City, Country". Do not call `refreshValues` for location — that
        // would overwrite these subtitles until the controller matches the same rules.

        $allowedKeys = ['property_type', 'category', 'location'];
        HomeBannerFilterDefinition::whereNotIn('key', $allowedKeys)->delete();

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
                'key' => 'category',
                'label' => 'Category',
                'translations' => [
                    'en' => ['label' => 'Category'],
                    'ar' => ['label' => 'الفئة'],
                ],
                'ui_type' => 'dropdown',
                'source_table' => 'properties',
                'source_column' => 'category',
                'status' => true,
                'sort_order' => 2,
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
                'source_column' => 'city,community,address,title',
                'status' => true,
                'sort_order' => 3,
            ],
        ];

        $definitionModels = [];
        foreach ($definitions as $def) {
            $definitionModels[$def['key']] = HomeBannerFilterDefinition::updateOrCreate(
                ['key' => $def['key']],
                $def,
            );
        }

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
            ['filter_definition_id' => $definitionModels['category']->id, 'value' => ''],
            [
                'label' => 'All categories',
                'translations' => [
                    'en' => ['label' => 'All categories'],
                    'ar' => ['label' => 'كل الفئات'],
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

        $categoryLabels = config('cms-kit.database.properties.categories', []);
        foreach ($categoryLabels as $value => $labelsByLang) {
            $enLabel = $labelsByLang['en'] ?? $value;
            $arLabel = $labelsByLang['ar'] ?? $value;
            HomeBannerFilterValue::updateOrCreate(
                ['filter_definition_id' => $definitionModels['category']->id, 'value' => (string) $value],
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

        $this->syncLocationFilterValuesFromDatabase($definitionModels['location']);

        $controller = app(HomeBannerFiltersController::class);
        $controller->refreshValues($definitionModels['property_type']->id);
        $controller->refreshValues($definitionModels['category']->id);
    }

    /**
     * Populate location filter cache with label / subtitle / type per active language from DB.
     *
     * Building (title): primary = building name, subtitle = full address (translation when present).
     * City: primary = city, subtitle = city, country.
     * Community: primary = community, subtitle = community, city, country.
     */
    private function syncLocationFilterValuesFromDatabase(HomeBannerFilterDefinition $definition): void
    {
        // Match public listings: active properties only (no published_at requirement).
        // Use Eloquent + case-insensitive matching: MySQL often matches case-insensitive in SQL while
        // PHP === would skip every row — resulting in zero inserts.
        $languages = Language::query()->where('status', true)->orderBy('id')->get();
        if ($languages->isEmpty()) {
            $languages = collect([
                (object) ['code' => (string) config('app.fallback_locale', 'en')],
            ]);
        }

        $fallbackLocale = config('app.fallback_locale', 'en');
        $columns = ['city', 'community', 'address', 'title'];
        $norm = static fn (?string $s): string => mb_strtolower(trim((string) $s), 'UTF-8');

        $computedValues = collect();
        foreach ($columns as $col) {
            $vals = Property::query()
                ->active()
                ->whereNotNull($col)
                ->where($col, '<>', '')
                ->pluck($col);
            foreach ($vals as $v) {
                $t = trim((string) $v);
                if ($t !== '') {
                    $computedValues->push($t);
                }
            }
        }

        $computedValues = $computedValues
            ->unique(fn (string $v) => $norm($v))
            ->sort()
            ->values()
            ->all();

        if ($computedValues === []) {
            $total = Property::query()->count();
            $active = Property::query()->active()->count();
            $msg = "HomeBannerFiltersSeeder: no location values inserted — no non-empty city/community/address/title on active properties (total properties: {$total}, active: {$active}).";
            Log::warning($msg);
            if ($this->command) {
                $this->command->warn($msg);
            }

            HomeBannerFilterValue::query()
                ->where('filter_definition_id', $definition->id)
                ->where('value', '!=', '')
                ->update(['status' => false]);

            return;
        }

        $valueMaxLen = 255;
        $rowsToUpsert = [];

        foreach ($computedValues as $value) {
            $needle = $norm($value);

            $prop = Property::query()
                ->active()
                ->where(function ($q) use ($columns, $needle) {
                    foreach ($columns as $col) {
                        $q->orWhereRaw('LOWER(TRIM(`'.$col.'`)) = ?', [$needle]);
                    }
                })
                ->with('translations')
                ->orderBy('id')
                ->first();

            if (! $prop) {
                continue;
            }

            $match = null;
            if ($norm($prop->title) === $needle) {
                $match = 'title';
            } elseif ($norm((string) $prop->address) === $needle) {
                $match = 'address';
            } elseif ($norm((string) $prop->community) === $needle) {
                $match = 'community';
            } elseif ($norm((string) $prop->city) === $needle) {
                $match = 'city';
            } else {
                continue;
            }

            $storedValue = match ($match) {
                'title' => trim((string) $prop->title),
                'address' => trim((string) $prop->address),
                'community' => trim((string) $prop->community),
                'city' => trim((string) $prop->city),
                default => $value,
            };

            if (mb_strlen($storedValue) > $valueMaxLen) {
                Log::warning('HomeBannerFiltersSeeder: skipped location value longer than '.$valueMaxLen.' chars (property id '.$prop->id.').');

                continue;
            }

            $translations = [];
            $labelFallback = $storedValue;

            foreach ($languages as $lang) {
                $code = is_object($lang) && isset($lang->code) ? (string) $lang->code : (string) $lang;

                $transRow = $prop->translations->firstWhere('language_code', $code);

                $countryBase = trim((string) ($prop->country ?? ''));
                $countryTranslated = trim((string) ($transRow?->country ?? $countryBase));
                $defaultCountry = 'United Arab Emirates';
                $country = $countryTranslated !== '' ? $countryTranslated : ($countryBase !== '' ? $countryBase : $defaultCountry);

                $cityLabel = trim((string) ($transRow?->city ?? $prop->city ?? ''));
                $communityLabel = trim((string) ($transRow?->community ?? $prop->community ?? ''));

                if ($match === 'title') {
                    $label = trim((string) ($transRow?->title ?? $prop->title ?? $storedValue));
                    $subtitle = trim((string) ($transRow?->full_address ?? $prop->full_address ?? ''));
                    if ($subtitle === '') {
                        $subtitle = trim((string) ($transRow?->address ?? $prop->address ?? ''));
                    }
                    $type = 'Building';
                } elseif ($match === 'city') {
                    $label = $cityLabel !== '' ? $cityLabel : $storedValue;
                    $subtitle = implode(', ', array_filter([$cityLabel, $country]));
                    $type = 'City';
                } elseif ($match === 'community') {
                    $label = $communityLabel !== '' ? $communityLabel : $storedValue;
                    $subtitle = implode(', ', array_filter([$communityLabel, $cityLabel, $country]));
                    $type = 'Community';
                } else {
                    $label = trim((string) ($transRow?->address ?? $prop->address ?? $storedValue));
                    $subtitle = implode(', ', array_filter([$cityLabel, $country]));
                    $type = 'Address';
                }

                $translations[$code] = [
                    'label' => (string) $label,
                    'subtitle' => (string) $subtitle,
                    'type' => $type,
                ];

                if ($code === $fallbackLocale) {
                    $labelFallback = (string) $label;
                }
            }

            $rowsToUpsert[] = [
                'value' => $storedValue,
                'label' => $labelFallback !== '' ? $labelFallback : $storedValue,
                'translations' => $translations,
            ];
        }

        $activeValues = collect($rowsToUpsert)->pluck('value')->unique()->filter()->values()->all();

        HomeBannerFilterValue::query()
            ->where('filter_definition_id', $definition->id)
            ->where('value', '!=', '')
            ->when(
                $activeValues !== [],
                fn ($q) => $q->whereNotIn('value', $activeValues),
            )
            ->update(['status' => false]);

        foreach ($rowsToUpsert as $i => $row) {
            HomeBannerFilterValue::updateOrCreate(
                ['filter_definition_id' => $definition->id, 'value' => $row['value']],
                [
                    'label' => $row['label'],
                    'translations' => $row['translations'],
                    'sort_order' => $i + 1,
                    'status' => true,
                ],
            );
        }

        if ($rowsToUpsert === [] && $computedValues !== []) {
            $msg = 'HomeBannerFiltersSeeder: location candidates existed but no rows could be resolved (check property data and value length ≤ 255).';
            Log::warning($msg);
            if ($this->command) {
                $this->command->warn($msg);
            }
        }
    }
}
