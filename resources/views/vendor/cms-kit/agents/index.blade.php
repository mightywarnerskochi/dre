@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Agents</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Agents</h5>
        <div class="d-flex gap-2">
            @if(auth('cms')->user()->can('agents.delete'))
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
            @if(auth('cms')->user()->can('agents.create'))
                <a href="{{ route('cms.agents.create') }}" class="btn btn-primary btn-sm">Add Agent</a>
            @endif
        </div>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table premium-table mb-0 w-100" id="agentsTable">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                        <th style="width: 40px;">#</th>
                        <th style="width: 80px;">Image</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Phone</th>
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
    const table = $('#agentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('cms.agents.index') }}",
        columns: [
            {data:'select_all', orderable:false, searchable:false},
            {data:'DT_RowIndex', orderable:false, searchable:false},
            {data:'image', orderable:false, searchable:false},
            {data:'name', name:'name'},
            {data:'designation', name:'designation'},
            {data:'phone', name:'phone'},
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
        if (action === 'delete' && !confirm('Delete selected agents?')) return;
        $.post("{{ route('cms.agents.bulk-action') }}", {
            ids: $('.row-checkbox:checked').map(function(){ return $(this).val(); }).get(),
            action: action,
            _token: "{{ csrf_token() }}"
        }, function() { table.ajax.reload(null, false); $('#selectAll').prop('checked', false); updateBulkButton(); });
    };

    $(document).on('click', '.delete-item', function() {
        if (!confirm('Delete this agent?')) return;
        $.ajax({
            url: "{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/agents/" + $(this).data('id'),
            type: 'DELETE',
            data: {_token: "{{ csrf_token() }}"},
            success: function(){ table.ajax.reload(null, false); }
        });
    });

    $(document).on('change', '.toggle-status', function() {
        $.post("{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/agents/" + $(this).data('id') + "/toggle-status", {_token: "{{ csrf_token() }}"})
            .fail(function(){ table.ajax.reload(null, false); });
    });
});
</script>
@endpush
