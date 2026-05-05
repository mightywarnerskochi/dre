<?php

namespace App\Http\Controllers\CmsKit;

use App\Http\Controllers\Controller;
use App\Models\CmsKit\Agent;
use App\Support\MediaStorage;
use App\Models\CmsKit\Language;
use CMS\SiteManager\Support\ValidatesImageDimensions;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AgentController extends Controller
{
    use ValidatesImageDimensions;

    protected function activeLanguages()
    {
        return Language::where('status', true)->get();
    }

    protected function normalizedTranslations(array $translations): array
    {
        return collect($translations)->mapWithKeys(function ($translation, $code) {
            return [$code => [
                'name' => trim((string) ($translation['name'] ?? '')),
                'designation' => trim((string) ($translation['designation'] ?? '')),
                'description' => trim((string) ($translation['description'] ?? '')),
            ]];
        })->all();
    }

    protected function rules(bool $isUpdate = false): array
    {
        $imageConfig = config('cms-kit.images.agents.image', []);

        $rules = [
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'image' => [$isUpdate ? 'nullable' : 'nullable', 'image', 'max:' . ($imageConfig['max_size'] ?? 2048)],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:255'],
            'languages' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
        ];

        foreach ($this->activeLanguages() as $language) {
            $rules["translations.{$language->code}.name"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.designation"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.description"] = ['nullable', 'string'];
        }

        return $rules;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cmsUser = auth('cms')->user();
            $data = Agent::query()->latest('id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('select_all', fn ($row) => '<input type="checkbox" class="row-checkbox form-check-input" value="' . $row->id . '">')
                ->addColumn('image', function ($row) {
                    return $row->image
                        ? '<img src="' . e(media_url($row->image)) . '" class="img-thumbnail" style="height: 40px;">'
                        : '-';
                })
                ->addColumn('name', function ($row) {
                    return '<div class="fw-semibold">' . e($row->getTranslation('name')) . '</div><small class="text-muted">' . e($row->email ?? '-') . '</small>';
                })
                ->addColumn('languages', fn ($row) => e($row->languages ?: '-'))
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';

                    return '<div class="form-check form-switch">
                                <input class="form-check-input toggle-status" type="checkbox" data-id="' . $row->id . '" ' . $checked . '>
                            </div>';
                })
                ->addColumn('action', function ($row) use ($cmsUser) {
                    $buttons = '<div class="btn-group">';

                    if ($cmsUser?->can('agents.edit')) {
                        $buttons .= '<a href="' . route('cms.agents.edit', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>';
                    }

                    if ($cmsUser?->can('agents.delete')) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['select_all', 'image', 'name', 'status', 'action'])
                ->make(true);
        }

        return view('cms-kit::agents.index');
    }

    public function create()
    {
        $languages = $this->activeLanguages();
        $imageConfig = config('cms-kit.images.agents.image', []);

        return view('cms-kit::agents.create', compact('languages', 'imageConfig'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $this->validateImageWithinLimits($request, 'image', config('cms-kit.images.agents.image', []), 'Agent image');
        $translations = $this->normalizedTranslations($request->input('translations', []));
        $fallback = config('app.fallback_locale', 'en');
        $fallbackTranslation = $translations[$fallback] ?? reset($translations) ?: [];
        $validated['status'] = $request->boolean('status', true);
        $validated['name'] = $fallbackTranslation['name'] ?? '';
        $validated['designation'] = $fallbackTranslation['designation'] ?? null;
        $validated['description'] = $fallbackTranslation['description'] ?? null;
        $validated['translations'] = $translations;

        if ($request->hasFile('image')) {
            $validated['image'] = MediaStorage::store($request->file('image'), 'agents');
        }

        Agent::create($validated);

        return redirect()->route('cms.agents.index')->with('success', 'Agent created successfully.');
    }

    public function edit($id)
    {
        $agent = Agent::findOrFail($id);
        $languages = $this->activeLanguages();
        $imageConfig = config('cms-kit.images.agents.image', []);

        return view('cms-kit::agents.edit', compact('agent', 'languages', 'imageConfig'));
    }

    public function update(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);
        $validated = $request->validate($this->rules(true));
        $this->validateImageWithinLimits($request, 'image', config('cms-kit.images.agents.image', []), 'Agent image');
        $translations = $this->normalizedTranslations($request->input('translations', []));
        $fallback = config('app.fallback_locale', 'en');
        $fallbackTranslation = $translations[$fallback] ?? reset($translations) ?: [];
        $validated['status'] = $request->boolean('status', false);
        $validated['name'] = $fallbackTranslation['name'] ?? $agent->name;
        $validated['designation'] = $fallbackTranslation['designation'] ?? null;
        $validated['description'] = $fallbackTranslation['description'] ?? null;
        $validated['translations'] = $translations;

        if ($request->hasFile('image')) {
            if ($agent->image) {
                MediaStorage::delete($agent->image);
            }

            $validated['image'] = MediaStorage::store($request->file('image'), 'agents');
        }

        $agent->update($validated);

        return redirect()->route('cms.agents.index')->with('success', 'Agent updated successfully.');
    }

    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);

        if ($agent->image) {
            MediaStorage::delete($agent->image);
        }

        $agent->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cms.agents.index')->with('success', 'Agent deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->update(['status' => !$agent->status]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cms.agents.index')->with('success', 'Agent status updated.');
    }

    public function bulkAction(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));
        $action = $request->input('action');

        if (!$ids || !$action) {
            return response()->json(['success' => false], 422);
        }

        if ($action === 'delete') {
            $agents = Agent::whereIn('id', $ids)->get();
            foreach ($agents as $agent) {
                if ($agent->image) {
                    MediaStorage::delete($agent->image);
                }

                $agent->delete();
            }
        }

        if (in_array($action, ['active', 'activate'], true)) {
            Agent::whereIn('id', $ids)->update(['status' => true]);
        }

        if (in_array($action, ['inactive', 'deactivate'], true)) {
            Agent::whereIn('id', $ids)->update(['status' => false]);
        }

        return response()->json(['success' => true]);
    }
}
