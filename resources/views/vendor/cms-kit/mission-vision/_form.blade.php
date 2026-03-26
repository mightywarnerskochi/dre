@php $showLanguageUi = config('cms-kit.common.modules.languages', true); @endphp

@if($showLanguageUi)
<ul class="nav nav-pills mb-4 bg-light p-2 rounded-3" id="missionVisionTabs" role="tablist">
    @foreach($languages as $lang)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium" data-bs-toggle="tab" data-bs-target="#mission-{{ $lang->code }}" type="button" role="tab">
            <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
        </button>
    </li>
    @endforeach
</ul>
@endif

<div class="tab-content mb-4">
    @foreach($languages as $lang)
    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="mission-{{ $lang->code }}" role="tabpanel">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                <input type="text" name="translations[{{ $lang->code }}][title]" class="form-control @error("translations.{$lang->code}.title") is-invalid @enderror" value="{{ old("translations.{$lang->code}.title", $item->translations[$lang->code]['title'] ?? '') }}" required>
                @error("translations.{$lang->code}.title")<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
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
    <div class="col-md-6">
        <label class="form-label fw-bold">Image</label>
        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if(!empty($item->image))
        <div class="mt-2">
            <img src="{{ asset('storage/' . $item->image) }}" alt="" class="img-thumbnail" style="max-height: 100px;">
        </div>
        @endif
        @if(!empty($imageConfig))
        <div class="form-text">Recommended: {{ $imageConfig['width'] ?? '-' }} x {{ $imageConfig['height'] ?? '-' }} px</div>
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Image Alt</label>
        <input type="text" name="image_alt" class="form-control @error('image_alt') is-invalid @enderror" value="{{ old('image_alt', $item->image_alt ?? ($item->translations[config('app.fallback_locale')]['image_alt'] ?? '')) }}">
        @error('image_alt')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold">Order</label>
        <input type="number" min="1" name="order_index" class="form-control @error('order_index') is-invalid @enderror" value="{{ old('order_index', $item->order_index ?? $nextOrder ?? 1) }}">
        @error('order_index')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 d-flex align-items-center">
        <div class="form-check form-switch mt-4">
            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" {{ old('status', $item->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="statusSwitch">Status</label>
        </div>
    </div>
    <div class="col-12 border-top pt-4">
        <button type="submit" class="btn btn-primary px-4 shadow-sm">{{ $buttonText }}</button>
        <a href="{{ route('cms.mission-vision.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
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
