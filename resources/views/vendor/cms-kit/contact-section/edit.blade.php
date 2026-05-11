@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
@endsection

@section('content')
@php
    $showLanguageUi = config('cms-kit.common.modules.languages', true);
    $translations = $section->translations ?? [];
@endphp

<div class="row">
    <div class="col-12">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">Contact Us Section Settings</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('cms.contact-section.update') }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="alert alert-light border-start border-primary border-4 py-2 mb-4 shadow-sm" style="font-size: 0.9rem;">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <strong>Note:</strong> These settings control the contact form section on the public Contact page.
                    </div>

                    @if($showLanguageUi)
                    <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3" id="contactLanguageTabs" role="tablist">
                        @foreach($languages as $lang)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium" id="contact-tab-{{ $lang->code }}" data-bs-toggle="tab" data-bs-target="#contact-panel-{{ $lang->code }}" type="button" role="tab">
                                <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <div class="tab-content mb-4">
                        @foreach($languages as $lang)
                        @php $trans = $translations[$lang->code] ?? []; @endphp
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="contact-panel-{{ $lang->code }}" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Title</label>
                                    <input type="text" name="translations[{{ $lang->code }}][title]" class="form-control @error("translations.{$lang->code}.title") is-invalid @enderror" value="{{ old("translations.{$lang->code}.title", $trans['title'] ?? '') }}" placeholder="Get in Touch">
                                    @error("translations.{$lang->code}.title")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text mt-1">Main heading for the contact form section.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Sub Title</label>
                                    <input type="text" name="translations[{{ $lang->code }}][sub_title]" class="form-control @error("translations.{$lang->code}.sub_title") is-invalid @enderror" value="{{ old("translations.{$lang->code}.sub_title", $trans['sub_title'] ?? '') }}" placeholder="Information Request">
                                    @error("translations.{$lang->code}.sub_title")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text mt-1">Heading displayed inside the contact form box.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">Content</label>
                                    <textarea name="translations[{{ $lang->code }}][content]" class="form-control @error("translations.{$lang->code}.content") is-invalid @enderror" rows="4" placeholder="Introductory text for the contact form">{{ old("translations.{$lang->code}.content", $trans['content'] ?? '') }}</textarea>
                                    @error("translations.{$lang->code}.content")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text mt-1">Introductory text shown above the form fields.</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="col-12 border-top pt-4">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-save me-2"></i>Update Section Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
