@php $showLanguageUi = config('cms-kit.common.modules.languages', true); @endphp

@if($showLanguageUi)
<ul class="nav nav-pills mb-4 bg-light p-2 rounded-3" id="successfulJourneyTabs" role="tablist">
    @foreach($languages as $lang)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium" data-bs-toggle="tab" data-bs-target="#journey-{{ $lang->code }}" type="button" role="tab">
            <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
        </button>
    </li>
    @endforeach
</ul>
@endif

<div class="tab-content mb-4">
    @foreach($languages as $lang)
    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="journey-{{ $lang->code }}" role="tabpanel">
        <div class="row g-4">
            <div class="col-12">
                <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                <textarea name="translations[{{ $lang->code }}][description]" rows="5" class="form-control @error("translations.{$lang->code}.description") is-invalid @enderror" required>{{ old("translations.{$lang->code}.description", $item->translations[$lang->code]['description'] ?? '') }}</textarea>
                @error("translations.{$lang->code}.description")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-md-4">
        <label class="form-label fw-bold">Year <span class="text-danger">*</span></label>
        <input type="text" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $item->year ?? '') }}" required>
        @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold">Order</label>
        <input type="number" min="1" name="order_index" class="form-control @error('order_index') is-invalid @enderror" value="{{ old('order_index', $item->order_index ?? $nextOrder ?? 1) }}">
        @error('order_index')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 d-flex align-items-center">
        <div class="form-check form-switch mt-4">
            <input class="form-check-input" type="checkbox" name="status" id="journeyStatus" {{ old('status', $item->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="journeyStatus">Status</label>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Image 1</label>
        <input type="file" name="image_1" class="form-control @error('image_1') is-invalid @enderror" accept="image/*">
        <div class="form-text small text-muted">
            Recommended: {{ $image1Config['width'] ?? '-' }}x{{ $image1Config['height'] ?? '-' }}px 
            (Max: {{ ($image1Config['max_size'] ?? 1024) / 1024 }}MB)
        </div>
        @error('image_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if(!empty($item->image_1))
        <div class="mt-2"><img src="{{ media_url($item->image_1) }}" alt="" class="img-thumbnail" style="max-height: 100px;"></div>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="remove_image_1" id="removeJourneyImage1" value="1" {{ old('remove_image_1') ? 'checked' : '' }}>
            <label class="form-check-label" for="removeJourneyImage1">Remove current image</label>
        </div>
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Image 1 Alt</label>
        <input type="text" name="image_1_alt" class="form-control @error('image_1_alt') is-invalid @enderror" value="{{ old('image_1_alt', $item->image_1_alt ?? ($item->translations[config('app.fallback_locale')]['image_1_alt'] ?? '')) }}">
        @error('image_1_alt')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Image 2</label>
        <input type="file" name="image_2" class="form-control @error('image_2') is-invalid @enderror" accept="image/*">
        <div class="form-text small text-muted">
            Recommended: {{ $image2Config['width'] ?? '-' }}x{{ $image2Config['height'] ?? '-' }}px 
            (Max: {{ ($image2Config['max_size'] ?? 1024) / 1024 }}MB)
        </div>
        @error('image_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if(!empty($item->image_2))
        <div class="mt-2"><img src="{{ media_url($item->image_2) }}" alt="" class="img-thumbnail" style="max-height: 100px;"></div>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="remove_image_2" id="removeJourneyImage2" value="1" {{ old('remove_image_2') ? 'checked' : '' }}>
            <label class="form-check-label" for="removeJourneyImage2">Remove current image</label>
        </div>
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Image 2 Alt</label>
        <input type="text" name="image_2_alt" class="form-control @error('image_2_alt') is-invalid @enderror" value="{{ old('image_2_alt', $item->image_2_alt ?? ($item->translations[config('app.fallback_locale')]['image_2_alt'] ?? '')) }}">
        @error('image_2_alt')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 border-top pt-4">
        <button type="submit" class="btn btn-primary px-4 shadow-sm">{{ $buttonText }}</button>
        <a href="{{ route('cms.successful-journeys.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </div>
</div>

@push('scripts')
<script>
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

