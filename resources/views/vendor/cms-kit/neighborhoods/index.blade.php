@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Neighborhoods</li>
@endsection

@section('content')
@php $showLanguageUi = config('cms-kit.common.modules.languages', true); @endphp
<div class="row">
    <div class="col-12">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Neighborhoods Section Settings</h6>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('cms.neighborhoods.update-section') }}" method="POST">
                    @csrf

                    <div class="alert alert-light border-start border-primary border-4 py-2 mb-4 shadow-sm" style="font-size: 0.9rem;">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <strong>Note:</strong> These settings control the Neighborhoods section header. Required fields are marked with <span class="text-danger">*</span>.{{ $showLanguageUi ? ' Across all language tabs' : '' }}.
                    </div>

                    @if($showLanguageUi)
                        <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3" id="neighborhoodSectionTabs" role="tablist">
                            @foreach($languages as $lang)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium" data-bs-toggle="tab" data-bs-target="#neigh-section-{{ $lang->code }}" type="button" role="tab">
                                        <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="tab-content mb-4">
                        @foreach($languages as $lang)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="neigh-section-{{ $lang->code }}" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            name="translations[{{ $lang->code }}][title]"
                                            class="form-control @error("translations.{$lang->code}.title") is-invalid @enderror"
                                            value="{{ old("translations.{$lang->code}.title", $section->translations[$lang->code]['title'] ?? '') }}"
                                            required
                                        >
                                        @error("translations.{$lang->code}.title")
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                                        <textarea
                                            name="translations[{{ $lang->code }}][description]"
                                            class="form-control @error("translations.{$lang->code}.description") is-invalid @enderror"
                                            rows="3"
                                            required
                                        >{{ old("translations.{$lang->code}.description", $section->translations[$lang->code]['description'] ?? '') }}</textarea>
                                        @error("translations.{$lang->code}.description")
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row g-4 align-items-center">
                        <div class="col-md-auto me-4">
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="status" value="0">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="status"
                                    id="neighborhoodSectionStatus"
                                    value="1"
                                    {{ old('status', $section->status ?? true) ? 'checked' : '' }}
                                >
                                <label class="form-check-label fw-bold" for="neighborhoodSectionStatus">Status</label>
                            </div>
                        </div>
                        <div class="col-12 border-top pt-4">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i>Update Section Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Neighborhoods</h5>

                <div class="d-flex gap-2">
                    @if(auth('cms')->user()->can('neighborhoods.delete'))
                    <div class="dropdown" id="bulkActions" style="display: none;">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cogs"></i> Bulk Actions (<span id="selectedCount">0</span>)
                        </button>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" type="button" onclick="bulkAction('active')"><i class="fas fa-check-circle text-success me-2"></i> Activate Selected</button></li>
                            <li><button class="dropdown-item" type="button" onclick="bulkAction('inactive')"><i class="fas fa-times-circle text-secondary me-2"></i> Deactivate Selected</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" type="button" onclick="bulkAction('delete')"><i class="fas fa-trash me-2"></i> Delete Selected</button></li>
                        </ul>
                    </div>
                    @endif

                    @if(auth('cms')->user()->can('neighborhoods.create'))
                        <a href="{{ route('cms.neighborhoods.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Neighborhood
                        </a>
                    @endif
                </div>
            </div>

            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table premium-table mb-0 w-100" id="neighborhoodsTable">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    @if(auth('cms')->user()->can('neighborhoods.delete'))
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    @endif
                                </th>
                                <th style="width: 40px;">#</th>
                                <th>Name</th>
                                <th style="width: 160px;">Latitude</th>
                                <th style="width: 160px;">Longitude</th>
                                <th style="width: 120px;" class="text-center">Order</th>
                                <th style="width: 120px;" class="text-center">Status</th>
                                <th style="width: 120px;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    document.addEventListener('invalid', function(e) {
        const invalidTabPane = e.target.closest('.tab-pane');
        if (!invalidTabPane) return;

        const tabId = invalidTabPane.id;
        const tabBtn = document.querySelector(`[data-bs-target="#${tabId}"]`);

        if (tabBtn && !tabBtn.classList.contains('active')) {
            bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            setTimeout(() => { e.target.focus(); }, 150);
        }
    }, true);

    $(function () {
        const table = $('#neighborhoodsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('cms.neighborhoods.index') }}",
            columns: [
                {data: 'select_all', name: 'select_all', orderable: false, searchable: false},
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'latitude', name: 'latitude'},
                {data: 'longitude', name: 'longitude'},
                {data: 'order', name: 'order', className: 'text-center'},
                {data: 'status', name: 'status', className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end'},
            ],
            order: [[5, 'asc']],
            drawCallback: function () {
                updateBulkVisibility();
            }
        });

        function updateBulkVisibility() {
            const bulkEl = $('#bulkActions');
            if (!bulkEl.length) return;

            const checkedCount = $('.row-checkbox:checked').length;
            $('#selectedCount').text(checkedCount);
            bulkEl.toggle(checkedCount > 0);
        }

        // Toggle all checkboxes
        $(document).on('change', '#selectAll', function () {
            $('.row-checkbox').prop('checked', this.checked);
            updateBulkVisibility();
        });

        // Per-row checkbox
        $(document).on('change', '.row-checkbox', function () {
            updateBulkVisibility();
        });

        // Toggle status
        $(document).on('change', '.toggle-status', function () {
            const id = $(this).data('id');
            const url = "{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/neighborhoods/" + id + "/toggle-status";

            $.post(url, {_token: '{{ csrf_token() }}'})
                .done(() => table.ajax.reload(null, false));
        });

        // Reorder
        $(document).on('change', '.reorder-input', function () {
            const id = $(this).data('id');
            const order_index = $(this).val();

            $.post("{{ route('cms.neighborhoods.reorder') }}", {
                _token: '{{ csrf_token() }}',
                id: id,
                order_index: order_index
            }).done(() => table.ajax.reload(null, false));
        });

        // Delete single
        $(document).on('click', '.delete-item', function () {
            if (!confirm('Are you sure you want to delete this neighborhood?')) return;

            const id = $(this).data('id');
            const url = "{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/neighborhoods/" + id;

            $.ajax({
                url: url,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: () => table.ajax.reload(null, false)
            });
        });

        // Bulk actions
        window.bulkAction = function (action) {
            const checkedBoxes = $('.row-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one neighborhood.');
                return;
            }

            if (action === 'delete' && !confirm('Are you sure you want to delete selected neighborhoods?')) return;

            const ids = checkedBoxes.map(function () { return $(this).val(); }).get();

            $.post("{{ route('cms.neighborhoods.bulk-action') }}", {
                _token: "{{ csrf_token() }}",
                action: action,
                ids: ids
            }).done(() => {
                table.ajax.reload(null, false);
                $('#selectAll').prop('checked', false);
                updateBulkVisibility();
            });
        }
    });
</script>
@endpush
