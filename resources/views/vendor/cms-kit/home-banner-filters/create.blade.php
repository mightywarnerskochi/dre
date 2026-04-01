@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.home-banner-filters.index') }}">Home Banner Filters</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Add Home Banner Filter</h5>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('cms.home-banner-filters.store') }}" method="POST">
            @csrf
            @include('cms-kit::home-banner-filters._form', [
                'filterItem' => null,
            ])

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-save me-2"></i>Create Filter
                </button>
                <a href="{{ route('cms.home-banner-filters.index') }}" class="btn btn-outline-secondary ms-2">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

