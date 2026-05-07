@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.home-banner-filters.index') }}">Site Filters</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Edit Home Banner Filter</h5>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('cms.home-banner-filters.update', $definition->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('cms-kit::home-banner-filters._form', [
                'filterItem' => $definition,
            ])

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-save me-2"></i>Update Filter
                </button>
                <a href="{{ route('cms.home-banner-filters.index') }}" class="btn btn-outline-secondary ms-2">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

