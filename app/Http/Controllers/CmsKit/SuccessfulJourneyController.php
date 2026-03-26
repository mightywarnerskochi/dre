<?php

namespace App\Http\Controllers\CmsKit;

use App\Http\Controllers\Controller;
use App\Models\CmsKit\SuccessfulJourney;
use CMS\SiteManager\Models\CmsKit\Language;
use CMS\SiteManager\Support\ManagesOrderIndex;
use CMS\SiteManager\Support\ValidatesImageDimensions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SuccessfulJourneyController extends Controller
{
    use ManagesOrderIndex, ValidatesImageDimensions;

    protected function rules(bool $isUpdate = false): array
    {
        $rules = [
            'year' => ['required', 'string', 'max:20'],
            'order_index' => ['nullable', 'integer', 'min:1'],
            'image_1' => [in_array('image_1', config('cms-kit.database.successful-journeys.items.required', []), true) && !$isUpdate ? 'required' : 'nullable', 'image', 'max:' . (config('cms-kit.images.successful-journeys.image_1.max_size') ?? 1024)],
            'image_2' => [in_array('image_2', config('cms-kit.database.successful-journeys.items.required', []), true) && !$isUpdate ? 'required' : 'nullable', 'image', 'max:' . (config('cms-kit.images.successful-journeys.image_2.max_size') ?? 1024)],
            'image_1_alt' => ['nullable', 'string', 'max:255'],
            'image_2_alt' => ['nullable', 'string', 'max:255'],
        ];

        foreach (Language::active()->get() as $language) {
            $rules["translations.{$language->code}.description"] = ['required', 'string'];
        }

        return $rules;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cmsUser = auth('cms')->user();
            $data = SuccessfulJourney::orderBy('order_index');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('select_all', fn ($row) => '<input type="checkbox" class="row-checkbox form-check-input" value="' . $row->id . '">')
                ->addColumn('image', fn ($row) => $row->image_1 ? '<img src="' . asset('storage/' . $row->image_1) . '" class="img-thumbnail" style="height: 40px;">' : '-')
                ->addColumn('year', fn ($row) => e($row->year))
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';

                    return '<div class="form-check form-switch"><input class="form-check-input toggle-status" type="checkbox" data-id="' . $row->id . '" ' . $checked . '></div>';
                })
                ->addColumn('order', fn ($row) => '<input type="number" min="1" class="form-control form-control-sm reorder-input" data-id="' . $row->id . '" value="' . $row->order_index . '" style="width: 80px;">')
                ->addColumn('action', function ($row) use ($cmsUser) {
                    $buttons = '<div class="btn-group">';
                    if ($cmsUser?->can('successful-journeys.edit')) {
                        $buttons .= '<a href="' . route('cms.successful-journeys.edit', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>';
                    }
                    if ($cmsUser?->can('successful-journeys.delete')) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['select_all', 'image', 'status', 'order', 'action'])
                ->make(true);
        }

        return view('cms-kit::successful-journeys.index');
    }

    public function create()
    {
        $languages = Language::active()->get();
        $image1Config = config('cms-kit.images.successful-journeys.image_1', []);
        $image2Config = config('cms-kit.images.successful-journeys.image_2', []);
        $nextOrder = SuccessfulJourney::count() + 1;

        return view('cms-kit::successful-journeys.create', compact('languages', 'image1Config', 'image2Config', 'nextOrder'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());
        $this->validateImageWithinLimits($request, 'image_1', config('cms-kit.images.successful-journeys.image_1', []), 'Image 1');
        $this->validateImageWithinLimits($request, 'image_2', config('cms-kit.images.successful-journeys.image_2', []), 'Image 2');

        $data = [
            'year' => $request->input('year'),
            'status' => $request->boolean('status', true),
            'image_1_alt' => $request->input('image_1_alt'),
            'image_2_alt' => $request->input('image_2_alt'),
            'translations' => $request->input('translations', []),
        ];

        foreach (['image_1', 'image_2'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('successful-journeys', 'public');
            }
        }

        $order = $this->resolveOrderForCreate(SuccessfulJourney::class, $request->integer('order_index'));
        SuccessfulJourney::where('order_index', '>=', $order)->increment('order_index');
        $data['order_index'] = $order;

        SuccessfulJourney::create($data);

        return redirect()->route('cms.successful-journeys.index')->with('success', 'Successful Journey created successfully.');
    }

    public function edit($id)
    {
        $item = SuccessfulJourney::findOrFail($id);
        $languages = Language::active()->get();
        $image1Config = config('cms-kit.images.successful-journeys.image_1', []);
        $image2Config = config('cms-kit.images.successful-journeys.image_2', []);

        return view('cms-kit::successful-journeys.edit', compact('item', 'languages', 'image1Config', 'image2Config'));
    }

    public function update(Request $request, $id)
    {
        $item = SuccessfulJourney::findOrFail($id);

        $request->validate($this->rules(true));
        $this->validateImageWithinLimits($request, 'image_1', config('cms-kit.images.successful-journeys.image_1', []), 'Image 1');
        $this->validateImageWithinLimits($request, 'image_2', config('cms-kit.images.successful-journeys.image_2', []), 'Image 2');

        $data = [
            'year' => $request->input('year'),
            'status' => $request->has('status') ? $request->boolean('status') : $item->status,
            'image_1_alt' => $request->input('image_1_alt'),
            'image_2_alt' => $request->input('image_2_alt'),
            'translations' => $request->input('translations', []),
        ];

        foreach (['image_1', 'image_2'] as $field) {
            if ($request->hasFile($field)) {
                if ($item->{$field}) {
                    Storage::disk('public')->delete($item->{$field});
                }
                $data[$field] = $request->file($field)->store('successful-journeys', 'public');
            }
        }

        $item->update($data);

        return redirect()->route('cms.successful-journeys.index')->with('success', 'Successful Journey updated successfully.');
    }

    public function destroy($id)
    {
        $item = SuccessfulJourney::findOrFail($id);
        $order = $item->order_index;

        foreach (['image_1', 'image_2'] as $field) {
            if ($item->{$field}) {
                Storage::disk('public')->delete($item->{$field});
            }
        }

        $item->delete();
        SuccessfulJourney::where('order_index', '>', $order)->decrement('order_index');
        $this->normalizeOrderIndex(SuccessfulJourney::class);

        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $item = SuccessfulJourney::findOrFail($id);
        $item->update(['status' => !$item->status]);

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:successful_journeys,id'],
            'order_index' => ['required', 'integer', 'min:1'],
        ]);

        $item = SuccessfulJourney::findOrFail($request->id);
        $newOrder = $this->resolveOrderForReorder(SuccessfulJourney::class, (int) $request->order_index);
        $oldOrder = $item->order_index;

        if ($newOrder !== $oldOrder) {
            if ($newOrder > $oldOrder) {
                SuccessfulJourney::where('order_index', '>', $oldOrder)->where('order_index', '<=', $newOrder)->decrement('order_index');
            } else {
                SuccessfulJourney::where('order_index', '>=', $newOrder)->where('order_index', '<', $oldOrder)->increment('order_index');
            }

            $item->update(['order_index' => $newOrder]);
        }

        $this->normalizeOrderIndex(SuccessfulJourney::class);

        return response()->json(['success' => true]);
    }

    public function bulkAction(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));
        $action = $request->input('action');

        if (!$ids || !$action) {
            return response()->json(['success' => false], 422);
        }

        if ($action === 'delete') {
            $items = SuccessfulJourney::whereIn('id', $ids)->get();
            foreach ($items as $item) {
                foreach (['image_1', 'image_2'] as $field) {
                    if ($item->{$field}) {
                        Storage::disk('public')->delete($item->{$field});
                    }
                }
                $item->delete();
            }
            $this->normalizeOrderIndex(SuccessfulJourney::class);
        }

        if (in_array($action, ['active', 'activate'], true)) {
            SuccessfulJourney::whereIn('id', $ids)->update(['status' => true]);
        }

        if (in_array($action, ['inactive', 'deactivate'], true)) {
            SuccessfulJourney::whereIn('id', $ids)->update(['status' => false]);
        }

        return response()->json(['success' => true]);
    }
}
