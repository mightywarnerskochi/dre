@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.neighborhoods.index') }}">Neighborhoods</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Add Neighborhood</h5>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('cms.neighborhoods.store') }}" method="POST">
            @csrf
            @include('cms-kit::neighborhoods._form', [
                'buttonText' => 'Create Neighborhood',
                'nextOrder' => $nextOrder,
                'neighborhood' => null,
            ])
        </form>
    </div>
</div>
@endsection

