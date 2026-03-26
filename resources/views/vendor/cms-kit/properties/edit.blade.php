@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.properties.index') }}">Properties</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Edit Property</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('cms.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('cms-kit::properties-form', ['submitLabel' => 'Update Property'])
        </form>
    </div>
</div>
@endsection

