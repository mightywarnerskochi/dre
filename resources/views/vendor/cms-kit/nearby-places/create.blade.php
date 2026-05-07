@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.nearby-places.index') }}">Nearby Places</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Add Nearby Place</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('cms.nearby-places.store') }}" method="POST">
            @csrf
            @include('cms-kit::nearby-places._form', ['buttonText' => 'Create Place'])
        </form>
    </div>
</div>
@endsection
