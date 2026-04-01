<?php

namespace App\Http\Controllers\CmsKit;

use App\Http\Controllers\Controller;
use App\Models\CmsKit\MissionVision;
use App\Support\MediaStorage;
use App\Models\CmsKit\Language;
use CMS\SiteManager\Support\ManagesOrderIndex;
use CMS\SiteManager\Support\ValidatesImageDimensions;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MissionVisionController extends Controller
{
    use ManagesOrderIndex, ValidatesImageDimensions;

    protected function maxItems(): int
    {
        return (int) config('cms-kit.database.mission-vision.max_items', 3);
    }

    protected function rules(bool $isUpdate = false): array
    {
        $rules = [
            'order_index' => ['nullable', 'integer', 'min:1'],
            'image' => [in_array('image', config('cms-kit.database.mission-vision.items.required', []), true) && !$isUpdate ? 'required' : 'nullable', 'image', 'max:' . (config('cms-kit.images.mission-vision.image.max_size') ?? 1024)],
            'image_alt' => ['nullable', 'string', 'max:255'],
        ];

        foreach (Language::active()->get() as $language) {
            $rules["translations.{$language->code}.title"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.description"] = ['required', 'string'];
        }

        return $rules;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cmsUser = auth('cms')->user();
            $data = MissionVision::orderBy('order_index');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('select_all', fn ($row) => '<input type="checkbox" class="row-checkbox form-check-input" value="' . $row->id . '">')
                ->addColumn('image', fn ($row) => $row->image ? '<img src="' . e(media_url($row->image)) . '" class="img-thumbnail" style="height: 40px;">' : '-')
                ->addColumn('title', fn ($row) => e($row->getTranslation('title')))
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';

                    return '<div class="form-check form-switch"><input class="form-check-input toggle-status" type="checkbox" data-id="' . $row->id . '" ' . $checked . '></div>';
                })
                ->addColumn('order', fn ($row) => '<input type="number" min="1" class="form-control form-control-sm reorder-input" data-id="' . $row->id . '" value="' . $row->order_index . '" style="width: 80px;">')
                ->addColumn('action', function ($row) use ($cmsUser) {
                    $buttons = '<div class="btn-group">';
                    if ($cmsUser?->can('mission-vision.edit')) {
                        $buttons .= '<a href="' . route('cms.mission-vision.edit', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>';
                    }
                    if ($cmsUser?->can('mission-vision.delete')) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['select_all', 'image', 'status', 'order', 'action'])
                ->make(true);
        }

        $maxItems = $this->maxItems();
        $canCreate = MissionVision::count() < $maxItems;

        return view('cms-kit::mission-vision.index', compact('maxItems', 'canCreate'));
    }

    public function create()
    {
        if (MissionVision::count() >= $this->maxItems()) {
            return redirect()->route('cms.mission-vision.index')->with('error', 'Mission & Vision allows only 3 items.');
        }

        $languages = Language::active()->get();
        $imageConfig = config('cms-kit.images.mission-vision.image', []);
        $nextOrder = MissionVision::count() + 1;

        return view('cms-kit::mission-vision.create', compact('languages', 'imageConfig', 'nextOrder'));
    }

    public function store(Request $request)
    {
        if (MissionVision::count() >= $this->maxItems()) {
            return redirect()->route('cms.mission-vision.index')->with('error', 'Mission & Vision allows only 3 items.');
        }

        $request->validate($this->rules());
        $this->validateImageWithinLimits($request, 'image', config('cms-kit.images.mission-vision.image', []), 'Image');

        $data = [
            'status' => $request->boolean('status', true),
            'image_alt' => $request->input('image_alt'),
            'translations' => $request->input('translations', []),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = MediaStorage::store($request->file('image'), 'mission-vision');
        }

        $order = $this->resolveOrderForCreate(MissionVision::class, $request->integer('order_index'));
        MissionVision::where('order_index', '>=', $order)->increment('order_index');
        $data['order_index'] = $order;

        MissionVision::create($data);

        return redirect()->route('cms.mission-vision.index')->with('success', 'Mission & Vision item created successfully.');
    }

    public function edit($id)
    {
        $item = MissionVision::findOrFail($id);
        $languages = Language::active()->get();
        $imageConfig = config('cms-kit.images.mission-vision.image', []);

        return view('cms-kit::mission-vision.edit', compact('item', 'languages', 'imageConfig'));
    }

    public function update(Request $request, $id)
    {
        $item = MissionVision::findOrFail($id);
        $rules = $this->rules(true);
        $rules['remove_image'] = ['nullable', 'boolean'];

        if (
            in_array('image', config('cms-kit.database.mission-vision.items.required', []), true)
            && $request->boolean('remove_image')
            && ! $request->hasFile('image')
        ) {
            $rules['image'][0] = 'required';
        }

        $request->validate($rules);
        $this->validateImageWithinLimits($request, 'image', config('cms-kit.images.mission-vision.image', []), 'Image');

        $data = [
            'status' => $request->has('status') ? $request->boolean('status') : $item->status,
            'image_alt' => $request->input('image_alt'),
            'translations' => $request->input('translations', []),
        ];

        if ($request->hasFile('image')) {
            if ($item->image) {
                MediaStorage::delete($item->image);
            }
            $data['image'] = MediaStorage::store($request->file('image'), 'mission-vision');
        } elseif ($request->boolean('remove_image') && $item->image) {
            MediaStorage::delete($item->image);
            $data['image'] = null;
        }

        $item->update($data);

        return redirect()->route('cms.mission-vision.index')->with('success', 'Mission & Vision item updated successfully.');
    }

    public function destroy($id)
    {
        $item = MissionVision::findOrFail($id);
        $order = $item->order_index;

        if ($item->image) {
            MediaStorage::delete($item->image);
        }

        $item->delete();
        MissionVision::where('order_index', '>', $order)->decrement('order_index');
        $this->normalizeOrderIndex(MissionVision::class);

        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $item = MissionVision::findOrFail($id);
        $item->update(['status' => !$item->status]);

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:mission_visions,id'],
            'order_index' => ['required', 'integer', 'min:1'],
        ]);

        $item = MissionVision::findOrFail($request->id);
        $newOrder = $this->resolveOrderForReorder(MissionVision::class, (int) $request->order_index);
        $oldOrder = $item->order_index;

        if ($newOrder !== $oldOrder) {
            if ($newOrder > $oldOrder) {
                MissionVision::where('order_index', '>', $oldOrder)->where('order_index', '<=', $newOrder)->decrement('order_index');
            } else {
                MissionVision::where('order_index', '>=', $newOrder)->where('order_index', '<', $oldOrder)->increment('order_index');
            }

            $item->update(['order_index' => $newOrder]);
        }

        $this->normalizeOrderIndex(MissionVision::class);

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
            $items = MissionVision::whereIn('id', $ids)->get();
            foreach ($items as $item) {
                if ($item->image) {
                    MediaStorage::delete($item->image);
                }
                $item->delete();
            }
            $this->normalizeOrderIndex(MissionVision::class);
        }

        if (in_array($action, ['active', 'activate'], true)) {
            MissionVision::whereIn('id', $ids)->update(['status' => true]);
        }

        if (in_array($action, ['inactive', 'deactivate'], true)) {
            MissionVision::whereIn('id', $ids)->update(['status' => false]);
        }

        return response()->json(['success' => true]);
    }
}
