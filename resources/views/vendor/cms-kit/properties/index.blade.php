@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Properties</li>
@endsection

@section('content')
@php $showLanguageUi = config('cms-kit.common.modules.languages', true); @endphp
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-primary">Home Page Rental Section Settings</h6>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('cms.properties.update-section') }}" method="POST">
            @csrf

            <div class="alert alert-light border-start border-primary border-4 py-2 mb-4 shadow-sm" style="font-size: 0.9rem;">
                <i class="fas fa-info-circle text-primary me-2"></i>
                <strong>Note:</strong> These settings are used for the Home page rental properties section.
            </div>

            @if($showLanguageUi)
                <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3" id="homePropertiesSectionTabs" role="tablist">
                    @foreach($languages as $lang)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium" data-bs-toggle="tab" data-bs-target="#home-prop-section-{{ $lang->code }}" type="button" role="tab">
                                <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="tab-content mb-4">
                @foreach($languages as $lang)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="home-prop-section-{{ $lang->code }}" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    name="translations[{{ $lang->code }}][title]"
                                    class="form-control @error("translations.{$lang->code}.title") is-invalid @enderror"
                                    value="{{ old("translations.{$lang->code}.title", $homeSection->translations[$lang->code]['title'] ?? '') }}"
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
                                >{{ old("translations.{$lang->code}.description", $homeSection->translations[$lang->code]['description'] ?? '') }}</textarea>
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
                        <input type="hidden" name="display_home" value="0">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="display_home"
                            id="displayHomeToggle"
                            value="1"
                            {{ old('display_home', $homeSection->status ?? true) ? 'checked' : '' }}
                        >
                        <label class="form-check-label fw-bold" for="displayHomeToggle">Display on Home</label>
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

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Properties</h5>
        <div class="d-flex gap-2">
            @if(auth('cms')->user()->can('property.delete'))
            <div class="dropdown" id="bulkActions" style="display: none;">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Bulk Actions (<span id="selectedCount">0</span>)
                </button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item" type="button" onclick="bulkAction('active')">Mark Active</button></li>
                    <li><button class="dropdown-item" type="button" onclick="bulkAction('inactive')">Mark Inactive</button></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><button class="dropdown-item" type="button" onclick="bulkAction('delete')">Delete Selected</button></li>
                </ul>
            </div>
            @endif
            @if(auth('cms')->user()->can('property.create'))
                <a href="{{ route('cms.properties.create') }}" class="btn btn-primary btn-sm">Add Property</a>
            @endif
        </div>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table premium-table mb-0 w-100" id="propertiesTable">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                        <th style="width: 40px;">#</th>
                        <th>Listing</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th style="width: 100px;">Order</th>
                        <th style="width: 100px;" class="text-center">Status</th>
                        <th style="width: 100px;" class="text-end">Actions</th>
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
    const table = $('#propertiesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('cms.properties.index') }}",
        columns: [
            {data:'select_all', orderable:false, searchable:false},
            {data:'DT_RowIndex', orderable:false, searchable:false},
            {data:'property', name:'title', orderable:false, searchable:true},
            {data:'type', name:'property_type', orderable:false, searchable:false},
            {data:'price_label', name:'price', orderable:false, searchable:true},
            {data:'order_input', orderable:false, searchable:false},
            {data:'status', orderable:false, searchable:false, className:'text-center'},
            {data:'action', orderable:false, searchable:false, className:'text-end'}
        ]
    });

    $('#selectAll').on('change', function(){ $('.row-checkbox').prop('checked', this.checked); updateBulkButton(); });
    $(document).on('change', '.row-checkbox', updateBulkButton);

    function updateBulkButton() {
        const count = $('.row-checkbox:checked').length;
        $('#selectedCount').text(count);
        $('#bulkActions').toggle(count > 0);
    }

    window.bulkAction = function(action) {
        if (action === 'delete' && !confirm('Delete selected properties?')) return;
        $.post("{{ route('cms.properties.bulk-action') }}", {
            ids: $('.row-checkbox:checked').map(function(){ return $(this).val(); }).get(),
            action: action,
            _token: "{{ csrf_token() }}"
        }, function() { table.ajax.reload(null, false); $('#selectAll').prop('checked', false); updateBulkButton(); });
    };

    $(document).on('click', '.delete-item', function() {
        if (!confirm('Delete this property?')) return;
        $.ajax({
            url: "{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/properties/" + $(this).data('id'),
            type: 'DELETE',
            data: {_token: "{{ csrf_token() }}"},
            success: function(){ table.ajax.reload(null, false); }
        });
    });

    $(document).on('change', '.toggle-status', function() {
        $.post("{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/properties/" + $(this).data('id') + "/toggle-status", {_token: "{{ csrf_token() }}"})
            .fail(function(){ table.ajax.reload(null, false); });
    });

    $(document).on('change', '.reorder-input', function() {
        $.post("{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/properties/" + $(this).data('id') + "/reorder", {
            order: $(this).val(),
            _token: "{{ csrf_token() }}"
        }, function(){ table.ajax.reload(null, false); });
    });
});
</script>
@endpush
