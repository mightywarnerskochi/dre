<?php

namespace App\Http\Controllers\CmsKit;

use App\Models\CmsKit\Enquiry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class EnquiryController extends Controller
{
    private const TYPE_FORM = 'form';
    private const TYPE_PROPERTY = 'property';
    private const TYPE_BOOK_VIEWING = 'book_viewing';

    public function formIndex(Request $request)
    {
        return $this->indexByType($request, self::TYPE_FORM);
    }

    public function propertyIndex(Request $request)
    {
        return $this->indexByType($request, self::TYPE_PROPERTY);
    }

    public function formShow(Request $request, $id)
    {
        return $this->showByType($request, $id, self::TYPE_FORM);
    }

    public function propertyShow(Request $request, $id)
    {
        return $this->showByType($request, $id, self::TYPE_PROPERTY);
    }

    public function bookViewingShow(Request $request, $id)
    {
        return $this->showByType($request, $id, self::TYPE_BOOK_VIEWING);
    }

    public function formExport(Request $request)
    {
        return $this->exportByType($request, self::TYPE_FORM);
    }

    public function propertyExport(Request $request)
    {
        return $this->exportByType($request, self::TYPE_PROPERTY);
    }

    public function bookViewingExport(Request $request)
    {
        return $this->exportByType($request, self::TYPE_BOOK_VIEWING);
    }

    public function formDestroy($id)
    {
        return $this->destroyByType($id, self::TYPE_FORM);
    }

    public function propertyDestroy($id)
    {
        return $this->destroyByType($id, self::TYPE_PROPERTY);
    }

    public function bookViewingDestroy($id)
    {
        return $this->destroyByType($id, self::TYPE_BOOK_VIEWING);
    }

    public function formBulkAction(Request $request)
    {
        return $this->bulkActionByType($request, self::TYPE_FORM);
    }

    public function propertyBulkAction(Request $request)
    {
        return $this->bulkActionByType($request, self::TYPE_PROPERTY);
    }

    public function bookViewingBulkAction(Request $request)
    {
        return $this->bulkActionByType($request, self::TYPE_BOOK_VIEWING);
    }

    public function bookViewingIndex(Request $request)
    {
        return $this->indexByType($request, self::TYPE_BOOK_VIEWING);
    }

    public function index(Request $request)
    {
        return $this->formIndex($request);
    }

    public function show(Request $request, $id)
    {
        return $this->formShow($request, $id);
    }

    public function export(Request $request)
    {
        return $this->formExport($request);
    }

    public function destroy($id)
    {
        return $this->formDestroy($id);
    }

    public function bulkAction(Request $request)
    {
        return $this->formBulkAction($request);
    }

    private function indexByType(Request $request, string $type)
    {
        $meta = $this->typeMeta($type);

        if ($request->ajax()) {
            $query = $this->queryByType($type);
            $this->applyCommonFilters($query, $request, $type);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('select_all', function ($row) {
                    return '<input type="checkbox" class="row-checkbox form-check-input" value="'.e((string) $row->id).'">';
                })
                ->editColumn('id', function ($row) {
                    return (int) $row->id;
                })
                ->addColumn('property_title', function ($row) {
                    return data_get($row->extra_fields, 'property_title') ?: '-';
                })
                ->addColumn('location', function ($row) {
                    return data_get($row->extra_fields, 'location') ?: '-';
                })
                ->addColumn('property_type', function ($row) {
                    return data_get($row->extra_fields, 'property_type') ?: '-';
                })
                ->addColumn('property_size', function ($row) {
                    return data_get($row->extra_fields, 'property_size') ?: '-';
                })
                ->addColumn('page_source_label', function ($row) {
                    return $row->page_source ?: '-';
                })
                ->addColumn('subject', function ($row) {
                    return $row->subject ?: '-';
                })
                ->addColumn('date', function ($row) {
                    return optional($row->created_at)->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) use ($meta) {
                    $viewUrl = route($meta['route_prefix'].'.show', ['id' => $row->id]);

                    return '<div class="btn-group">
                                <a href="'.e($viewUrl).'" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> View</a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-item" data-id="'.e((string) $row->id).'"><i class="fas fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $sources = $this->queryByType($type)
            ->select('page_source')
            ->distinct()
            ->pluck('page_source')
            ->filter()
            ->values();

        $propertyTypes = collect();
        if ($type === self::TYPE_PROPERTY) {
            $propertyTypes = $this->queryByType($type)
                ->get()
                ->pluck('extra_fields.property_type')
                ->filter()
                ->map(static fn ($value) => trim((string) $value))
                ->filter()
                ->unique()
                ->sort()
                ->values();
        }

        $hasData = $this->queryByType($type)->exists();

        return view('cms-kit::enquiries.index', [
            'sources' => $sources,
            'hasData' => $hasData,
            'routePrefix' => $meta['route_prefix'],
            'pageHeading' => $meta['heading'],
            'listHeading' => $meta['list_heading'],
            'enquiryType' => $meta['type'],
            'propertyTypes' => $propertyTypes,
        ]);
    }

    private function showByType(Request $request, $id, string $type)
    {
        $meta = $this->typeMeta($type);
        $enquiry = $this->queryByType($type)->findOrFail($id);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json($enquiry);
        }

        $extraFields = is_array($enquiry->extra_fields) ? $enquiry->extra_fields : [];
        $details = [
            'Name' => $enquiry->name,
            'Email' => $enquiry->email,
            'Phone' => $enquiry->phone,
            'Country' => $enquiry->country,
            'Subject' => $enquiry->subject ?: data_get($extraFields, 'subject'),
            'Page Source' => $enquiry->page_source,
            'Message' => $this->sanitizeMessage($enquiry->message),
            'Date' => optional($enquiry->created_at)->format('Y-m-d H:i:s'),
        ];

        if (filled($enquiry->page_url)) {
            $details['Page URL'] = $enquiry->page_url;
        }

        foreach ($extraFields as $key => $value) {
            if (! filled($value) || in_array($key, ['subject', 'enquiry_type'], true)) {
                continue;
            }
            $label = str($key)->replace('_', ' ')->title()->toString();
            $details[$label] = $value;
        }

        return view('cms-kit::enquiries.show', [
            'enquiry' => $enquiry,
            'details' => $details,
            'routePrefix' => $meta['route_prefix'],
            'pageHeading' => $meta['heading'],
        ]);
    }

    private function exportByType(Request $request, string $type)
    {
        $query = $this->queryByType($type);
        $this->applyCommonFilters($query, $request, $type);
        $enquiries = $query->latest()->get();

        $filenamePrefix = $type === self::TYPE_PROPERTY
            ? 'property_enquiries'
            : ($type === self::TYPE_BOOK_VIEWING ? 'book_viewing_enquiries' : 'form_enquiries');
        $filename = $filenamePrefix.'_'.date('Y-m-d_H-i-s').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Name', 'Email', 'Phone', 'Company', 'Country', 'Subject', 'Page Source', 'Page URL', 'Message', 'Date'];

        $callback = function () use ($enquiries, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($enquiries as $enquiry) {
                fputcsv($file, [
                    $enquiry->id,
                    $enquiry->name,
                    $enquiry->email,
                    $enquiry->phone,
                    $enquiry->company,
                    $enquiry->country,
                    $enquiry->subject,
                    $enquiry->page_source,
                    $enquiry->page_url,
                    $enquiry->message,
                    optional($enquiry->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function destroyByType($id, string $type)
    {
        $enquiry = $this->queryByType($type)->findOrFail($id);
        $enquiry->delete();

        return response()->json(['success' => true]);
    }

    private function bulkActionByType(Request $request, string $type)
    {
        $ids = collect($request->input('ids', []))
            ->filter(static fn ($id) => is_numeric($id))
            ->map(static fn ($id) => (int) $id)
            ->values();
        $action = $request->input('action');

        if ($action === 'delete' && $ids->isNotEmpty()) {
            $query = $this->queryByType($type)->whereIn('id', $ids->all());
            $query->delete();
        }

        return response()->json(['success' => true]);
    }

    private function applyCommonFilters(Builder $query, Request $request, ?string $type = null): void
    {
        if ($request->filled('page_source') && $request->page_source !== 'All') {
            $query->where('page_source', $request->page_source);
        }

        if ($type === self::TYPE_PROPERTY && $request->filled('property_type') && $request->property_type !== 'All') {
            $query->where('extra_fields->property_type', $request->property_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->from_date));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->to_date));
        }
    }

    private function queryByType(string $type): Builder
    {
        $query = Enquiry::query();

        if ($type === self::TYPE_PROPERTY) {
            $query->where('extra_fields->enquiry_type', 'property');
        } elseif ($type === self::TYPE_BOOK_VIEWING) {
            $query->where('extra_fields->enquiry_type', self::TYPE_BOOK_VIEWING);
        } else {
            $query->where(function (Builder $innerQuery) {
                $innerQuery
                    ->whereNull('extra_fields->enquiry_type')
                    ->orWhere(function (Builder $typed) {
                        $typed
                            ->where('extra_fields->enquiry_type', '!=', self::TYPE_PROPERTY)
                            ->where('extra_fields->enquiry_type', '!=', self::TYPE_BOOK_VIEWING);
                    });
            });
        }

        return $query;
    }

    private function typeMeta(string $type): array
    {
        if ($type === self::TYPE_PROPERTY) {
            return [
                'route_prefix' => 'cms.property-enquiries',
                'heading' => 'Properties Enquiries',
                'list_heading' => 'Properties Enquiries List',
                'type' => self::TYPE_PROPERTY,
            ];
        }
        if ($type === self::TYPE_BOOK_VIEWING) {
            return [
                'route_prefix' => 'cms.book-viewing-enquiries',
                'heading' => 'Book Viewing Enquiries',
                'list_heading' => 'Book Viewing Enquiries List',
                'type' => self::TYPE_BOOK_VIEWING,
            ];
        }

        return [
            'route_prefix' => 'cms.form-enquiries',
            'heading' => 'Form Enquiries',
            'list_heading' => 'Form Enquiries List',
            'type' => self::TYPE_FORM,
        ];
    }

    private function sanitizeMessage(?string $message): ?string
    {
        $content = trim((string) $message);
        if ($content === '') {
            return null;
        }

        $lines = preg_split('/\R/u', $content) ?: [];
        $filtered = array_filter($lines, static function ($line) {
            return ! preg_match('/^\s*Page\s+(Source|URL)\s*:/i', (string) $line);
        });

        $sanitized = trim(implode(PHP_EOL, $filtered));

        return $sanitized !== '' ? $sanitized : null;
    }
}
