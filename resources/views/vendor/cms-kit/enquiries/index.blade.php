@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Enquiries</li>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form id="filterForm" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Page source</label>
                <select name="page_source" id="filterSource" class="form-select form-select-sm">
                    <option value="All">All</option>
                    @foreach($sources as $source)
                    <option value="{{ $source }}">{{ $source }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">From date</label>
                <input type="date" name="from_date" id="filterFromDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">To date</label>
                <input type="date" name="to_date" id="filterToDate" class="form-control form-control-sm">
            </div>
            <div class="col-auto">
                <button type="button" id="applyFilter" class="btn btn-sm btn-outline-primary">Filter</button>
            @if($hasData && $cmsUser->can('enquiries.export'))
                <button type="button" id="exportCsv" class="btn btn-sm btn-success">Export CSV</button>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Enquiries List</h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table premium-table mb-0 w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
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
    $(function() {
        const table = $('.premium-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('cms.enquiries.index') }}",
                data: function(d) {
                    d.page_source = $('#filterSource').val();
                    d.from_date = $('#filterFromDate').val();
                    d.to_date = $('#filterToDate').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name', render: function(data) {
                    return data ? `<span class="text-truncate d-inline-block" style="max-width: 150px;">${data}</span>` : '-';
                }},
                {data: 'email', name: 'email', render: function(data) {
                    return data ? `<span class="text-truncate d-inline-block" style="max-width: 180px;">${data}</span>` : '-';
                }},
                {data: 'phone', name: 'phone', render: function(data) {
                    return data ? `<span class="text-truncate d-inline-block" style="max-width: 140px;">${data}</span>` : '-';
                }},
                {data: 'country', name: 'country', render: function(data) {
                    return data ? `<span class="text-truncate d-inline-block" style="max-width: 120px;">${data}</span>` : '-';
                }},
                {data: 'subject', name: 'subject', orderable: false, searchable: false, render: function(data) {
                    return data ? `<span class="text-truncate d-inline-block" style="max-width: 150px;">${data}</span>` : '-';
                }},
                {data: 'date', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[6, 'desc']]
        });

        $('#applyFilter').on('click', function() {
            table.ajax.reload();
        });

        $('#exportCsv').on('click', function() {
            const params = $.param({
                page_source: $('#filterSource').val(),
                from_date: $('#filterFromDate').val(),
                to_date: $('#filterToDate').val()
            });
            window.location.href = "{{ route('cms.enquiries.export') }}?" + params;
        });

        // Delete
        $(document).on('click', '.delete-item', function() {
            if (confirm('Are you sure you want to delete this enquiry?')) {
                const id = $(this).data('id');
                $.ajax({
                    url: `{{ url(config('cms-kit.common.auth.prefix', 'admin')) }}/enquiries/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        table.ajax.reload(null, false);
                    }
                });
            }
        });

    });
</script>
@endpush
