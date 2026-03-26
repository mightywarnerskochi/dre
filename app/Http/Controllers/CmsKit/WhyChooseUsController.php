<?php

namespace App\Http\Controllers\CmsKit;

use App\Http\Controllers\Controller;
use App\Models\CmsKit\WhyChooseUs;
use CMS\SiteManager\Models\CmsKit\Language;
use CMS\SiteManager\Models\CmsKit\SectionLabel;
use CMS\SiteManager\Support\ManagesOrderIndex;
use CMS\SiteManager\Support\ValidatesImageDimensions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class WhyChooseUsController extends Controller
{
    use ManagesOrderIndex, ValidatesImageDimensions;

    protected function maxItems(): int
    {
        return (int) config('cms-kit.database.why-choose-us.max_items', 4);
    }

    protected function itemRules(): array
    {
        $rules = ['order_index' => ['nullable', 'integer', 'min:1']];

        foreach (Language::active()->get() as $language) {
            $rules["translations.{$language->code}.title"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.description"] = ['required', 'string'];
        }

        return $rules;
    }

    protected function sectionRules(): array
    {
        $rules = [
            'section_image' => ['nullable', 'image', 'max:' . (config('cms-kit.images.why-choose-us.section_image.max_size') ?? 2048)],
        ];

        foreach (Language::active()->get() as $language) {
            $rules["translations.{$language->code}.title"] = ['required', 'string', 'max:255'];
            $rules["translations.{$language->code}.subtitle"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.description"] = ['nullable', 'string'];
        }

        return $rules;
    }

    protected function getSection(): SectionLabel
    {
        return SectionLabel::firstOrCreate(['section_key' => 'why_choose_us']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cmsUser = auth('cms')->user();
            $data = WhyChooseUs::orderBy('order_index');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('select_all', fn ($row) => '<input type="checkbox" class="row-checkbox form-check-input" value="' . $row->id . '">')
                ->addColumn('title', fn ($row) => e($row->getTranslation('title')))
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';

                    return '<div class="form-check form-switch"><input class="form-check-input toggle-status" type="checkbox" data-id="' . $row->id . '" ' . $checked . '></div>';
                })
                ->addColumn('order', fn ($row) => '<input type="number" min="1" class="form-control form-control-sm reorder-input" data-id="' . $row->id . '" value="' . $row->order_index . '" style="width: 80px;">')
                ->addColumn('action', function ($row) use ($cmsUser) {
                    $buttons = '<div class="btn-group">';
                    if ($cmsUser?->can('why-choose-us.edit')) {
                        $buttons .= '<a href="' . route('cms.why-choose-us.edit', $row->id) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>';
                    }
                    if ($cmsUser?->can('why-choose-us.delete')) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['select_all', 'status', 'order', 'action'])
                ->make(true);
        }

        $section = $this->getSection();
        $languages = Language::active()->get();
        $maxItems = $this->maxItems();
        $canCreate = WhyChooseUs::count() < $maxItems;

        return view('cms-kit::why-choose-us.index', compact('section', 'languages', 'maxItems', 'canCreate'));
    }

    public function updateSection(Request $request)
    {
        $section = $this->getSection();
        $request->validate($this->sectionRules());
        $this->validateImageWithinLimits($request, 'section_image', config('cms-kit.images.why-choose-us.section_image', []), 'Section image');

        $data = [
            'translations' => $request->input('translations', []),
            'status' => $request->boolean('status'),
        ];

        if ($request->hasFile('section_image')) {
            if ($section->section_image) {
                Storage::disk('public')->delete($section->section_image);
            }

            $data['section_image'] = $request->file('section_image')->store('why-choose-us', 'public');
        }

        $section->forceFill($data)->save();

        return redirect()->route('cms.why-choose-us.index')->with('success', 'Why Choose Us section settings updated.');
    }

    public function create()
    {
        if (WhyChooseUs::count() >= $this->maxItems()) {
            return redirect()->route('cms.why-choose-us.index')->with('error', 'Why Choose Us allows only 4 items.');
        }

        $languages = Language::active()->get();
        $nextOrder = WhyChooseUs::count() + 1;

        return view('cms-kit::why-choose-us.create', compact('languages', 'nextOrder'));
    }

    public function store(Request $request)
    {
        if (WhyChooseUs::count() >= $this->maxItems()) {
            return redirect()->route('cms.why-choose-us.index')->with('error', 'Why Choose Us allows only 4 items.');
        }

        $request->validate($this->itemRules());

        $data = [
            'status' => $request->boolean('status', true),
            'translations' => $request->input('translations', []),
        ];

        $order = $this->resolveOrderForCreate(WhyChooseUs::class, $request->integer('order_index'));
        WhyChooseUs::where('order_index', '>=', $order)->increment('order_index');
        $data['order_index'] = $order;

        WhyChooseUs::create($data);

        return redirect()->route('cms.why-choose-us.index')->with('success', 'Why Choose Us item created successfully.');
    }

    public function edit($id)
    {
        $item = WhyChooseUs::findOrFail($id);
        $languages = Language::active()->get();

        return view('cms-kit::why-choose-us.edit', compact('item', 'languages'));
    }

    public function update(Request $request, $id)
    {
        $item = WhyChooseUs::findOrFail($id);
        $request->validate($this->itemRules());

        $item->update([
            'status' => $request->has('status') ? $request->boolean('status') : $item->status,
            'translations' => $request->input('translations', []),
        ]);

        return redirect()->route('cms.why-choose-us.index')->with('success', 'Why Choose Us item updated successfully.');
    }

    public function destroy($id)
    {
        $item = WhyChooseUs::findOrFail($id);
        $order = $item->order_index;
        $item->delete();

        WhyChooseUs::where('order_index', '>', $order)->decrement('order_index');
        $this->normalizeOrderIndex(WhyChooseUs::class);

        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $item = WhyChooseUs::findOrFail($id);
        $item->update(['status' => !$item->status]);

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:why_choose_us_items,id'],
            'order_index' => ['required', 'integer', 'min:1'],
        ]);

        $item = WhyChooseUs::findOrFail($request->id);
        $newOrder = $this->resolveOrderForReorder(WhyChooseUs::class, (int) $request->order_index);
        $oldOrder = $item->order_index;

        if ($newOrder !== $oldOrder) {
            if ($newOrder > $oldOrder) {
                WhyChooseUs::where('order_index', '>', $oldOrder)->where('order_index', '<=', $newOrder)->decrement('order_index');
            } else {
                WhyChooseUs::where('order_index', '>=', $newOrder)->where('order_index', '<', $oldOrder)->increment('order_index');
            }

            $item->update(['order_index' => $newOrder]);
        }

        $this->normalizeOrderIndex(WhyChooseUs::class);

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
            WhyChooseUs::whereIn('id', $ids)->delete();
            $this->normalizeOrderIndex(WhyChooseUs::class);
        }

        if (in_array($action, ['active', 'activate'], true)) {
            WhyChooseUs::whereIn('id', $ids)->update(['status' => true]);
        }

        if (in_array($action, ['inactive', 'deactivate'], true)) {
            WhyChooseUs::whereIn('id', $ids)->update(['status' => false]);
        }

        return response()->json(['success' => true]);
    }
}

