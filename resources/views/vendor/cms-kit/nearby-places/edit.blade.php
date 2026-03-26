@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.nearby-places.index') }}">Nearby Places</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Edit Nearby Place</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('cms.nearby-places.update', $place->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('cms-kit::nearby-places._form', ['buttonText' => 'Update Place'])
        </form>
    </div>
</div>
@endsection
