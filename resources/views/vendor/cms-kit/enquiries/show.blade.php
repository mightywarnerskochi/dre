@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route(($routePrefix ?? 'cms.enquiries') . '.index') }}">{{ $pageHeading ?? 'Enquiries' }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Enquiry #{{ $enquiry->id }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">{{ $pageHeading ?? 'Enquiry' }} Details</h5>
        <a href="{{ route(($routePrefix ?? 'cms.enquiries') . '.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            @foreach($details as $label => $value)
                @if(filled($value))
                    <div class="col-md-6">
                        <p class="mb-0">
                            <strong>{{ $label }}:</strong><br>
                            @if($label === 'Page URL')
                                <a href="{{ $value }}" target="_blank" rel="noopener noreferrer">{{ $value }}</a>
                            @elseif($label === 'Message')
                                {!! nl2br(e($value)) !!}
                            @else
                                {{ $value }}
                            @endif
                        </p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
