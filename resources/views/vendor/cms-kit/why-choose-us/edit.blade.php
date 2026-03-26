@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.why-choose-us.index') }}">Why Choose Us</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3"><h5 class="mb-0">Edit Why Choose Us Item</h5></div>
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
        <form action="{{ route('cms.why-choose-us.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="alert alert-light border-start border-primary border-4 py-2 mb-4 shadow-sm" style="font-size: 0.9rem;">
                <i class="fas fa-info-circle text-primary me-2"></i>
                <strong>Note:</strong> Please ensure all required fields <span class="text-danger">*</span> are filled{{ config('cms-kit.common.modules.languages', true) ? ' across all language tabs' : '' }}.
            </div>
            @php($buttonText = 'Update Item')
            @include('cms-kit::why-choose-us._form')
        </form>
    </div>
</div>
@endsection
