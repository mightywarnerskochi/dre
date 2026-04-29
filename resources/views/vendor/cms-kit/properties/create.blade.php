@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.properties.index') }}">Properties</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Add Property</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('cms.properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('cms-kit::properties-form', ['submitLabel' => 'Create Property'])
        </form>
    </div>
</div>
@endsection

