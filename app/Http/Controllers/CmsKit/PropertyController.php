<?php

namespace App\Http\Controllers\CmsKit;

use App\Http\Controllers\Controller;
use App\Models\CmsKit\Agent;
use App\Models\CmsKit\Language;
use App\Models\CmsKit\NearbyPlace;
use App\Models\CmsKit\Property;
use App\Models\CmsKit\SectionLabel;
use App\Support\MediaStorage;
use CMS\SiteManager\Support\ValidatesImageDimensions;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class PropertyController extends Controller
{
    use ValidatesImageDimensions;

    protected function activeLanguages()
    {
        return Language::where('status', true)->get();
    }

    protected function homeSection(): SectionLabel
    {
        return SectionLabel::firstOrCreate(['section_key' => 'properties']);
    }

    protected function sectionRules(): array
    {
        $rules = [
            'display_home' => ['required', 'boolean'],
        ];

        foreach ($this->activeLanguages() as $language) {
            $rules["translations.{$language->code}.title"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.description"] = ['required', 'string'];
        }

        return $rules;
    }

    protected function propertyTypes(): array
    {
        return config('cms-kit.database.properties.property_types', [
            'apartment' => 'Apartment',
            'villa' => 'Villa',
            'townhouse' => 'Townhouse',
            'penthouse' => 'Penthouse',
            'office' => 'Office',
        ]);
    }

    protected function listingTypes(): array
    {
        return config('cms-kit.database.properties.listing_types', [
            'rent' => 'Rent',
            'sale' => 'Sale',
        ]);
    }

    protected function categories(): array
    {
        return config('cms-kit.database.properties.categories', [
            'residential' => ['en' => 'Residential', 'ar' => 'Residential'],
            'commercial' => ['en' => 'Commercial', 'ar' => 'Commercial'],
            'luxury' => ['en' => 'Luxury', 'ar' => 'Luxury'],
            'off-plan' => ['en' => 'Off-plan', 'ar' => 'Off-plan'],
        ]);
    }

    protected function sourceTypes(): array
    {
        return config('cms-kit.database.properties.source_types', [
            'manual' => ['en' => 'Manual', 'ar' => 'Manual'],
            'sync' => ['en' => 'Sync', 'ar' => 'Sync'],
        ]);
    }

    protected function currencies(): array
    {
        return config('cms-kit.database.properties.currencies', [
            'AED' => 'AED',
            'USD' => 'USD',
            'EUR' => 'EUR',
        ]);
    }

    protected function placeTypes(): array
    {
        return config('cms-kit.database.properties.nearby_place_types', [
            'school' => 'School',
            'hospital' => 'Hospital',
            'restaurant' => 'Restaurant',
            'attraction' => 'Attraction',
        ]);
    }

    protected function optionLabel(array $options, ?string $key): string
    {
        if (! $key) {
            return '-';
        }

        $value = $options[$key] ?? null;

        if (is_array($value)) {
            $locale = app()->getLocale();
            $fallback = config('app.fallback_locale', 'en');
            $label = $value[$locale] ?? $value[$fallback] ?? reset($value);

            return trim((string) ($label ?: Str::headline(str_replace(['-', '_'], ' ', $key))));
        }

        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }

        return Str::headline(str_replace(['-', '_'], ' ', $key));
    }

    protected function propertySectionIconConfig(): array
    {
        return config('cms-kit.images.properties.section_icon', [
            'max_size' => 512,
        ]);
    }

    protected function resolvedPropertyType(Request $request): string
    {
        $propertyType = trim((string) $request->input('property_type'));

        if ($propertyType === 'custom') {
            return trim((string) $request->input('custom_property_type'));
        }

        return $propertyType;
    }

    protected function resolvedCategory(Request $request): ?string
    {
        $category = trim((string) $request->input('category'));

        if ($category === 'custom') {
            $customCategory = trim((string) $request->input('custom_category'));

            return $customCategory !== '' ? $customCategory : null;
        }

        return $category !== '' ? $category : null;
    }

    protected function sanitizePropId(?string $propId, int $propertyId): string
    {
        $sanitized = preg_replace('/[^A-Za-z0-9\-]/', '-', (string) ($propId ?? ''));

        return trim((string) $sanitized, '-') ?: "PROP-{$propertyId}";
    }

    protected function safeMediaUrl(?string $path): ?string
    {
        try {
            return media_url($path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function rules(?Property $property = null): array
    {
        $propertyId = $property?->id;
        $languages = $this->activeLanguages();
        $imageConfig = config('cms-kit.images.properties.image', []);
        $fallbackLocale = config('app.fallback_locale', 'en');
        $rules = [
            'prop_id' => $property !== null
                ? ['required', 'string', 'max:255', Rule::in([(string) ($property->getOriginal('prop_id') ?? '')])]
                : ['required', 'string', 'max:255', Rule::unique('properties', 'prop_id')],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('properties', 'slug')->ignore($propertyId)],
            'property_type' => ['required', 'string', 'max:255'],
            'custom_property_type' => ['nullable', 'string', 'max:255', 'required_if:property_type,custom'],
            'listing_type' => ['required', Rule::in(array_keys($this->listingTypes()))],
            'category' => ['required', 'string', 'max:255'],
            'custom_category' => ['nullable', 'string', 'max:255', 'required_if:category,custom'],
            'source_type' => ['required', Rule::in(array_keys($this->sourceTypes()))],
        ];

        // Shared Setup Rules
        $rules += [
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'agent_id' => ['required', 'integer', Rule::exists('agents', 'id')],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'sqft' => ['nullable', 'integer', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:1'],
            'published_at' => ['nullable', 'date'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:'.(now()->year + 1)],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'virtual_tour_url' => ['nullable', 'url', 'max:2048'],
            'direct_from_owner' => ['nullable', 'string', 'max:255'],
            'nearby_places' => ['nullable', 'array'],
            'nearby_places.*' => ['nullable', 'array'],
            'nearby_places.*.*' => ['integer', Rule::exists('nearby_places', 'id')],
            'existing_images' => ['nullable', 'array'],
            'existing_images.*.order' => ['nullable', 'integer', 'min:1'],
            'existing_images.*.delete' => ['nullable', 'boolean'],
            'new_images' => ['nullable', 'array'],
            'new_images.*.file' => ['nullable', 'image', 'max:'.($imageConfig['max_size'] ?? 4096)],
            'new_images.*.order' => ['nullable', 'integer', 'min:1'],
            'metadata.meta_title' => ['nullable', 'string', 'max:255'],
            'metadata.meta_description' => ['nullable', 'string', 'max:500'],
            'metadata.meta_keywords' => ['nullable', 'string', 'max:500'],
            'metadata.canonical_url' => ['nullable', 'url', 'max:2048'],
            'metadata.og_title' => ['nullable', 'string', 'max:255'],
            'metadata.og_description' => ['nullable', 'string', 'max:500'],
            'metadata.og_image' => ['nullable', 'image', 'max:512'],
            'metadata.other_meta_tags' => ['nullable', 'string'],
            'remove_metadata_og_image' => ['nullable', 'boolean'],
        ];

        foreach ($languages as $language) {
            $rules["translations.{$language->code}.title"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.address"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.city"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.country"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.description"] = ['nullable', 'string'];
            $rules["translations.{$language->code}.community"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.postal_code"] = ['nullable', 'string', 'max:50'];
            $rules["translations.{$language->code}.easy_to_access"] = ['nullable', 'array'];
            $rules["translations.{$language->code}.key_features_text"] = ['nullable', 'string', 'max:2000'];
            $rules["translations.{$language->code}.easy_to_access.*.current_icon"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.easy_to_access.*.icon_file"] = ['nullable', 'image', 'max:'.($this->propertySectionIconConfig()['max_size'] ?? 512)];
            $rules["translations.{$language->code}.easy_to_access.*.label"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.key_features_text"] = ['nullable', 'string', 'max:2000'];
            $rules["translations.{$language->code}.amenities"] = ['nullable', 'array'];
            $rules["translations.{$language->code}.amenities.*.current_icon"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.amenities.*.icon_file"] = ['nullable', 'image', 'max:'.($this->propertySectionIconConfig()['max_size'] ?? 512)];
            $rules["translations.{$language->code}.amenities.*.name"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.property_attributes"] = ['nullable', 'array'];
            $rules["translations.{$language->code}.property_attributes.*.current_icon"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.property_attributes.*.icon_file"] = ['nullable', 'image', 'max:'.($this->propertySectionIconConfig()['max_size'] ?? 512)];
            $rules["translations.{$language->code}.property_attributes.*.name"] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    protected function groupedNearbyPlaces()
    {
        return NearbyPlace::query()
            ->where('status', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');
    }

    protected function normalizedRows(array $rows, array $keys): array
    {
        return collect($rows)
            ->map(function ($row) use ($keys) {
                $normalized = [];

                foreach ($keys as $key) {
                    $value = $row[$key] ?? null;
                    $normalized[$key] = is_string($value) ? trim($value) : $value;
                }

                return $normalized;
            })
            ->filter(function ($row) {
                foreach ($row as $value) {
                    if ($value !== null && $value !== '') {
                        return true;
                    }
                }

                return false;
            })
            ->values()
            ->all();
    }

    protected function normalizedIconRows(Request $request, string $languageCode, string $section, array $rows, array $keys): array
    {
        return collect($rows)
            ->map(function ($row, $index) use ($request, $languageCode, $section, $keys) {
                $normalized = [];

                foreach ($keys as $key) {
                    $value = $row[$key] ?? null;
                    $normalized[$key] = is_string($value) ? trim($value) : $value;
                }

                $normalized['icon'] = trim((string) ($row['current_icon'] ?? $row['icon'] ?? ''));

                /** @var UploadedFile|null $uploadedIcon */
                $uploadedIcon = $request->file("translations.{$languageCode}.{$section}.{$index}.icon_file");
                if ($uploadedIcon) {
                    $normalized['icon'] = MediaStorage::store($uploadedIcon, 'properties/section-icons');
                }

                return $normalized;
            })
            ->filter(function ($row) {
                foreach ($row as $value) {
                    if ($value !== null && $value !== '') {
                        return true;
                    }
                }

                return false;
            })
            ->values()
            ->all();
    }

    protected function buildFullAddressFromParts(array $parts): string
    {
        return collect([
            trim((string) ($parts['address'] ?? '')),
            trim((string) ($parts['community'] ?? '')),
            trim((string) ($parts['city'] ?? '')),
            trim((string) ($parts['postal_code'] ?? '')),
            trim((string) ($parts['country'] ?? '')),
        ])
            ->filter(fn ($value) => $value !== '')
            ->implode(', ');
    }

    protected function normalizedTranslations(Request $request): array
    {
        $translations = (array) $request->input('translations', []);

        $normalized = collect($translations)
            ->map(function ($translation, $code) use ($request) {
                $normalizedAddress = [
                    'address' => trim((string) ($translation['address'] ?? '')),
                    'community' => trim((string) ($translation['community'] ?? '')),
                    'city' => trim((string) ($translation['city'] ?? '')),
                    'postal_code' => trim((string) ($translation['postal_code'] ?? '')),
                    'country' => trim((string) ($translation['country'] ?? '')),
                ];
                $keyFeaturesText = trim((string) ($translation['key_features_text'] ?? ''));

                return [
                    'language_code' => $code,
                    'title' => trim((string) ($translation['title'] ?? '')),
                    'description' => trim((string) ($translation['description'] ?? '')),
                    'address' => $normalizedAddress['address'],
                    'full_address' => $this->buildFullAddressFromParts($normalizedAddress),
                    'city' => $normalizedAddress['city'],
                    'community' => $normalizedAddress['community'],
                    'country' => $normalizedAddress['country'],
                    'postal_code' => $normalizedAddress['postal_code'],
                    'easy_to_access' => $this->normalizedIconRows($request, (string) $code, 'easy_to_access', $translation['easy_to_access'] ?? [], ['label']),
                    'key_features' => $keyFeaturesText !== '' ? [['text' => $keyFeaturesText]] : [],
                    'amenities' => $this->normalizedIconRows($request, (string) $code, 'amenities', $translation['amenities'] ?? [], ['name']),
                    'property_attributes' => $this->normalizedIconRows($request, (string) $code, 'property_attributes', $translation['property_attributes'] ?? [], ['name']),
                ];
            })
            ->filter(function ($translation) {
                return $translation['title'] !== ''
                    || $translation['description'] !== ''
                    || $translation['address'] !== ''
                    || $translation['full_address'] !== ''
                    || ! empty($translation['easy_to_access'])
                    || ! empty($translation['key_features'])
                    || ! empty($translation['amenities'])
                    || ! empty($translation['property_attributes']);
            })
            ->values()
            ->all();

        return $this->withSharedTranslatedSectionStructure($normalized);
    }

    protected function withSharedTranslatedSectionStructure(array $translations): array
    {
        $fallbackLocale = config('app.fallback_locale', 'en');
        $fallbackTranslation = collect($translations)->firstWhere('language_code', $fallbackLocale) ?? ($translations[0] ?? null);

        if (! $fallbackTranslation) {
            return $translations;
        }

        $sectionFields = [
            'easy_to_access' => 'label',
            'amenities' => 'name',
            'property_attributes' => 'name',
        ];

        foreach ($translations as &$translation) {
            if (($translation['language_code'] ?? null) === ($fallbackTranslation['language_code'] ?? null)) {
                continue;
            }

            foreach ($sectionFields as $section => $textField) {
                $fallbackRows = array_values((array) ($fallbackTranslation[$section] ?? []));
                $rows = array_values((array) ($translation[$section] ?? []));
                $rowCount = max(count($fallbackRows), count($rows));
                $mergedRows = [];

                for ($index = 0; $index < $rowCount; $index++) {
                    $fallbackRow = is_array($fallbackRows[$index] ?? null) ? $fallbackRows[$index] : [];
                    $row = is_array($rows[$index] ?? null) ? $rows[$index] : [];
                    $text = trim((string) ($row[$textField] ?? ''));
                    $icon = trim((string) ($row['icon'] ?? $fallbackRow['icon'] ?? ''));

                    if ($text === '' && $icon === '') {
                        continue;
                    }

                    $mergedRows[] = [
                        'icon' => $icon,
                        $textField => $text,
                    ];
                }

                $translation[$section] = $mergedRows;
            }
        }
        unset($translation);

        return $translations;
    }

    protected function validateSectionIconUploads(Request $request): void
    {
        $iconConfig = $this->propertySectionIconConfig();

        foreach ($this->activeLanguages() as $language) {
            foreach (['easy_to_access', 'amenities', 'property_attributes'] as $section) {
                foreach (array_keys((array) $request->input("translations.{$language->code}.{$section}", [])) as $index) {
                    $field = "translations.{$language->code}.{$section}.{$index}.icon_file";
                    if ($request->hasFile($field)) {
                        $this->validateImageWithinLimits($request, $field, $iconConfig, ucfirst(str_replace('_', ' ', $section)).' icon');
                    }
                }
            }
        }
    }

    protected function managedIconPathsFromTranslations(array $translations): array
    {
        return collect($translations)
            ->flatMap(function ($translation) {
                return collect([
                    $translation['easy_to_access'] ?? [],
                    $translation['amenities'] ?? [],
                    $translation['property_attributes'] ?? [],
                ])->flatten(1);
            })
            ->pluck('icon')
            ->filter(fn ($path) => is_string($path) && Str::startsWith($path, 'properties/section-icons/'))
            ->unique()
            ->values()
            ->all();
    }

    protected function managedIconPathsForProperty(Property $property): array
    {
        $translations = $property->relationLoaded('translations')
            ? $property->translations
            : $property->translations()->get();

        return $translations
            ->flatMap(function ($translation) {
                return collect([
                    $translation->easy_to_access ?? [],
                    $translation->amenities ?? [],
                    $translation->property_attributes ?? [],
                ])->flatten(1);
            })
            ->pluck('icon')
            ->filter(fn ($path) => is_string($path) && Str::startsWith($path, 'properties/section-icons/'))
            ->unique()
            ->values()
            ->all();
    }

    protected function deleteManagedSectionIcons(array $paths): void
    {
        MediaStorage::deleteMany(collect($paths)->filter()->unique());
    }

    protected function resolveSlug(Request $request, array $translations, ?int $ignoreId = null): string
    {
        $fallbackLocale = config('app.fallback_locale', 'en');
        $providedSlug = trim((string) $request->input('slug'));
        $title = data_get(collect($translations)->firstWhere('language_code', $fallbackLocale), 'title')
            ?? data_get(collect($translations)->first(), 'title')
            ?? '';
        $baseSlug = Str::slug($providedSlug !== '' ? $providedSlug : $title);

        if ($baseSlug === '') {
            throw ValidationException::withMessages([
                'slug' => 'Unable to generate a slug.',
            ]);
        }

        $slug = $baseSlug;
        $suffix = 2;

        while (
            Property::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    protected function resolveOrderForCreate(?int $requestedOrder): int
    {
        $total = Property::count();
        $maxAllowed = $total + 1;

        if ($requestedOrder === null) {
            return $maxAllowed;
        }

        if ($requestedOrder < 1 || $requestedOrder > $maxAllowed) {
            throw ValidationException::withMessages([
                'order' => "Order must be between 1 and {$maxAllowed}.",
            ]);
        }

        return $requestedOrder;
    }

    protected function resolveOrderForReorder(int $requestedOrder): int
    {
        $total = Property::count();

        if ($total <= 1) {
            return 1;
        }

        if ($requestedOrder < 1 || $requestedOrder > $total) {
            throw ValidationException::withMessages([
                'order' => "Order must be between 1 and {$total}.",
            ]);
        }

        return $requestedOrder;
    }

    protected function normalizePropertyOrder(): void
    {
        $items = Property::query()->orderBy('order')->orderBy('id')->get(['id']);

        foreach ($items as $index => $item) {
            Property::whereKey($item->id)->update(['order' => $index + 1]);
        }
    }

    protected function syncTranslations(Property $property, array $translations): void
    {
        $property->translations()->delete();

        foreach ($translations as $translation) {
            $property->translations()->create($translation);
        }
    }

    protected function syncNearbyPlaces(Property $property, array $nearbyPlaces): void
    {
        $syncData = [];
        $order = 1;

        foreach ($nearbyPlaces as $typePlaces) {
            foreach ((array) $typePlaces as $placeId) {
                $placeId = (int) $placeId;
                if ($placeId <= 0) {
                    continue;
                }

                $syncData[$placeId] = [
                    'distance' => null,
                    'order' => $order++,
                ];
            }
        }

        $property->nearbyPlaces()->sync($syncData);
    }

    /**
     * Property updates should not touch storage unless the gallery section actually changed.
     * Otherwise every save would hit Cloudinary (download + re-upload of all images).
     */
    protected function propertyImageGalleryRequiresSync(Request $request, Property $property): bool
    {
        foreach (array_keys((array) $request->input('new_images', [])) as $index) {
            if ($request->file("new_images.{$index}.file")) {
                return true;
            }
        }

        foreach ((array) $request->input('existing_images', []) as $payload) {
            if (! empty($payload['delete'])) {
                return true;
            }
        }

        $existing = (array) $request->input('existing_images', []);
        $suffixesOrdered = collect($existing)
            ->filter(fn ($p) => empty($p['delete']))
            ->sortBy(fn ($p) => (int) ($p['order'] ?? 0))
            ->keys()
            ->map(fn ($k) => (string) $k)
            ->values()
            ->all();

        $rawImages = (string) ($property->getAttributes()['images'] ?? '');
        $current = array_values(array_filter(array_map('trim', explode(',', $rawImages)), fn ($s) => $s !== ''));

        return $suffixesOrdered !== $current;
    }

    protected function syncImages(Property $property, Request $request, ?string $oldSanitizedPropId = null): void
    {
        $storage = MediaStorage::disk();
        $isCloudinaryDisk = MediaStorage::diskName() === 'cloudinary';
        $propertyId = $property->id;
        $sanitizedPropId = $property->sanitized_prop_id;
        $dir = "properties/property_{$propertyId}";

        if (! $storage->exists($dir)) {
            $storage->makeDirectory($dir);
        }

        $allProcessedImages = [];

        // 1. Process Existing Images
        foreach ((array) $request->input('existing_images', []) as $suffix => $payload) {
            $currentPath = "{$dir}/property_".($oldSanitizedPropId ?? $sanitizedPropId)."_{$suffix}.jpg";
            // Fallback to ID-based path if prop_id path doesn't exist (for migration period or if prop_id changed)
            if (! $storage->exists($currentPath)) {
                $currentPath = "{$dir}/property_{$propertyId}_{$suffix}.jpg";
            }

            if (! empty($payload['delete'])) {
                if ($storage->exists($currentPath)) {
                    $storage->delete($currentPath);
                }

                continue;
            }

            if ($storage->exists($currentPath)) {
                $allProcessedImages[] = [
                    'source' => $currentPath,
                    'order' => (int) ($payload['order'] ?? $suffix),
                    'is_new' => false,
                ];
            }
        }

        // 2. Process New Images
        foreach ((array) $request->input('new_images', []) as $index => $payload) {
            $file = $request->file("new_images.{$index}.file");
            if (! $file) {
                continue;
            }

            if ($isCloudinaryDisk) {
                $allProcessedImages[] = [
                    'source' => null,
                    'binary' => $this->jpegData($file),
                    'order' => (int) ($payload['order'] ?? 999),
                    'is_new' => true,
                ];
            } else {
                // Save to a temporary name first
                $tempName = 'temp_'.uniqid().'.jpg';
                $tempPath = "{$dir}/{$tempName}";

                // Convert to JPEG during upload
                $this->storeAsJpeg($file, $tempPath);

                $allProcessedImages[] = [
                    'source' => $tempPath,
                    'order' => (int) ($payload['order'] ?? 999),
                    'is_new' => true,
                ];
            }
        }

        // 3. Sort by intended order
        usort($allProcessedImages, fn ($a, $b) => $a['order'] <=> $b['order']);

        if ($isCloudinaryDisk) {
            // Snapshot existing binaries first to avoid in-place overwrite cascade during reorder.
            foreach ($allProcessedImages as $idx => $img) {
                if (! empty($img['is_new'])) {
                    continue;
                }

                $sourcePath = (string) ($img['source'] ?? '');
                if ($sourcePath !== '' && $storage->exists($sourcePath)) {
                    $allProcessedImages[$idx]['binary'] = $storage->get($sourcePath);
                }
            }

            $keepPaths = [];
            foreach ($allProcessedImages as $index => $img) {
                $finalIdx = $index + 1;
                $finalPath = "{$dir}/property_{$sanitizedPropId}_{$finalIdx}.jpg";
                $binary = $img['binary'] ?? null;

                if (is_string($binary) && $binary !== '') {
                    MediaStorage::put($finalPath, $binary);
                }

                $keepPaths[] = $finalPath;
            }

            foreach ($storage->files($dir) as $existingPath) {
                if (! in_array($existingPath, $keepPaths, true)) {
                    MediaStorage::delete($existingPath);
                }
            }

            $newSuffixes = range(1, count($allProcessedImages));
            $property->update([
                'images_directory' => $dir,
                'images' => implode(',', $newSuffixes),
            ]);

            return;
        }

        // 4. Sequential Renaming
        $newSuffixes = [];
        $tempMapping = [];

        // First pass: rename everything to temporary names to avoid collisions
        foreach ($allProcessedImages as $index => $img) {
            $finalIdx = $index + 1;
            $tempPath = "{$dir}/final_temp_{$finalIdx}.jpg";
            $storage->move($img['source'], $tempPath);
            $tempMapping[$finalIdx] = $tempPath;
            $newSuffixes[] = $finalIdx;
        }

        // Second pass: rename from temporary to final names
        foreach ($tempMapping as $idx => $tempPath) {
            $finalPath = "{$dir}/property_{$sanitizedPropId}_{$idx}.jpg";
            $storage->move($tempPath, $finalPath);
        }

        // 5. Update Property Model
        $property->update([
            'images_directory' => $dir,
            'images' => implode(',', $newSuffixes),
        ]);
    }

    protected function storeAsJpeg($file, $targetPath): void
    {
        MediaStorage::put($targetPath, $this->jpegData($file));
    }

    protected function jpegData(UploadedFile $file): string
    {
        $raw = (string) file_get_contents($file->getRealPath());
        $image = @imagecreatefromstring($raw);
        if (! $image) {
            return $raw;
        }

        ob_start();
        imagejpeg($image, null, 90);
        $jpgData = (string) ob_get_clean();
        imagedestroy($image);

        return $jpgData !== '' ? $jpgData : $raw;
    }

    /**
     * @param  array<string, mixed>  $existingMetadata
     * @return array<string, mixed>
     */
    protected function extractPropertyMetadata(Request $request, array $existingMetadata = []): array
    {
        $metadata = $request->input('metadata', []);
        $metadata = is_array($metadata) ? $metadata : [];

        $out = [
            'meta_title' => $this->trimNullableString($metadata['meta_title'] ?? null),
            'meta_description' => $this->trimNullableString($metadata['meta_description'] ?? null),
            'meta_keywords' => $this->trimNullableString($metadata['meta_keywords'] ?? null),
            'canonical_url' => $this->trimNullableString($metadata['canonical_url'] ?? null),
            'og_title' => $this->trimNullableString($metadata['og_title'] ?? null),
            'og_description' => $this->trimNullableString($metadata['og_description'] ?? null),
            'other_meta_tags' => $this->trimNullableString($metadata['other_meta_tags'] ?? null),
            'og_image' => null,
        ];

        $existingOgImage = trim((string) ($existingMetadata['og_image'] ?? ''));
        if ($request->hasFile('metadata.og_image')) {
            if ($existingOgImage !== '') {
                MediaStorage::delete($existingOgImage);
            }
            $out['og_image'] = MediaStorage::store($request->file('metadata.og_image'), 'properties/metadata');
        } elseif ($request->boolean('remove_metadata_og_image')) {
            if ($existingOgImage !== '') {
                MediaStorage::delete($existingOgImage);
            }
            $out['og_image'] = null;
        } else {
            $out['og_image'] = $existingOgImage !== '' ? $existingOgImage : null;
        }

        return array_filter($out, static function ($value, $key) {
            if ($key === 'og_image') {
                return $value !== null && trim((string) $value) !== '';
            }

            return $value !== null && trim((string) $value) !== '';
        }, ARRAY_FILTER_USE_BOTH);
    }

    protected function trimNullableString(mixed $value): ?string
    {
        $stringValue = trim((string) $value);

        return $stringValue !== '' ? $stringValue : null;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cmsUser = auth('cms')->user();
            $propertyTypes = $this->propertyTypes();
            $listingTypes = $this->listingTypes();
            $categories = $this->categories();
            $data = Property::query()
                ->select([
                    'id',
                    'title',
                    'city',
                    'community',
                    'property_type',
                    'listing_type',
                    'category',
                    'price',
                    'currency',
                    'status',
                    'is_featured',
                    'order',
                ])
                ->ordered();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('select_all', fn ($row) => '<input type="checkbox" class="row-checkbox form-check-input" value="'.$row->id.'">')
                ->addColumn('property', function ($row) {
                    return '<div><div class="fw-semibold">'.e($row->title).'</div><small class="text-muted">'.e(trim(($row->city ?: '-').($row->community ? ' / '.$row->community : ''))).'</small></div>';
                })
                ->addColumn('type', fn ($row) => e($this->optionLabel($propertyTypes, $row->property_type).' / '.$this->optionLabel($listingTypes, $row->listing_type)))
                ->addColumn('category_label', fn ($row) => e($this->optionLabel($categories, $row->category)))
                ->addColumn('price_label', function ($row) {
                    return e(trim(($row->currency ?: '').' '.($row->price ? number_format((float) $row->price, 2) : '-')));
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';

                    return '<div class="form-check form-switch">
                                <input class="form-check-input toggle-status" type="checkbox" data-id="'.$row->id.'" '.$checked.'>
                            </div>';
                })
                ->addColumn('featured', function ($row) {
                    return $row->is_featured
                        ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">Featured</span>'
                        : '<span class="text-muted">-</span>';
                })
                ->addColumn('order_input', function ($row) {
                    return '<input type="number" min="1" class="form-control form-control-sm reorder-input" data-id="'.$row->id.'" value="'.$row->order.'" style="width: 80px;">';
                })
                ->addColumn('action', function ($row) use ($cmsUser) {
                    $buttons = '<div class="btn-group">';

                    if ($cmsUser?->can('property.edit')) {
                        $buttons .= '<a href="'.route('cms.properties.edit', $row->id).'" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>';
                    }

                    if ($cmsUser?->can('property.delete')) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['select_all', 'property', 'status', 'featured', 'order_input', 'action'])
                ->make(true);
        }

        $languages = $this->activeLanguages();
        $homeSection = $this->homeSection();

        return view('cms-kit::properties.index', compact('languages', 'homeSection'));
    }

    public function updateSection(Request $request)
    {
        $section = $this->homeSection();
        $request->validate($this->sectionRules());

        $section->forceFill([
            'translations' => $request->input('translations', []),
            'status' => $request->boolean('display_home', false),
        ])->save();

        return redirect()->route('cms.properties.index')->with('success', 'Home rental section settings updated.');
    }

    public function create()
    {
        $languages = $this->activeLanguages();
        $agents = Agent::query()->where('status', true)->orderBy('name')->get();
        $nearbyPlaces = $this->groupedNearbyPlaces();
        $propertyTypes = $this->propertyTypes();
        $listingTypes = $this->listingTypes();
        $categories = $this->categories();
        $sourceTypes = $this->sourceTypes();
        $currencies = $this->currencies();
        $placeTypes = $this->placeTypes();
        $nextOrder = Property::count() + 1;
        $propertyImageConfig = config('cms-kit.images.properties.image', []);

        return view('cms-kit::properties.create', compact(
            'languages',
            'agents',
            'nearbyPlaces',
            'propertyTypes',
            'listingTypes',
            'categories',
            'sourceTypes',
            'currencies',
            'placeTypes',
            'nextOrder',
            'propertyImageConfig'
        ));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());
        $this->validateSectionIconUploads($request);
        foreach (array_keys((array) $request->input('new_images', [])) as $index) {
            $this->validateImageWithinLimits($request, "new_images.{$index}.file", config('cms-kit.images.properties.image', []), 'Property image');
        }
        $translations = $this->normalizedTranslations($request);
        $metadata = $this->extractPropertyMetadata($request);
        $order = $this->resolveOrderForCreate($request->filled('order') ? (int) $request->order : null);
        $fallbackLocale = config('app.fallback_locale', 'en');
        $fallbackTranslation = collect($translations)->firstWhere('language_code', $fallbackLocale) ?? ($translations[0] ?? []);

        DB::transaction(function () use ($request, $translations, $metadata, $order, $fallbackTranslation) {
            Property::where('order', '>=', $order)->increment('order');

            $property = Property::create([
                'prop_id' => trim((string) $request->input('prop_id')),
                'title' => $fallbackTranslation['title'] ?? '',
                'slug' => $this->resolveSlug($request, $translations),
                'property_type' => $this->resolvedPropertyType($request),
                'listing_type' => $request->input('listing_type'),
                'category' => $this->resolvedCategory($request),
                'source_type' => $request->input('source_type', 'manual'),
                'price' => $request->input('price'),
                'currency' => trim((string) $request->input('currency')),
                'bedrooms' => $request->input('bedrooms'),
                'bathrooms' => $request->input('bathrooms'),
                'sqft' => $request->input('sqft'),
                'address' => trim((string) ($fallbackTranslation['address'] ?? '')),
                'full_address' => trim((string) ($fallbackTranslation['full_address'] ?? '')),
                'postal_code' => trim((string) ($fallbackTranslation['postal_code'] ?? '')),
                'city' => trim((string) ($fallbackTranslation['city'] ?? '')),
                'community' => trim((string) ($fallbackTranslation['community'] ?? '')),
                'country' => trim((string) ($fallbackTranslation['country'] ?? '')),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'agent_id' => $request->input('agent_id'),
                'status' => $request->boolean('status', true),
                'is_featured' => $request->boolean('is_featured', false),
                'order' => $order,
                'published_at' => $request->input('published_at') ?: now(),
                'metadata' => $metadata,
            ]);

            $property->details()->create([
                'description' => $fallbackTranslation['description'] ?? '',
                'year_built' => $request->filled('year_built') ? (int) $request->input('year_built') : null,
                'security_deposit' => $request->filled('security_deposit') ? (float) $request->input('security_deposit') : null,
                'virtual_tour_url' => $request->filled('virtual_tour_url') ? trim((string) $request->input('virtual_tour_url')) : null,
                'direct_from_owner' => trim((string) $request->input('direct_from_owner')),
                'easy_to_access' => $fallbackTranslation['easy_to_access'] ?? [],
                'key_features' => $fallbackTranslation['key_features'] ?? [],
                'amenities' => $fallbackTranslation['amenities'] ?? [],
                'property_attributes' => $fallbackTranslation['property_attributes'] ?? [],
            ]);

            $this->syncTranslations($property, $translations);
            $this->syncNearbyPlaces($property, (array) $request->input('nearby_places', []));
            $this->syncImages($property, $request);
        });

        return redirect()->route('cms.properties.index')->with('success', 'Property created successfully.');
    }

    public function edit($id)
    {
        $property = Property::query()
            ->with(['details', 'translations', 'nearbyPlaces'])
            ->findOrFail($id);
        $languages = $this->activeLanguages();
        $agents = Agent::query()->where('status', true)->orderBy('name')->get();
        $nearbyPlaces = $this->groupedNearbyPlaces();
        $propertyTypes = $this->propertyTypes();
        $listingTypes = $this->listingTypes();
        $categories = $this->categories();
        $sourceTypes = $this->sourceTypes();
        $currencies = $this->currencies();
        $placeTypes = $this->placeTypes();
        $propertyImageConfig = config('cms-kit.images.properties.image', []);

        return view('cms-kit::properties.edit', compact(
            'property',
            'languages',
            'agents',
            'nearbyPlaces',
            'propertyTypes',
            'listingTypes',
            'categories',
            'sourceTypes',
            'currencies',
            'placeTypes',
            'propertyImageConfig'
        ));
    }

    public function update(Request $request, $id)
    {
        $property = Property::query()->with(['details', 'translations'])->findOrFail($id);
        $request->validate($this->rules($property));
        $this->validateSectionIconUploads($request);
        foreach (array_keys((array) $request->input('new_images', [])) as $index) {
            $this->validateImageWithinLimits($request, "new_images.{$index}.file", config('cms-kit.images.properties.image', []), 'Property image');
        }
        $translations = $this->normalizedTranslations($request);
        $metadata = $this->extractPropertyMetadata($request, is_array($property->metadata) ? $property->metadata : []);
        $fallbackLocale = config('app.fallback_locale', 'en');
        $fallbackTranslation = collect($translations)->firstWhere('language_code', $fallbackLocale) ?? ($translations[0] ?? []);
        $existingIconPaths = $this->managedIconPathsForProperty($property);

        $galleryRequiresSync = $this->propertyImageGalleryRequiresSync($request, $property);

        DB::transaction(function () use ($request, $property, $translations, $metadata, $fallbackTranslation, $existingIconPaths, $galleryRequiresSync) {
            $newOrder = $this->resolveOrderForReorder((int) $request->input('order', $property->order));
            $oldOrder = $property->order;

            if ($newOrder !== $oldOrder) {
                if ($newOrder > $oldOrder) {
                    Property::where('order', '>', $oldOrder)->where('order', '<=', $newOrder)->decrement('order');
                } else {
                    Property::where('order', '>=', $newOrder)->where('order', '<', $oldOrder)->increment('order');
                }
            }

            $property->update([
                'prop_id' => trim((string) ($property->getOriginal('prop_id') ?? '')),
                'title' => $fallbackTranslation['title'] ?? $property->title,
                'slug' => $this->resolveSlug($request, $translations, $property->id),
                'property_type' => $this->resolvedPropertyType($request),
                'listing_type' => $request->input('listing_type'),
                'category' => $this->resolvedCategory($request),
                'source_type' => $request->input('source_type', 'manual'),
                'price' => $request->input('price'),
                'currency' => trim((string) $request->input('currency')),
                'bedrooms' => $request->input('bedrooms'),
                'bathrooms' => $request->input('bathrooms'),
                'sqft' => $request->input('sqft'),
                'address' => trim((string) ($fallbackTranslation['address'] ?? '')),
                'full_address' => trim((string) ($fallbackTranslation['full_address'] ?? '')),
                'postal_code' => trim((string) ($fallbackTranslation['postal_code'] ?? '')),
                'city' => trim((string) ($fallbackTranslation['city'] ?? '')),
                'community' => trim((string) ($fallbackTranslation['community'] ?? '')),
                'country' => trim((string) ($fallbackTranslation['country'] ?? '')),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'agent_id' => $request->input('agent_id'),
                'status' => $request->boolean('status', false),
                'is_featured' => $request->boolean('is_featured', false),
                'order' => $newOrder,
                'published_at' => $request->input('published_at') ?: $property->published_at,
                'metadata' => $metadata,
            ]);

            $property->details()->updateOrCreate(
                ['property_id' => $property->id],
                [
                    'description' => $fallbackTranslation['description'] ?? '',
                    'year_built' => $request->filled('year_built') ? (int) $request->input('year_built') : null,
                    'security_deposit' => $request->filled('security_deposit') ? (float) $request->input('security_deposit') : null,
                    'virtual_tour_url' => $request->filled('virtual_tour_url') ? trim((string) $request->input('virtual_tour_url')) : null,
                    'direct_from_owner' => trim((string) $request->input('direct_from_owner')),
                    'easy_to_access' => $fallbackTranslation['easy_to_access'] ?? [],
                    'key_features' => $fallbackTranslation['key_features'] ?? [],
                    'amenities' => $fallbackTranslation['amenities'] ?? [],
                    'property_attributes' => $fallbackTranslation['property_attributes'] ?? [],
                ]
            );

            $this->syncTranslations($property, $translations);
            $this->syncNearbyPlaces($property, (array) $request->input('nearby_places', []));
            if ($galleryRequiresSync) {
                $oldSanitizedPropId = $this->sanitizePropId((string) $property->getOriginal('prop_id'), (int) $property->id);
                $this->syncImages($property, $request, $oldSanitizedPropId);
            }
            $this->normalizePropertyOrder();

            $newIconPaths = $this->managedIconPathsFromTranslations($translations);
            $this->deleteManagedSectionIcons(array_diff($existingIconPaths, $newIconPaths));
        });

        return redirect()->route('cms.properties.index')->with('success', 'Property updated successfully.');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $order = $property->order;

        MediaStorage::delete(data_get($property->metadata, 'og_image'));

        if ($property->images_directory) {
            MediaStorage::deleteDirectory($property->images_directory);
        }

        $this->deleteManagedSectionIcons($this->managedIconPathsForProperty($property));

        $property->delete();
        Property::where('order', '>', $order)->decrement('order');
        $this->normalizePropertyOrder();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cms.properties.index')->with('success', 'Property deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['status' => ! $property->status]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cms.properties.index')->with('success', 'Property status updated.');
    }

    public function reorder(Request $request, $id)
    {
        $request->validate([
            'order' => ['required', 'integer', 'min:1'],
        ]);

        $property = Property::findOrFail($id);
        $newOrder = $this->resolveOrderForReorder((int) $request->input('order'));
        $oldOrder = $property->order;

        if ($newOrder !== $oldOrder) {
            if ($newOrder > $oldOrder) {
                Property::where('order', '>', $oldOrder)->where('order', '<=', $newOrder)->decrement('order');
            } else {
                Property::where('order', '>=', $newOrder)->where('order', '<', $oldOrder)->increment('order');
            }

            $property->update(['order' => $newOrder]);
            $this->normalizePropertyOrder();
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cms.properties.index')->with('success', 'Property order updated.');
    }

    public function bulkAction(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));
        $action = $request->input('action');

        if (! $ids || ! $action) {
            return response()->json(['success' => false], 422);
        }

        if ($action === 'delete') {
            $properties = Property::query()->whereIn('id', $ids)->get();
            foreach ($properties as $property) {
                MediaStorage::delete(data_get($property->metadata, 'og_image'));

                if ($property->images_directory) {
                    MediaStorage::deleteDirectory($property->images_directory);
                }

                $this->deleteManagedSectionIcons($this->managedIconPathsForProperty($property));

                $property->delete();
            }

            $this->normalizePropertyOrder();
        }

        if (in_array($action, ['active', 'activate'], true)) {
            Property::whereIn('id', $ids)->update(['status' => true]);
        }

        if (in_array($action, ['inactive', 'deactivate'], true)) {
            Property::whereIn('id', $ids)->update(['status' => false]);
        }

        return response()->json(['success' => true]);
    }
}
