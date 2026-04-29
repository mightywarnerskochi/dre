<?php

namespace App\Http\Controllers\CmsKit;

use App\Http\Controllers\Controller;
use App\Models\CmsKit\Neighborhood;
use App\Models\CmsKit\Language;
use App\Models\CmsKit\SectionLabel;
use CMS\SiteManager\Support\ManagesOrderIndex;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NeighborhoodController extends Controller
{
    use ManagesOrderIndex;

    protected function activeLanguages()
    {
        return Language::where('status', true)->get();
    }

    protected function normalizedTranslations(array $translations): array
    {
        return collect($translations)->mapWithKeys(function ($translation, $code) {
            return [
                $code => [
                    'name' => trim((string) ($translation['name'] ?? '')),
                ],
            ];
        })->all();
    }

    protected function rules(): array
    {
        $rules = [
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'order_index' => ['required', 'integer', 'min:1'],
            'status' => ['nullable', 'boolean'],
        ];

        foreach ($this->activeLanguages() as $language) {
            $rules["translations.{$language->code}.name"] = [
                'required',
                'string',
                'max:255',
            ];
        }

        return $rules;
    }

    protected function getSection(): SectionLabel
    {
        return SectionLabel::firstOrCreate(['section_key' => 'neighborhoods']);
    }

    protected function sectionRules(): array
    {
        $rules = [
            'status' => ['required', 'boolean'],
        ];

        foreach ($this->activeLanguages() as $language) {
            $rules["translations.{$language->code}.title"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.description"] = ['required', 'string'];
        }

        return $rules;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cmsUser = auth('cms')->user();
            $languages = $this->activeLanguages();

            $data = Neighborhood::query()
                ->orderBy('order_index', 'asc')
                ->orderByDesc('id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('select_all', function ($row) use ($cmsUser) {
                    if (! $cmsUser?->can('neighborhoods.delete')) {
                        return '';
                    }

                    return '<input type="checkbox" class="row-checkbox form-check-input" value="'.$row->id.'">';
                })
                ->addColumn('name', fn ($row) => e($row->getTranslation('name') ?? '-'))
                ->addColumn('latitude', fn ($row) => $row->latitude !== null ? (string) $row->latitude : '-')
                ->addColumn('longitude', fn ($row) => $row->longitude !== null ? (string) $row->longitude : '-')
                ->addColumn('order', function ($row) use ($cmsUser) {
                    if ($cmsUser?->can('neighborhoods.edit')) {
                        return '<input type="number" min="1" class="form-control form-control-sm reorder-input" data-id="'.$row->id.'" value="'.$row->order_index.'" style="width: 80px;">';
                    }

                    return (string) $row->order_index;
                })
                ->addColumn('status', function ($row) use ($cmsUser) {
                    if ($cmsUser?->can('neighborhoods.edit')) {
                        $checked = $row->status ? 'checked' : '';

                        return '<div class="form-check form-switch">
                                    <input class="form-check-input toggle-status" type="checkbox" data-id="'.$row->id.'" '.$checked.'>
                                </div>';
                    }

                    return $row->status ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function ($row) use ($cmsUser) {
                    $buttons = '<div class="btn-group">';

                    if ($cmsUser?->can('neighborhoods.edit')) {
                        $buttons .= '<a href="'.route('cms.neighborhoods.edit', $row->id).'" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>';
                    }

                    if ($cmsUser?->can('neighborhoods.delete')) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->filter(function ($query) use ($request, $languages) {
                    $search = trim((string) $request->input('search.value'));

                    if ($search === '') {
                        return;
                    }

                    $query->where(function ($builder) use ($search, $languages) {
                        $builder->where('latitude', 'like', "%{$search}%")
                            ->orWhere('longitude', 'like', "%{$search}%");

                        foreach ($languages as $language) {
                            $builder->orWhere("translations->{$language->code}->name", 'like', "%{$search}%");
                        }
                    });
                })
                ->rawColumns(['select_all', 'status', 'action', 'order'])
                ->make(true);
        }

        $section = $this->getSection();
        $languages = $this->activeLanguages();

        return view('cms-kit::neighborhoods.index', compact('section', 'languages'));
    }

    public function create()
    {
        $languages = $this->activeLanguages();
        $nextOrder = Neighborhood::count() + 1;

        return view('cms-kit::neighborhoods.create', compact('languages', 'nextOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $translations = $this->normalizedTranslations($request->input('translations', []));
        $validated['translations'] = $translations;
        $validated['status'] = $request->boolean('status', true);

        $order = $this->resolveOrderForCreate(
            Neighborhood::class,
            $validated['order_index'] ? (int) $validated['order_index'] : null,
        );

        Neighborhood::where('order_index', '>=', $order)->increment('order_index');
        $validated['order_index'] = $order;

        Neighborhood::create($validated);

        return redirect()->route('cms.neighborhoods.index')->with('success', 'Neighborhood created successfully.');
    }

    public function edit($id)
    {
        $neighborhood = Neighborhood::findOrFail($id);
        $languages = $this->activeLanguages();

        return view('cms-kit::neighborhoods.edit', compact('neighborhood', 'languages'));
    }

    public function update(Request $request, $id)
    {
        $neighborhood = Neighborhood::findOrFail($id);
        $validated = $request->validate($this->rules());

        $translations = $this->normalizedTranslations($request->input('translations', []));
        $validated['translations'] = $translations;
        $validated['status'] = $request->boolean('status', false);

        $newOrder = (int) $validated['order_index'];
        $oldOrder = (int) $neighborhood->order_index;

        if ($newOrder !== $oldOrder) {
            if ($newOrder > $oldOrder) {
                Neighborhood::where('order_index', '>', $oldOrder)
                    ->where('order_index', '<=', $newOrder)
                    ->decrement('order_index');
            } else {
                Neighborhood::where('order_index', '>=', $newOrder)
                    ->where('order_index', '<', $oldOrder)
                    ->increment('order_index');
            }
            $neighborhood->order_index = $newOrder;
        }

        $neighborhood->latitude = $validated['latitude'] ?? null;
        $neighborhood->longitude = $validated['longitude'] ?? null;
        $neighborhood->translations = $validated['translations'];
        $neighborhood->status = $validated['status'];
        $neighborhood->save();

        $this->normalizeOrderIndex(Neighborhood::class);

        return redirect()->route('cms.neighborhoods.index')->with('success', 'Neighborhood updated successfully.');
    }

    public function destroy($id)
    {
        $neighborhood = Neighborhood::findOrFail($id);
        $neighborhood->delete();

        $this->normalizeOrderIndex(Neighborhood::class);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cms.neighborhoods.index')->with('success', 'Neighborhood deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $neighborhood = Neighborhood::findOrFail($id);
        $neighborhood->status = ! $neighborhood->status;
        $neighborhood->save();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cms.neighborhoods.index')->with('success', 'Neighborhood status updated.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:neighborhoods,id',
            'order_index' => 'required|integer|min:1',
        ]);

        $neighborhood = Neighborhood::findOrFail($request->id);
        $newOrder = $this->resolveOrderForReorder(Neighborhood::class, (int) $request->order_index);
        $oldOrder = (int) $neighborhood->order_index;

        if ($newOrder !== $oldOrder) {
            if ($newOrder > $oldOrder) {
                Neighborhood::where('order_index', '>', $oldOrder)
                    ->where('order_index', '<=', $newOrder)
                    ->decrement('order_index');
            } else {
                Neighborhood::where('order_index', '>=', $newOrder)
                    ->where('order_index', '<', $oldOrder)
                    ->increment('order_index');
            }
            $neighborhood->order_index = $newOrder;
            $neighborhood->save();
        }

        $this->normalizeOrderIndex(Neighborhood::class);

        return response()->json(['success' => true]);
    }

    public function bulkAction(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));
        $action = $request->input('action');

        if (! $ids || ! $action) {
            return response()->json(['success' => false], 422);
        }

        if ($action === 'delete') {
            Neighborhood::whereIn('id', $ids)->delete();
            $this->normalizeOrderIndex(Neighborhood::class);
        }

        if (in_array($action, ['active', 'activate'], true)) {
            Neighborhood::whereIn('id', $ids)->update(['status' => true]);
        }

        if (in_array($action, ['inactive', 'deactivate'], true)) {
            Neighborhood::whereIn('id', $ids)->update(['status' => false]);
        }

        return response()->json(['success' => true]);
    }

    public function updateSection(Request $request)
    {
        $section = $this->getSection();
        $request->validate($this->sectionRules());

        $data = [
            'translations' => $request->input('translations', []),
            'status' => $request->boolean('status', false),
        ];

        $section->forceFill($data)->save();

        return redirect()->route('cms.neighborhoods.index')->with('success', 'Neighborhoods section settings updated.');
    }
}
