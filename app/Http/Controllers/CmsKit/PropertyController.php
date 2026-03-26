<?php

namespace App\Http\Controllers\CmsKit;

use App\Http\Controllers\Controller;
use App\Models\CmsKit\Agent;
use App\Models\CmsKit\NearbyPlace;
use App\Models\CmsKit\Property;
use App\Models\CmsKit\PropertyImage;
use CMS\SiteManager\Models\CmsKit\Language;
use CMS\SiteManager\Support\ValidatesImageDimensions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        if (!$key) {
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

    protected function rules(?Property $property = null): array
    {
        $propertyId = $property?->id;
        $languages = $this->activeLanguages();
        $imageConfig = config('cms-kit.images.properties.image', []);
        $rules = [
            'prop_id' => ['nullable', 'string', 'max:255', Rule::unique('properties', 'prop_id')->ignore($propertyId)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('properties', 'slug')->ignore($propertyId)],
            'property_type' => ['required', Rule::in(array_keys($this->propertyTypes()))],
            'listing_type' => ['required', Rule::in(array_keys($this->listingTypes()))],
            'source_type' => ['required', Rule::in(array_keys($this->sourceTypes()))],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'sqft' => ['nullable', 'integer', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'agent_id' => ['nullable', 'integer', Rule::exists('agents', 'id')],
            'status' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:1'],
            'published_at' => ['nullable', 'date'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:' . (now()->year + 1)],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'direct_from_owner' => ['nullable', 'string', 'max:255'],
            'nearby_places' => ['nullable', 'array'],
            'nearby_places.*' => ['nullable', 'array'],
            'nearby_places.*.*' => ['integer', Rule::exists('nearby_places', 'id')],
            'existing_images' => ['nullable', 'array'],
            'existing_images.*.alt_text' => ['nullable', 'string', 'max:255'],
            'existing_images.*.order' => ['nullable', 'integer', 'min:1'],
            'existing_images.*.is_featured' => ['nullable', 'boolean'],
            'existing_images.*.delete' => ['nullable', 'boolean'],
            'new_images' => ['nullable', 'array'],
            'new_images.*.file' => ['nullable', 'image', 'max:' . ($imageConfig['max_size'] ?? 4096)],
            'new_images.*.alt_text' => ['nullable', 'string', 'max:255'],
            'new_images.*.order' => ['nullable', 'integer', 'min:1'],
            'new_images.*.is_featured' => ['nullable', 'boolean'],
        ];

        foreach ($languages as $language) {
            $rules["translations.{$language->code}.title"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.description"] = ['nullable', 'string'];
            $rules["translations.{$language->code}.address"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.city"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.community"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.country"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.zip_code"] = ['nullable', 'string', 'max:50'];
            $rules["translations.{$language->code}.easy_to_access"] = ['nullable', 'array'];
            $rules["translations.{$language->code}.easy_to_access.*.icon"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.easy_to_access.*.label"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.key_features"] = ['nullable', 'array'];
            $rules["translations.{$language->code}.key_features.*.text"] = ['nullable', 'string', 'max:2000'];
            $rules["translations.{$language->code}.amenities"] = ['nullable', 'array'];
            $rules["translations.{$language->code}.amenities.*.icon"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.amenities.*.name"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.property_attributes"] = ['nullable', 'array'];
            $rules["translations.{$language->code}.property_attributes.*.icon"] = ['nullable', 'string', 'max:255'];
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

    protected function buildFullAddressFromParts(array $parts): string
    {
        return collect([
            trim((string) ($parts['address'] ?? '')),
            trim((string) ($parts['community'] ?? '')),
            trim((string) ($parts['city'] ?? '')),
            trim((string) ($parts['zip_code'] ?? '')),
            trim((string) ($parts['country'] ?? '')),
        ])
            ->filter(fn ($value) => $value !== '')
            ->implode(', ');
    }

    protected function normalizedTranslations(array $translations): array
    {
        return collect($translations)
            ->map(function ($translation, $code) {
                $normalizedAddress = [
                    'address' => trim((string) ($translation['address'] ?? '')),
                    'community' => trim((string) ($translation['community'] ?? '')),
                    'city' => trim((string) ($translation['city'] ?? '')),
                    'zip_code' => trim((string) ($translation['zip_code'] ?? '')),
                    'country' => trim((string) ($translation['country'] ?? '')),
                ];

                return [
                    'language_code' => $code,
                    'title' => trim((string) ($translation['title'] ?? '')),
                    'description' => trim((string) ($translation['description'] ?? '')),
                    'address' => $normalizedAddress['address'],
                    'full_address' => $this->buildFullAddressFromParts($normalizedAddress),
                    'city' => $normalizedAddress['city'],
                    'community' => $normalizedAddress['community'],
                    'country' => $normalizedAddress['country'],
                    'zip_code' => $normalizedAddress['zip_code'],
                    'easy_to_access' => $this->normalizedRows($translation['easy_to_access'] ?? [], ['icon', 'label']),
                    'key_features' => $this->normalizedRows($translation['key_features'] ?? [], ['text']),
                    'amenities' => $this->normalizedRows($translation['amenities'] ?? [], ['icon', 'name']),
                    'property_attributes' => $this->normalizedRows($translation['property_attributes'] ?? [], ['icon', 'name']),
                ];
            })
            ->filter(function ($translation) {
                return $translation['title'] !== ''
                    || $translation['description'] !== ''
                    || $translation['address'] !== ''
                    || $translation['full_address'] !== ''
                    || !empty($translation['easy_to_access'])
                    || !empty($translation['key_features'])
                    || !empty($translation['amenities'])
                    || !empty($translation['property_attributes']);
            })
            ->values()
            ->all();
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
            $slug = $baseSlug . '-' . $suffix;
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

    protected function syncImages(Property $property, Request $request): void
    {
        $featuredImageId = null;

        foreach ((array) $request->input('existing_images', []) as $imageId => $payload) {
            $image = $property->images()->whereKey($imageId)->first();

            if (!$image) {
                continue;
            }

            if (!empty($payload['delete'])) {
                Storage::disk('public')->delete($image->image);
                $image->delete();
                continue;
            }

            $isFeatured = !empty($payload['is_featured']);
            if ($isFeatured && $featuredImageId === null) {
                $featuredImageId = $image->id;
            }

            $image->update([
                'alt_text' => trim((string) ($payload['alt_text'] ?? '')),
                'order' => max(1, (int) ($payload['order'] ?? $image->order ?? 1)),
                'is_featured' => false,
            ]);
        }

        foreach ((array) $request->input('new_images', []) as $index => $payload) {
            $file = $request->file("new_images.{$index}.file");
            if (!$file) {
                continue;
            }

            $image = $property->images()->create([
                'image' => $file->store('properties', 'public'),
                'alt_text' => trim((string) ($payload['alt_text'] ?? '')),
                'order' => max(1, (int) ($payload['order'] ?? ($property->images()->max('order') + 1))),
                'is_featured' => false,
            ]);

            if (!empty($payload['is_featured']) && $featuredImageId === null) {
                $featuredImageId = $image->id;
            }
        }

        $property->images()->update(['is_featured' => false]);

        $fallbackFeatured = $featuredImageId ?: $property->images()->orderBy('order')->value('id');
        if ($fallbackFeatured) {
            $property->images()->whereKey($fallbackFeatured)->update(['is_featured' => true]);
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cmsUser = auth('cms')->user();
            $propertyTypes = $this->propertyTypes();
            $listingTypes = $this->listingTypes();
            $data = Property::query()->with(['agent', 'featuredImage'])->ordered();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('select_all', fn ($row) => '<input type="checkbox" class="row-checkbox form-check-input" value="' . $row->id . '">')
                ->addColumn('property', function ($row) {
                    $image = $row->featuredImage
                        ? '<img src="' . asset('storage/' . $row->featuredImage->image) . '" class="rounded border me-2" style="height:40px;width:60px;object-fit:cover;">'
                        : '';

                    return '<div class="d-flex align-items-center">' . $image . '<div><div class="fw-semibold">' . e($row->title) . '</div><small class="text-muted">' . e(trim(($row->city ?: '-') . ($row->community ? ' / ' . $row->community : ''))) . '</small></div></div>';
                })
                ->addColumn('type', fn ($row) => e($this->optionLabel($propertyTypes, $row->property_type) . ' / ' . $this->optionLabel($listingTypes, $row->listing_type)))
                ->addColumn('price_label', function ($row) {
                    return e(trim(($row->currency ?: '') . ' ' . ($row->price ? number_format((float) $row->price, 2) : '-')));
                })
                ->addColumn('agent_name', fn ($row) => e($row->agent?->name ?? '-'))
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';

                    return '<div class="form-check form-switch">
                                <input class="form-check-input toggle-status" type="checkbox" data-id="' . $row->id . '" ' . $checked . '>
                            </div>';
                })
                ->addColumn('order_input', function ($row) {
                    return '<input type="number" min="1" class="form-control form-control-sm reorder-input" data-id="' . $row->id . '" value="' . $row->order . '" style="width: 80px;">';
                })
                ->addColumn('action', function ($row) use ($cmsUser) {
                    $buttons = '<div class="btn-group">';

                    if ($cmsUser?->can('property.edit')) {
                        $buttons .= '<a href="' . route('cms.properties.edit', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>';
                    }

                    if ($cmsUser?->can('property.delete')) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['select_all', 'property', 'status', 'order_input', 'action'])
                ->make(true);
        }

        return view('cms-kit::properties.index');
    }

    public function create()
    {
        $languages = $this->activeLanguages();
        $agents = Agent::query()->where('status', true)->orderBy('name')->get();
        $nearbyPlaces = $this->groupedNearbyPlaces();
        $propertyTypes = $this->propertyTypes();
        $listingTypes = $this->listingTypes();
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
        foreach (array_keys((array) $request->input('new_images', [])) as $index) {
            $this->validateImageWithinLimits($request, "new_images.{$index}.file", config('cms-kit.images.properties.image', []), 'Property image');
        }
        $translations = $this->normalizedTranslations($request->input('translations', []));
        $order = $this->resolveOrderForCreate($request->filled('order') ? (int) $request->order : null);
        $fallbackLocale = config('app.fallback_locale', 'en');
        $fallbackTranslation = collect($translations)->firstWhere('language_code', $fallbackLocale) ?? ($translations[0] ?? []);

        DB::transaction(function () use ($request, $translations, $order, $fallbackTranslation) {
            Property::where('order', '>=', $order)->increment('order');

            $property = Property::create([
                'prop_id' => trim((string) $request->input('prop_id')),
                'title' => $fallbackTranslation['title'] ?? '',
                'slug' => $this->resolveSlug($request, $translations),
                'property_type' => $request->input('property_type'),
                'listing_type' => $request->input('listing_type'),
                'source_type' => $request->input('source_type', 'manual'),
                'price' => $request->input('price'),
                'currency' => trim((string) $request->input('currency')),
                'bedrooms' => $request->input('bedrooms'),
                'bathrooms' => $request->input('bathrooms'),
                'sqft' => $request->input('sqft'),
                'address' => trim((string) ($fallbackTranslation['address'] ?? '')),
                'full_address' => trim((string) ($fallbackTranslation['full_address'] ?? '')),
                'zip_code' => trim((string) ($fallbackTranslation['zip_code'] ?? '')),
                'city' => trim((string) ($fallbackTranslation['city'] ?? '')),
                'community' => trim((string) ($fallbackTranslation['community'] ?? '')),
                'country' => trim((string) ($fallbackTranslation['country'] ?? '')),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'agent_id' => $request->input('agent_id'),
                'status' => $request->boolean('status', true),
                'order' => $order,
                'published_at' => $request->input('published_at') ?: now(),
            ]);

            $property->details()->create([
                'description' => $fallbackTranslation['description'] ?? '',
                'year_built' => $request->input('year_built'),
                'security_deposit' => $request->input('security_deposit'),
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
            ->with(['details', 'images', 'translations', 'nearbyPlaces'])
            ->findOrFail($id);
        $languages = $this->activeLanguages();
        $agents = Agent::query()->where('status', true)->orderBy('name')->get();
        $nearbyPlaces = $this->groupedNearbyPlaces();
        $propertyTypes = $this->propertyTypes();
        $listingTypes = $this->listingTypes();
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
            'sourceTypes',
            'currencies',
            'placeTypes',
            'propertyImageConfig'
        ));
    }

    public function update(Request $request, $id)
    {
        $property = Property::query()->with(['details', 'images', 'translations'])->findOrFail($id);
        $request->validate($this->rules($property));
        foreach (array_keys((array) $request->input('new_images', [])) as $index) {
            $this->validateImageWithinLimits($request, "new_images.{$index}.file", config('cms-kit.images.properties.image', []), 'Property image');
        }
        $translations = $this->normalizedTranslations($request->input('translations', []));
        $fallbackLocale = config('app.fallback_locale', 'en');
        $fallbackTranslation = collect($translations)->firstWhere('language_code', $fallbackLocale) ?? ($translations[0] ?? []);

        DB::transaction(function () use ($request, $property, $translations, $fallbackTranslation) {
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
                'prop_id' => trim((string) $request->input('prop_id')),
                'title' => $fallbackTranslation['title'] ?? $property->title,
                'slug' => $this->resolveSlug($request, $translations, $property->id),
                'property_type' => $request->input('property_type'),
                'listing_type' => $request->input('listing_type'),
                'source_type' => $request->input('source_type', 'manual'),
                'price' => $request->input('price'),
                'currency' => trim((string) $request->input('currency')),
                'bedrooms' => $request->input('bedrooms'),
                'bathrooms' => $request->input('bathrooms'),
                'sqft' => $request->input('sqft'),
                'address' => trim((string) ($fallbackTranslation['address'] ?? '')),
                'full_address' => trim((string) ($fallbackTranslation['full_address'] ?? '')),
                'zip_code' => trim((string) ($fallbackTranslation['zip_code'] ?? '')),
                'city' => trim((string) ($fallbackTranslation['city'] ?? '')),
                'community' => trim((string) ($fallbackTranslation['community'] ?? '')),
                'country' => trim((string) ($fallbackTranslation['country'] ?? '')),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'agent_id' => $request->input('agent_id'),
                'status' => $request->boolean('status', false),
                'order' => $newOrder,
                'published_at' => $request->input('published_at') ?: $property->published_at,
            ]);

            $property->details()->updateOrCreate(
                ['property_id' => $property->id],
                [
                    'description' => $fallbackTranslation['description'] ?? '',
                    'year_built' => $request->input('year_built'),
                    'security_deposit' => $request->input('security_deposit'),
                    'direct_from_owner' => trim((string) $request->input('direct_from_owner')),
                    'easy_to_access' => $fallbackTranslation['easy_to_access'] ?? [],
                    'key_features' => $fallbackTranslation['key_features'] ?? [],
                    'amenities' => $fallbackTranslation['amenities'] ?? [],
                    'property_attributes' => $fallbackTranslation['property_attributes'] ?? [],
                ]
            );

            $this->syncTranslations($property, $translations);
            $this->syncNearbyPlaces($property, (array) $request->input('nearby_places', []));
            $this->syncImages($property, $request);
            $this->normalizePropertyOrder();
        });

        return redirect()->route('cms.properties.index')->with('success', 'Property updated successfully.');
    }

    public function destroy($id)
    {
        $property = Property::query()->with('images')->findOrFail($id);
        $order = $property->order;

        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

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
        $property->update(['status' => !$property->status]);

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

        if (!$ids || !$action) {
            return response()->json(['success' => false], 422);
        }

        if ($action === 'delete') {
            $properties = Property::query()->with('images')->whereIn('id', $ids)->get();
            foreach ($properties as $property) {
                foreach ($property->images as $image) {
                    Storage::disk('public')->delete($image->image);
                }

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
