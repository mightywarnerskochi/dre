@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Why Choose Us</li>
@endsection

@section('content')
@php $showLanguageUi = config('cms-kit.common.modules.languages', true); @endphp
<div class="row">
    <div class="col-12">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-primary">Why Choose Us Section Settings</h6>
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

                <form action="{{ route('cms.why-choose-us.update-section') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="alert alert-light border-start border-primary border-4 py-2 mb-4 shadow-sm" style="font-size: 0.9rem;">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <strong>Note:</strong> These settings control the Why Choose Us section content. Required fields are marked with <span class="text-danger">*</span>{{ $showLanguageUi ? ' across all language tabs' : '' }}.
                    </div>

                    @if($showLanguageUi)
                    <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3" id="whyChooseUsSectionTabs" role="tablist">
                        @foreach($languages as $lang)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium" data-bs-toggle="tab" data-bs-target="#why-section-{{ $lang->code }}" type="button" role="tab">
                                <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <div class="tab-content mb-4">
                        @foreach($languages as $lang)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="why-section-{{ $lang->code }}" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="translations[{{ $lang->code }}][title]" class="form-control @error("translations.{$lang->code}.title") is-invalid @enderror" value="{{ old("translations.{$lang->code}.title", $section->translations[$lang->code]['title'] ?? '') }}" required>
                                    @error("translations.{$lang->code}.title")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Subtitle</label>
                                    <input type="text" name="translations[{{ $lang->code }}][subtitle]" class="form-control @error("translations.{$lang->code}.subtitle") is-invalid @enderror" value="{{ old("translations.{$lang->code}.subtitle", $section->translations[$lang->code]['subtitle'] ?? '') }}">
                                    @error("translations.{$lang->code}.subtitle")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Description</label>
                                    <textarea name="translations[{{ $lang->code }}][description]" rows="4" class="form-control @error("translations.{$lang->code}.description") is-invalid @enderror">{{ old("translations.{$lang->code}.description", $section->translations[$lang->code]['description'] ?? '') }}</textarea>
                                    @error("translations.{$lang->code}.description")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Section Image</label>
                            <input type="file" name="section_image" class="form-control @error('section_image') is-invalid @enderror" accept="image/*">
                            @error('section_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($section->section_image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $section->section_image) }}" alt="" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Section Image Alt</label>
                            <input type="text" name="section_image_alt" class="form-control @error('section_image_alt') is-invalid @enderror" value="{{ old('section_image_alt', $section->section_image_alt ?? '') }}">
                            @error('section_image_alt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="status" id="whySectionStatus" {{ old('status', $section->status ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="whySectionStatus">Status</label>
                            </div>
                        </div>
                        <div class="col-12 border-top pt-4">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">Update Section Settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="mb-0">Why Choose Us Items</h5>
                    <small class="text-muted">Maximum {{ $maxItems }} items allowed.</small>
                </div>
                <div class="d-flex gap-2">
                    @if(auth('cms')->user()->can('why-choose-us.delete'))
                    <div class="dropdown" id="bulkActions" style="display:none;">
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
                    @if(auth('cms')->user()->can('why-choose-us.create') && $canCreate)
                    <a href="{{ route('cms.why-choose-us.create') }}" class="btn btn-primary btn-sm">Add Item</a>
                    @endif
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table premium-table mb-0 w-100" id="whyChooseUsTable">
                        <thead>
                            <tr>
                                <th style="width:40px;"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                                <th style="width:40px;">#</th>
                                <th>Title</th>
                                <th style="width:100px;">Order</th>
                                <th style="width:100px;" class="text-center">Status</th>
                                <th style="width:100px;" class="text-end">Actions</th>
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
$(function () {
    const table = $('#whyChooseUsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('cms.why-choose-us.index') }}",
        columns: [
            {data:'select_all', orderable:false, searchable:false},
            {data:'DT_RowIndex', orderable:false, searchable:false},
            {data:'title'},
            {data:'order'},
            {data:'status', orderable:false, searchable:false, className:'text-center'},
            {data:'action', orderable:false, searchable:false, className:'text-end'}
        ],
        drawCallback: updateBulkButton
    });

    $('#selectAll').on('change', function(){ $('.row-checkbox').prop('checked', this.checked); updateBulkButton(); });
    $(document).on('change', '.row-checkbox', updateBulkButton);

    function updateBulkButton() {
        const count = $('.row-checkbox:checked').length;
        $('#selectedCount').text(count);
        $('#bulkActions').toggle(count > 0);
    }

    window.bulkAction = function(action) {
        if (action === 'delete' && !confirm('Delete selected items?')) return;
        $.post("{{ route('cms.why-choose-us.bulk-action') }}", {
            ids: $('.row-checkbox:checked').map(function(){ return $(this).val(); }).get(),
            action: action,
            _token: "{{ csrf_token() }}"
        }, function() { table.ajax.reload(null, false); $('#selectAll').prop('checked', false); updateBulkButton(); });
    };

    $(document).on('click', '.delete-item', function() {
        if (!confirm('Delete this item?')) return;
        $.ajax({
            url: "{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/why-choose-us/" + $(this).data('id'),
            type: 'DELETE',
            data: {_token: "{{ csrf_token() }}"},
            success: function(){ table.ajax.reload(null, false); }
        });
    });

    $(document).on('change', '.toggle-status', function() {
        $.post("{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/why-choose-us/" + $(this).data('id') + "/toggle-status", {_token: "{{ csrf_token() }}"})
            .fail(function(){ table.ajax.reload(null, false); });
    });

    $(document).on('change', '.reorder-input', function() {
        $.post("{{ route('cms.why-choose-us.reorder') }}", {
            id: $(this).data('id'),
            order_index: $(this).val(),
            _token: "{{ csrf_token() }}"
        }, function(){ table.ajax.reload(null, false); });
    });

    document.addEventListener('invalid', function(e) {
        let invalidTabPane = e.target.closest('.tab-pane');
        if (invalidTabPane) {
            let tabId = invalidTabPane.id;
            let tabBtn = document.querySelector(`[data-bs-target="#${tabId}"]`);
            if (tabBtn && !tabBtn.classList.contains('active')) {
                bootstrap.Tab.getOrCreateInstance(tabBtn).show();
                setTimeout(() => { e.target.focus(); }, 150);
            }
        }
    }, true);
});
</script>
@endpush


