<?php

namespace App\Http\Controllers\CmsKit;

use App\Http\Controllers\Controller;
use App\Models\CmsKit\HomeBannerFilterDefinition;
use App\Models\CmsKit\HomeBannerFilterValue;
use App\Models\CmsKit\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class HomeBannerFiltersController extends Controller
{
    protected function activeLanguages()
    {
        return Language::where('status', true)->get();
    }

    protected function rules(?int $id = null): array
    {
        $showLanguageUi = (bool) config('cms-kit.common.modules.languages', true);
        $activeLanguages = $this->activeLanguages();

        $baseRules = [
            'filter' => [
                'required',
                'string',
                'in:property_type,location,bedrooms,bathrooms,bed_and_baths,price',
                Rule::unique('home_banner_filter_definitions', 'key')->ignore($id),
            ],
            'ui_type' => ['required', 'in:dropdown,text,integer'],
            'columns' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'boolean'],
        ];

        if ($showLanguageUi) {
            $rules = array_merge($baseRules, [
                'translations' => ['required', 'array'],
            ]);

            foreach ($activeLanguages as $lang) {
                $rules["translations.{$lang->code}.label"] = ['required', 'string', 'max:255'];
            }

            return $rules;
        }

        return [
            ...$baseRules,
            'label' => ['required', 'string', 'max:255'],
        ];
    }

    protected function mapToSourceColumns(string $filter, ?string $columns): array
    {
        $columns = $columns !== null ? trim($columns) : null;

        return match ($filter) {
            'price' => [null, null], // Fixed values handled separately

            'property_type' => [
                'properties',
                $columns && $columns !== 'property_type' ? throw ValidationException::withMessages([
                    'columns' => ['Invalid column for Property type filter. Allowed: property_type.'],
                ]) : ($columns ?: 'property_type'),
            ],

            'location' => [
                'properties',
                in_array($columns ?: 'city,community', ['city', 'community', 'city,community'], true)
                    ? ($columns ?: 'city,community')
                    : throw ValidationException::withMessages([
                        'columns' => ['Invalid column for Location filter. Allowed: city, community, or city,community.'],
                    ]),
            ],

            'bedrooms' => [
                'properties',
                in_array($columns ?: 'bedrooms', ['bedrooms'], true)
                    ? ($columns ?: 'bedrooms')
                    : throw ValidationException::withMessages([
                        'columns' => ['Invalid column for Bedrooms filter. Allowed: bedrooms.'],
                    ]),
            ],

            'bathrooms' => [
                'properties',
                in_array($columns ?: 'bathrooms', ['bathrooms'], true)
                    ? ($columns ?: 'bathrooms')
                    : throw ValidationException::withMessages([
                        'columns' => ['Invalid column for Bathrooms filter. Allowed: bathrooms.'],
                    ]),
            ],

            'bed_and_baths' => [
                'properties',
                in_array($columns ?: 'bedrooms,bathrooms', ['bedrooms,bathrooms', 'bathrooms,bedrooms'], true)
                    ? ($columns ?: 'bedrooms,bathrooms')
                    : throw ValidationException::withMessages([
                        'columns' => ['Invalid column for Beds & Baths filter. Allowed: bedrooms,bathrooms.'],
                    ]),
            ],

            default => [null, null],
        };
    }

    protected function seedFixedPriceRanges(HomeBannerFilterDefinition $definition): void
    {
        $priceRanges = [
            '0-100000' => 'Up to 100,000 AED / yr',
            '100000-200000' => '100,000 – 200,000 AED',
            '200000-500000' => '200,000 – 500,000 AED',
            '500000+' => '500,000+ AED',
        ];

        $activeLanguages = $this->activeLanguages();
        $fallbackLocale = config('app.fallback_locale', 'en');

        $priceRangesArabic = [
            '0-100000' => 'حتى 100,000 درهم/سنة',
            '100000-200000' => '100,000 – 200,000 درهم',
            '200000-500000' => '200,000 – 500,000 درهم',
            '500000+' => '500,000+ درهم',
        ];

        $i = 1;
        foreach ($priceRanges as $value => $label) {
            $translations = [];
            foreach ($activeLanguages as $lang) {
                if ($lang->code === 'ar' && isset($priceRangesArabic[$value])) {
                    $translations[$lang->code] = ['label' => $priceRangesArabic[$value]];
                } else {
                    $translations[$lang->code] = ['label' => $label];
                }
            }

            HomeBannerFilterValue::updateOrCreate(
                ['filter_definition_id' => $definition->id, 'value' => (string) $value],
                [
                    'label' => $translations[$fallbackLocale]['label'] ?? $label,
                    'translations' => $translations,
                    'sort_order' => $i,
                    'status' => true,
                ],
            );
            $i++;
        }
    }

    public function refreshValues(?int $definitionId = null): void
    {
        $definitions = HomeBannerFilterDefinition::query()
            ->where('status', true)
            ->when($definitionId, fn ($q) => $q->where('id', $definitionId))
            ->orderBy('sort_order')
            ->get();

        $propertyTypeLabels = config('cms-kit.database.properties.property_types', []);
        $activeLanguages = $this->activeLanguages();
        $fallbackLocale = config('app.fallback_locale', 'en');

        foreach ($definitions as $definition) {
            if (! $definition->source_table || ! $definition->source_column) {
                // Fixed filters (like price ranges) are manually maintained in `home_banner_filter_values`.
                continue;
            }

            if ($definition->source_table !== 'properties') {
                continue;
            }

            $computedValues = [];
            $valueMeta = []; // for combined values, store parsed info if needed

            // Compute DISTINCT values from the selected columns.
            if ($definition->key === 'property_type') {
                $rows = DB::table('properties')
                    ->selectRaw('TRIM(property_type) as value')
                    ->where('property_type', '<>', '')
                    ->whereNotNull('property_type')
                    ->where('status', true)
                    ->whereNotNull('published_at')
                    ->distinct()
                    ->orderBy('value')
                    ->get();

                $computedValues = collect($rows)
                    ->map(fn ($r) => trim((string) $r->value))
                    ->filter(fn ($v) => $v !== '')
                    ->values()
                    ->all();
            } elseif ($definition->key === 'location') {
                $columns = array_values(array_filter(array_map('trim', explode(',', (string) $definition->source_column))));
                $allowedColumns = ['city', 'community'];
                $columns = array_values(array_intersect($columns, $allowedColumns));

                if (! $columns) {
                    continue;
                }

                $query = null;
                foreach ($columns as $col) {
                    $sub = DB::table('properties')
                        ->selectRaw('TRIM(' . $col . ') as value')
                        ->where($col, '<>', '')
                        ->whereNotNull($col)
                        ->where('status', true)
                        ->whereNotNull('published_at');

                    $query = $query ? $query->union($sub) : $sub;
                }

                $rows = $query?->get();
                if (! $rows) {
                    continue;
                }

                $computedValues = collect($rows)
                    ->map(fn ($r) => trim((string) $r->value))
                    ->filter(fn ($v) => $v !== '')
                    ->unique()
                    ->sort()
                    ->values()
                    ->all();
            } elseif ($definition->key === 'bedrooms') {
                $rows = DB::table('properties')
                    ->select('bedrooms')
                    ->whereNotNull('bedrooms')
                    ->where('bedrooms', '>', 0)
                    ->where('status', true)
                    ->whereNotNull('published_at')
                    ->distinct()
                    ->orderBy('bedrooms')
                    ->get();

                $computedValues = collect($rows)
                    ->map(fn ($r) => (string) $r->bedrooms)
                    ->values()
                    ->all();
            } elseif ($definition->key === 'bathrooms') {
                $rows = DB::table('properties')
                    ->select('bathrooms')
                    ->whereNotNull('bathrooms')
                    ->where('bathrooms', '>', 0)
                    ->where('status', true)
                    ->whereNotNull('published_at')
                    ->distinct()
                    ->orderBy('bathrooms')
                    ->get();

                $computedValues = collect($rows)
                    ->map(fn ($r) => (string) $r->bathrooms)
                    ->values()
                    ->all();
            } elseif ($definition->key === 'bed_and_baths') {
                $rows = DB::table('properties')
                    ->select('bedrooms', 'bathrooms')
                    ->whereNotNull('bedrooms')
                    ->where('bedrooms', '>', 0)
                    ->whereNotNull('bathrooms')
                    ->where('bathrooms', '>', 0)
                    ->where('status', true)
                    ->whereNotNull('published_at')
                    ->groupBy('bedrooms', 'bathrooms')
                    ->orderBy('bedrooms')
                    ->orderBy('bathrooms')
                    ->get();

                $computedValues = [];
                foreach ($rows as $r) {
                    $value = (string) $r->bedrooms . '|' . (string) $r->bathrooms;
                    $computedValues[] = $value;
                    $valueMeta[$value] = ['beds' => (int) $r->bedrooms, 'baths' => (int) $r->bathrooms];
                }
            } else {
                // Unsupported dynamic filter key.
                continue;
            }

            if (! $computedValues) {
                continue;
            }

            // Mark outdated cached values inactive (preserve the "empty/all" value if present).
            HomeBannerFilterValue::query()
                ->where('filter_definition_id', $definition->id)
                ->where('value', '!=', '')
                ->whereNotIn('value', $computedValues)
                ->update(['status' => false]);

            // Prebuild translation lookup for location.
            $locationCityMapByLang = [];
            $locationCommunityMapByLang = [];
            if ($definition->key === 'location') {
                $columns = array_values(array_filter(array_map('trim', explode(',', (string) $definition->source_column))));
                $columns = array_values(array_intersect($columns, ['city', 'community']));

                foreach ($activeLanguages as $lang) {
                    if (in_array('city', $columns, true)) {
                        $rows = DB::table('properties as p')
                            ->join('property_translations as pt', 'pt.property_id', '=', 'p.id')
                            ->selectRaw('p.city as value, pt.city as label')
                            ->where('pt.language_code', $lang->code)
                            ->whereIn('p.city', $computedValues)
                            ->where('p.city', '<>', '')
                            ->whereNotNull('p.city')
                            ->distinct()
                            ->get();

                        $locationCityMapByLang[$lang->code] = $rows->pluck('label', 'value')->toArray();
                    }

                    if (in_array('community', $columns, true)) {
                        $rows = DB::table('properties as p')
                            ->join('property_translations as pt', 'pt.property_id', '=', 'p.id')
                            ->selectRaw('p.community as value, pt.community as label')
                            ->where('pt.language_code', $lang->code)
                            ->whereIn('p.community', $computedValues)
                            ->where('p.community', '<>', '')
                            ->whereNotNull('p.community')
                            ->distinct()
                            ->get();

                        $locationCommunityMapByLang[$lang->code] = $rows->pluck('label', 'value')->toArray();
                    }
                }
            }

            foreach ($computedValues as $i => $value) {
                $translations = [];
                $labelFallback = $value;

                foreach ($activeLanguages as $lang) {
                    $label = $value;

                    if ($definition->key === 'property_type') {
                        $label = $propertyTypeLabels[$value][$lang->code]
                            ?? $propertyTypeLabels[$value][$fallbackLocale]
                            ?? $value;
                    } elseif ($definition->key === 'location') {
                        $columns = array_values(array_filter(array_map('trim', explode(',', (string) $definition->source_column))));
                        $columns = array_values(array_intersect($columns, ['city', 'community']));

                        // Prefer community label when both are enabled.
                        if (in_array('community', $columns, true) && isset($locationCommunityMapByLang[$lang->code][$value])) {
                            $label = $locationCommunityMapByLang[$lang->code][$value] ?: $value;
                        } elseif (in_array('city', $columns, true) && isset($locationCityMapByLang[$lang->code][$value])) {
                            $label = $locationCityMapByLang[$lang->code][$value] ?: $value;
                        }
                    } elseif ($definition->key === 'bedrooms' || $definition->key === 'bathrooms') {
                        // Numeric labels are the same across languages.
                        $label = $value;
                    } elseif ($definition->key === 'bed_and_baths') {
                        $meta = $valueMeta[$value] ?? ['beds' => 0, 'baths' => 0];
                        $beds = (int) ($meta['beds'] ?? 0);
                        $baths = (int) ($meta['baths'] ?? 0);

                        if ($lang->code === $fallbackLocale || $lang->code === 'en') {
                            $bedsLabel = $beds === 1 ? 'Bed' : 'Beds';
                            $bathsLabel = $baths === 1 ? 'Bath' : 'Baths';
                            $label = "{$beds} {$bedsLabel} & {$baths} {$bathsLabel}";
                        } else {
                            $bedsLabel = $beds === 1 ? 'غرفة نوم' : 'غرف نوم';
                            $bathsLabel = $baths === 1 ? 'حمام' : 'حمامات';
                            $label = "{$beds} {$bedsLabel} و {$baths} {$bathsLabel}";
                        }
                    }

                    $translations[$lang->code] = [
                        'label' => (string) $label,
                    ];

                    if ($lang->code === $fallbackLocale) {
                        $labelFallback = (string) $label;
                    }
                }

                HomeBannerFilterValue::updateOrCreate(
                    ['filter_definition_id' => $definition->id, 'value' => $value],
                    [
                        'label' => $labelFallback ?: $value,
                        'translations' => $translations,
                        'sort_order' => $i + 1,
                        'status' => true,
                    ],
                );
            }
        }
    }

    public function index()
    {
        if (request()->ajax()) {
            $cmsUser = auth('cms')->user();
            $data = HomeBannerFilterDefinition::query()
                ->withCount(['values as active_values_count' => fn ($q) => $q->where('status', true)])
                ->orderBy('sort_order')
                ->orderBy('id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('select_all', function ($row) use ($cmsUser) {
                    if (! $cmsUser?->can('home-banner-filters.delete')) {
                        return '';
                    }
                    return '<input type="checkbox" class="row-checkbox form-check-input" value="' . $row->id . '">';
                })
                ->addColumn('filter', fn ($row) => e($row->key))
                ->addColumn('values_count', fn ($row) => (string) $row->active_values_count)
                ->addColumn('order', function ($row) use ($cmsUser) {
                    if ($cmsUser?->can('home-banner-filters.edit')) {
                        return '<input type="number" min="1" class="form-control form-control-sm reorder-input" data-id="' . $row->id . '" value="' . $row->sort_order . '" style="width: 80px;">';
                    }
                    return (string) $row->sort_order;
                })
                ->addColumn('status', function ($row) use ($cmsUser) {
                    if ($cmsUser?->can('home-banner-filters.edit')) {
                        $checked = $row->status ? 'checked' : '';
                        return '<div class="form-check form-switch d-inline-block"><input class="form-check-input toggle-status" type="checkbox" data-id="' . $row->id . '" ' . $checked . '></div>';
                    }
                    return $row->status ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function ($row) use ($cmsUser) {
                    $buttons = '<div class="btn-group">';
                    if ($cmsUser?->can('home-banner-filters.edit')) {
                        $buttons .= '<a href="' . route('cms.home-banner-filters.edit', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>';
                    }
                    if ($cmsUser?->can('home-banner-filters.edit')) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-secondary refresh-item" data-id="' . $row->id . '"><i class="fas fa-sync"></i></button>';
                    }
                    if ($cmsUser?->can('home-banner-filters.delete')) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }
                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['select_all', 'status', 'order', 'action'])
                ->make(true);
        }

        return view('cms-kit::home-banner-filters.index');
    }

    public function create()
    {
        $languages = $this->activeLanguages();
        return view('cms-kit::home-banner-filters.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        [$sourceTable, $sourceColumns] = $this->mapToSourceColumns(
            (string) $validated['filter'],
            $validated['columns'] ?? null,
        );

        $fallbackLocale = config('app.fallback_locale', 'en');
        $translations = $validated['translations'] ?? [
            $fallbackLocale => ['label' => $validated['label'] ?? ''],
        ];
        $label = $translations[$fallbackLocale]['label'] ?? collect($translations)->first()['label'] ?? '';

        $definition = HomeBannerFilterDefinition::create([
            'key' => trim((string) $validated['filter']),
            'label' => trim((string) $label),
            'translations' => $translations,
            'ui_type' => $validated['ui_type'],
            'source_table' => $sourceTable,
            'source_column' => $sourceColumns,
            'sort_order' => $validated['sort_order'] ?? 1,
            'status' => $request->boolean('status', true),
        ]);

        if ($definition->key === 'price') {
            $this->seedFixedPriceRanges($definition);
        } else {
            // If it is a property-sourced filter, immediately compute cached values.
            $this->refreshValues($definition->id);
        }

        return redirect()
            ->route('cms.home-banner-filters.index')
            ->with('success', 'Home banner filter created successfully.');
    }

    public function edit(int $id)
    {
        $definition = HomeBannerFilterDefinition::findOrFail($id);

        $languages = $this->activeLanguages();
        return view('cms-kit::home-banner-filters.edit', compact('definition', 'languages'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate($this->rules($id));
        $definition = HomeBannerFilterDefinition::findOrFail($id);

        [$sourceTable, $sourceColumns] = $this->mapToSourceColumns(
            (string) $validated['filter'],
            $validated['columns'] ?? null,
        );

        $definition->forceFill([
            'key' => trim((string) $validated['filter']),
            'label' => trim((string) (
                ($validated['translations'][config('app.fallback_locale', 'en')]['label'] ?? null)
                ?? collect($validated['translations'] ?? [])->first()['label']
                ?? $definition->label
            )),
            'translations' => $validated['translations'] ?? $definition->translations,
            'ui_type' => $validated['ui_type'],
            'source_table' => $sourceTable,
            'source_column' => $sourceColumns,
            'sort_order' => $validated['sort_order'] ?? $definition->sort_order ?? 1,
            'status' => $request->boolean('status', false),
        ])->save();

        if ($definition->key === 'price') {
            $this->seedFixedPriceRanges($definition);
        } else {
            $this->refreshValues($definition->id);
        }

        return redirect()
            ->route('cms.home-banner-filters.index')
            ->with('success', 'Home banner filter updated successfully.');
    }

    public function destroy(int $id)
    {
        $definition = HomeBannerFilterDefinition::findOrFail($id);
        $definition->delete();

        return redirect()
            ->route('cms.home-banner-filters.index')
            ->with('success', 'Home banner filter deleted successfully.');
    }

    public function toggleDefinitionStatus(Request $request, int $id)
    {
        $definition = HomeBannerFilterDefinition::findOrFail($id);
        $definition->status = ! $definition->status;
        $definition->save();

        return response()->json([
            'success' => true,
            'status' => $definition->status,
        ]);
    }

    public function refresh(Request $request)
    {
        $definitionId = $request->input('definition_id');
        $definitionId = is_numeric($definitionId) ? (int) $definitionId : null;

        $this->refreshValues($definitionId);

        return redirect()->route('cms.home-banner-filters.index')->with('success', 'Filter values refreshed successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:home_banner_filter_definitions,id'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        $definition = HomeBannerFilterDefinition::findOrFail((int) $request->input('id'));
        $definition->sort_order = (int) $request->input('sort_order');
        $definition->save();

        return response()->json(['success' => true]);
    }

    public function bulkAction(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));
        $action = (string) $request->input('action', '');

        if (! $ids || $action === '') {
            return response()->json(['success' => false], 422);
        }

        if ($action === 'delete') {
            HomeBannerFilterDefinition::whereIn('id', $ids)->delete();
            return response()->json(['success' => true]);
        }

        if (in_array($action, ['active', 'activate'], true)) {
            HomeBannerFilterDefinition::whereIn('id', $ids)->update(['status' => true]);
            return response()->json(['success' => true]);
        }

        if (in_array($action, ['inactive', 'deactivate'], true)) {
            HomeBannerFilterDefinition::whereIn('id', $ids)->update(['status' => false]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422);
    }
}

