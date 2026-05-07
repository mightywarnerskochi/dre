@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item active">About</li>
@endsection

@section('content')
@php $showLanguageUi = config('cms-kit.common.modules.languages', true); @endphp
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Edit About Section</h5>
    </div>
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

        <form action="{{ route('cms.about.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="alert alert-light border-start border-primary border-4 py-2 mb-4 shadow-sm" style="font-size: 0.9rem;">
                <i class="fas fa-info-circle text-primary me-2"></i>
                <strong>Note:</strong> These settings control the About page content. Required fields are marked with <span class="text-danger">*</span>{{ $showLanguageUi ? ' across all language tabs' : '' }}.
            </div>

            @if($showLanguageUi)
            <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3" id="aboutTabs" role="tablist">
                @foreach($languages as $lang)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium" id="tab-{{ $lang->code }}" data-bs-toggle="tab" data-bs-target="#about-{{ $lang->code }}" type="button" role="tab">
                        <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
                    </button>
                </li>
                @endforeach
            </ul>
            @endif

            <div class="tab-content mb-4" id="aboutTabContent">
                @foreach($languages as $lang)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="about-{{ $lang->code }}" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="translations[{{ $lang->code }}][title]" class="form-control @error("translations.{$lang->code}.title") is-invalid @enderror" value="{{ old("translations.{$lang->code}.title", $about->translations[$lang->code]['title'] ?? '') }}" required>
                            @error("translations.{$lang->code}.title")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Subtitle</label>
                            <input type="text" name="translations[{{ $lang->code }}][subtitle]" class="form-control @error("translations.{$lang->code}.subtitle") is-invalid @enderror" value="{{ old("translations.{$lang->code}.subtitle", $about->translations[$lang->code]['subtitle'] ?? '') }}">
                            @error("translations.{$lang->code}.subtitle")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="translations[{{ $lang->code }}][description]" rows="6" class="form-control tinymce-editor @error("translations.{$lang->code}.description") is-invalid @enderror">{{ old("translations.{$lang->code}.description", $about->translations[$lang->code]['description'] ?? ($about->translations[$lang->code]['short_description'] ?? '')) }}</textarea>
                            @error("translations.{$lang->code}.description")
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="row g-4 mt-1">
                @foreach([1,2,3,4] as $index)
                <div class="col-md-6">
                    <label class="form-label fw-bold">Image {{ $index }}</label>
                    <input type="file" name="image_{{ $index }}" class="form-control @error("image_{$index}") is-invalid @enderror" accept="image/*">
                    @error("image_{$index}")
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($about->{"image_{$index}"})
                    <div class="mt-2">
                        <img src="{{ media_url($about->{"image_{$index}"}) }}" alt="" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remove_image_{{ $index }}" id="removeImage{{ $index }}" value="1" {{ old("remove_image_{$index}") ? 'checked' : '' }}>
                        <label class="form-check-label" for="removeImage{{ $index }}">Remove current image</label>
                    </div>
                    @endif
                    @if(!empty($imagesConfig["image_{$index}"]))
                    <div class="form-text">Recommended: {{ $imagesConfig["image_{$index}"]['width'] ?? '-' }} x {{ $imagesConfig["image_{$index}"]['height'] ?? '-' }} px</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Image {{ $index }} Alt</label>
                    <input type="text" name="image_{{ $index }}_alt" class="form-control @error("image_{$index}_alt") is-invalid @enderror" value="{{ old("image_{$index}_alt", $about->{"image_{$index}_alt"} ?? ($about->translations[config('app.fallback_locale')]["image_{$index}_alt"] ?? '')) }}">
                    @error("image_{$index}_alt")
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @endforeach

                <div class="col-md-6">
                    <label class="form-label fw-bold">Home About Image</label>
                    <input type="file" name="home_image" class="form-control @error('home_image') is-invalid @enderror" accept="image/*">
                    @error('home_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($about->home_image)
                    <div class="mt-2">
                        <img src="{{ media_url($about->home_image) }}" alt="" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remove_home_image" id="removeHomeImage" value="1" {{ old('remove_home_image') ? 'checked' : '' }}>
                        <label class="form-check-label" for="removeHomeImage">Remove current image</label>
                    </div>
                    @endif
                    @if(!empty($imagesConfig['home_image']))
                    <div class="form-text">Recommended: {{ $imagesConfig['home_image']['width'] ?? '-' }} x {{ $imagesConfig['home_image']['height'] ?? '-' }} px</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Home About Image Alt</label>
                    <input type="text" name="home_image_alt" class="form-control @error('home_image_alt') is-invalid @enderror" value="{{ old('home_image_alt', $about->home_image_alt ?? '') }}">
                    @error('home_image_alt')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="display_home" value="0">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="display_home"
                            id="aboutDisplayHome"
                            value="1"
                            {{ old('display_home', data_get($about->translations, '_meta.display_home', true)) ? 'checked' : '' }}
                        >
                        <label class="form-check-label fw-bold" for="aboutDisplayHome">Display on Home</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="aboutStatus" {{ old('status', $about->status) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="aboutStatus">Status</label>
                    </div>
                </div>
                <div class="col-12 border-top pt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-save me-2"></i>Update About Section
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/site-manager/js/tinymce/tinymce.min.js') }}"></script>
<script>
tinymce.init({
    selector: '.tinymce-editor',
    height: 300,
    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
});

document.addEventListener('invalid', function(e) {
    let invalidTabPane = e.target.closest('.tab-pane');
    if (invalidTabPane) {
        let tabId = invalidTabPane.id;
        let tabBtn = document.querySelector(`[data-bs-target="#${tabId}"]`);
        if (tabBtn && !tabBtn.classList.contains('active')) {
            bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            setTimeout(() => { e.target.focus(); }, 150);
        }
    }
}, true);
</script>
@endpush

