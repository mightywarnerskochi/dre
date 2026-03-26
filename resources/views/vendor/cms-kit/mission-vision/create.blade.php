@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.mission-vision.index') }}">Mission & Vision</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3"><h5 class="mb-0">Add Mission & Vision Item</h5></div>
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('cms.mission-vision.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="alert alert-light border-start border-primary border-4 py-2 mb-4 shadow-sm" style="font-size: 0.9rem;">
                <i class="fas fa-info-circle text-primary me-2"></i>
                <strong>Note:</strong> Please ensure all required fields <span class="text-danger">*</span> are filled{{ config('cms-kit.common.modules.languages', true) ? ' across all language tabs' : '' }}.
            </div>
            @php($item = new \App\Models\CmsKit\MissionVision())
            @php($buttonText = 'Save Item')
            @include('cms-kit::mission-vision._form')
        </form>
    </div>
</div>
@endsection
