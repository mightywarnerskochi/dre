<?php

namespace App\Http\Controllers\CmsKit;

use App\Http\Controllers\Controller;
use App\Models\CmsKit\NearbyPlace;
use App\Models\CmsKit\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class NearbyPlaceController extends Controller
{
    protected function activeLanguages()
    {
        return Language::where('status', true)->get();
    }

    protected function normalizedTranslations(array $translations): array
    {
        return collect($translations)->mapWithKeys(function ($translation, $code) {
            return [$code => [
                'name' => trim((string) ($translation['name'] ?? '')),
                'address' => trim((string) ($translation['address'] ?? '')),
            ]];
        })->all();
    }

    protected function rules(): array
    {
        $rules = [
            'type' => ['required', Rule::in(array_keys($this->placeTypes()))],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['nullable', 'boolean'],
        ];

        foreach ($this->activeLanguages() as $language) {
            $rules["translations.{$language->code}.name"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.address"] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
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

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cmsUser = auth('cms')->user();
            $placeTypes = $this->placeTypes();
            $data = NearbyPlace::query()->orderBy('type')->orderBy('name');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('select_all', fn ($row) => '<input type="checkbox" class="row-checkbox form-check-input" value="' . $row->id . '">')
                ->addColumn('name', fn ($row) => e($row->getTranslation('name')))
                ->addColumn('type_label', fn ($row) => e($this->optionLabel($placeTypes, $row->type)))
                ->editColumn('address', fn ($row) => e($row->getTranslation('address') ?: '-'))
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';

                    return '<div class="form-check form-switch">
                                <input class="form-check-input toggle-status" type="checkbox" data-id="' . $row->id . '" ' . $checked . '>
                            </div>';
                })
                ->addColumn('action', function ($row) use ($cmsUser) {
                    $buttons = '<div class="btn-group">';

                    if ($cmsUser?->can('nearby-places.edit')) {
                        $buttons .= '<a href="' . route('cms.nearby-places.edit', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>';
                    }

                    if ($cmsUser?->can('nearby-places.delete')) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['select_all', 'status', 'action'])
                ->make(true);
        }

        return view('cms-kit::nearby-places.index');
    }

    public function create(Request $request)
    {
        $placeTypes = $this->placeTypes();
        $defaultType = $request->query('type');
        $languages = $this->activeLanguages();

        return view('cms-kit::nearby-places.create', compact('placeTypes', 'defaultType', 'languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $translations = $this->normalizedTranslations($request->input('translations', []));
        $fallback = config('app.fallback_locale', 'en');
        $fallbackTranslation = $translations[$fallback] ?? reset($translations) ?: [];
        $validated['status'] = $request->boolean('status', true);
        $validated['name'] = $fallbackTranslation['name'] ?? '';
        $validated['address'] = $fallbackTranslation['address'] ?? null;
        $validated['translations'] = $translations;

        NearbyPlace::create($validated);

        return redirect()->route('cms.nearby-places.index')->with('success', 'Nearby place created successfully.');
    }

    public function edit($id)
    {
        $place = NearbyPlace::findOrFail($id);
        $placeTypes = $this->placeTypes();
        $languages = $this->activeLanguages();

        return view('cms-kit::nearby-places.edit', compact('place', 'placeTypes', 'languages'));
    }

    public function update(Request $request, $id)
    {
        $place = NearbyPlace::findOrFail($id);
        $validated = $request->validate($this->rules());
        $translations = $this->normalizedTranslations($request->input('translations', []));
        $fallback = config('app.fallback_locale', 'en');
        $fallbackTranslation = $translations[$fallback] ?? reset($translations) ?: [];
        $validated['status'] = $request->boolean('status', false);
        $validated['name'] = $fallbackTranslation['name'] ?? $place->name;
        $validated['address'] = $fallbackTranslation['address'] ?? null;
        $validated['translations'] = $translations;

        $place->update($validated);

        return redirect()->route('cms.nearby-places.index')->with('success', 'Nearby place updated successfully.');
    }

    public function destroy($id)
    {
        NearbyPlace::findOrFail($id)->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cms.nearby-places.index')->with('success', 'Nearby place deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $place = NearbyPlace::findOrFail($id);
        $place->update(['status' => !$place->status]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cms.nearby-places.index')->with('success', 'Nearby place status updated.');
    }

    public function bulkAction(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));
        $action = $request->input('action');

        if (!$ids || !$action) {
            return response()->json(['success' => false], 422);
        }

        if ($action === 'delete') {
            NearbyPlace::whereIn('id', $ids)->delete();
        }

        if (in_array($action, ['active', 'activate'], true)) {
            NearbyPlace::whereIn('id', $ids)->update(['status' => true]);
        }

        if (in_array($action, ['inactive', 'deactivate'], true)) {
            NearbyPlace::whereIn('id', $ids)->update(['status' => false]);
        }

        return response()->json(['success' => true]);
    }
}
