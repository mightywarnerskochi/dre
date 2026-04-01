@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.neighborhoods.index') }}">Neighborhoods</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Edit Neighborhood</h5>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('cms.neighborhoods.update', $neighborhood->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('cms-kit::neighborhoods._form', [
                'buttonText' => 'Update Neighborhood',
                'nextOrder' => null,
                'neighborhood' => $neighborhood,
            ])
        </form>
    </div>
</div>
@endsection

