@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Site Filters</li>
@endsection

@section('content')
    @php
        $canEdit = auth('cms')->user()?->can('home-banner-filters.edit') ?? false;
        $canCreate = auth('cms')->user()?->can('home-banner-filters.create') ?? false;
        $canDelete = auth('cms')->user()?->can('home-banner-filters.delete') ?? false;
    @endphp

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">Site Filters</h5>
            <div class="d-flex gap-2">
                @if($canDelete)
                <div class="dropdown" id="bulkActions" style="display:none;">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Bulk Actions (<span id="selectedCount">0</span>)
                    </button>
                    <ul class="dropdown-menu">
                        <li><button class="dropdown-item" type="button" onclick="bulkAction('active')"><i class="fas fa-check-circle text-success me-2"></i>Mark Active</button></li>
                        <li><button class="dropdown-item" type="button" onclick="bulkAction('inactive')"><i class="fas fa-times-circle text-secondary me-2"></i>Mark Inactive</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger" type="button" onclick="bulkAction('delete')"><i class="fas fa-trash me-2"></i>Delete Selected</button></li>
                    </ul>
                </div>
                @endif
                @if($canCreate)
                    <a href="{{ route('cms.home-banner-filters.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Filter
                    </a>
                @endif
                @if($canEdit)
                    <form action="{{ route('cms.home-banner-filters.refresh') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-rotate me-2"></i>Refresh Values (All)
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table premium-table mb-0 w-100" id="homeBannerFiltersTable">
                    <thead>
                        <tr>
                            <th style="width:40px;">
                                @if($canDelete)<input type="checkbox" class="form-check-input" id="selectAll">@endif
                            </th>
                            <th style="width:40px;">#</th>
                            <th style="width:70px;">ID</th>
                            <th style="width:140px;">Filter</th>
                            <th>Label</th>
                            <th style="width:120px;">UI Type</th>
                            <th style="width:90px;" class="text-center">Values</th>
                            <th style="width:100px;" class="text-center">Order</th>
                            <th style="width:100px;" class="text-center">Status</th>
                            <th style="width:130px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function () {
            const table = $('#homeBannerFiltersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('cms.home-banner-filters.index') }}",
                columns: [
                    {data: 'select_all', name: 'select_all', orderable: false, searchable: false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'filter', name: 'key'},
                    {data: 'label', name: 'label'},
                    {data: 'ui_type', name: 'ui_type'},
                    {data: 'values_count', name: 'values_count', className: 'text-center'},
                    {data: 'order', name: 'sort_order', className: 'text-center'},
                    {data: 'status', name: 'status', className: 'text-center'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end'},
                ],
                order: [[7, 'asc']],
                drawCallback: function () {
                    updateBulkButton();
                }
            });

            $('#selectAll').on('change', function() {
                $('.row-checkbox').prop('checked', this.checked);
                updateBulkButton();
            });

            $(document).on('change', '.row-checkbox', function() {
                updateBulkButton();
            });

            function updateBulkButton() {
                const count = $('.row-checkbox:checked').length;
                $('#selectedCount').text(count);
                $('#bulkActions').toggle(count > 0);
                $('#selectAll').prop('checked', count > 0 && count === $('.row-checkbox').length && $('.row-checkbox').length > 0);
            }

            window.bulkAction = function(action) {
                if (action === 'delete' && !confirm('Delete selected filters?')) {
                    return;
                }
                const ids = $('.row-checkbox:checked').map(function() { return $(this).val(); }).get();
                $.post("{{ route('cms.home-banner-filters.bulk-action') }}", {
                    ids: ids,
                    action: action,
                    _token: "{{ csrf_token() }}"
                }, function() {
                    table.ajax.reload(null, false);
                    $('#selectAll').prop('checked', false);
                    updateBulkButton();
                });
            };

            $(document).on('change', '.toggle-status', function () {
                const id = $(this).data('id');
                $.post(
                    "{{ route('cms.home-banner-filters.toggle-definition-status', ['id' => '__ID__']) }}".replace('__ID__', id),
                    {_token: '{{ csrf_token() }}'},
                    function () {
                        table.ajax.reload(null, false);
                    }
                ).fail(function () {
                    table.ajax.reload(null, false);
                });
            });

            $(document).on('change', '.reorder-input', function() {
                $.post("{{ route('cms.home-banner-filters.reorder') }}", {
                    id: $(this).data('id'),
                    sort_order: $(this).val(),
                    _token: "{{ csrf_token() }}"
                }, function() {
                    table.ajax.reload(null, false);
                });
            });

            $(document).on('click', '.refresh-item', function() {
                const id = $(this).data('id');
                $.post("{{ route('cms.home-banner-filters.refresh') }}", {
                    definition_id: id,
                    _token: "{{ csrf_token() }}"
                }, function() {
                    table.ajax.reload(null, false);
                });
            });

            $(document).on('click', '.delete-item', function() {
                const id = $(this).data('id');
                if (!confirm('Delete this filter definition?')) return;
                $.ajax({
                    url: "{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/home-banner-filters/" + id,
                    type: 'DELETE',
                    data: {_token: "{{ csrf_token() }}"},
                    success: function() {
                        table.ajax.reload(null, false);
                    }
                });
            });
        });
    </script>
@endpush

