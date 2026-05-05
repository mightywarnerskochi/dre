@php
    $showLanguageUi = config('cms-kit.common.modules.languages', true);
    $fallbackLocale = config('app.fallback_locale', 'en');
    $item = $agent ?? null;
    $imageConfig = $imageConfig ?? config('cms-kit.images.agents.image', []);
@endphp

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="alert alert-light border-start border-primary border-4 py-2 mb-4 shadow-sm" style="font-size: 0.9rem;">
    <i class="fas fa-info-circle text-primary me-2"></i>
    <strong>Note:</strong> Please ensure all required fields <span class="text-danger">*</span> are filled{{ $showLanguageUi ? ' across all language tabs' : '' }}. If a required field is missing in another language tab, the form will switch to that tab automatically.
</div>

<div class="row g-4">
    @if($showLanguageUi)
    <div class="col-12">
        <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3" id="agentLanguageTabs" role="tablist">
            @foreach($languages as $lang)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium" data-bs-toggle="tab" data-bs-target="#agent-panel-{{ $lang->code }}" type="button" role="tab">
                    <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
                </button>
            </li>
            @endforeach
        </ul>

        <div class="tab-content mb-4">
            @foreach($languages as $lang)
                @php $translation = data_get($item, "translations.{$lang->code}", []); @endphp
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="agent-panel-{{ $lang->code }}" role="tabpanel">
                    <div class="card bg-light border-0">
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="translations[{{ $lang->code }}][name]" class="form-control @error("translations.{$lang->code}.name") is-invalid @enderror" value="{{ old("translations.{$lang->code}.name", $translation['name'] ?? ($lang->code === $fallbackLocale ? $item->name ?? '' : '')) }}" required>
                                    @error("translations.{$lang->code}.name")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Designation</label>
                                    <input type="text" name="translations[{{ $lang->code }}][designation]" class="form-control @error("translations.{$lang->code}.designation") is-invalid @enderror" value="{{ old("translations.{$lang->code}.designation", $translation['designation'] ?? ($lang->code === $fallbackLocale ? $item->designation ?? '' : '')) }}">
                                    @error("translations.{$lang->code}.designation")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Description</label>
                                    <textarea name="translations[{{ $lang->code }}][description]" rows="5" class="form-control @error("translations.{$lang->code}.description") is-invalid @enderror">{{ old("translations.{$lang->code}.description", $translation['description'] ?? ($lang->code === $fallbackLocale ? $item->description ?? '' : '')) }}</textarea>
                                    @error("translations.{$lang->code}.description")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    <div class="col-md-6">
        <label class="form-label fw-bold">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $agent->email ?? '') }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Phone</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $agent->phone ?? '') }}">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">WhatsApp Number</label>
        <input type="text" name="whatsapp_number" class="form-control @error('whatsapp_number') is-invalid @enderror" value="{{ old('whatsapp_number', $agent->whatsapp_number ?? '') }}" placeholder="e.g. +971501234567">
        @error('whatsapp_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Experience</label>
        <input type="text" name="experience" class="form-control @error('experience') is-invalid @enderror" value="{{ old('experience', $agent->experience ?? '') }}" placeholder="e.g. 8 Years">
        @error('experience')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Languages</label>
        <input type="text" name="languages" class="form-control @error('languages') is-invalid @enderror" value="{{ old('languages', $agent->languages ?? '') }}" placeholder="e.g. English, Arabic">
        @error('languages')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Image</label>
        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted d-block mt-2">
            Recommended: {{ $imageConfig['width'] ?? 600 }}x{{ $imageConfig['height'] ?? 600 }}px.
            Max size: {{ $imageConfig['max_size'] ?? 2048 }} KB.
        </small>
        @if(!empty($agent->image))
            <div class="mt-2">
                <img src="{{ media_url($agent->image) }}" alt="{{ $agent->image_alt }}" class="img-thumbnail" style="max-height: 90px;">
            </div>
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Image Alt Text</label>
        <input type="text" name="image_alt" class="form-control @error('image_alt') is-invalid @enderror" value="{{ old('image_alt', $agent->image_alt ?? '') }}" placeholder="Describe the agent image">
        @error('image_alt')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 d-flex align-items-center">
        <div class="form-check form-switch mt-4">
            <input class="form-check-input" type="checkbox" name="status" id="agentStatus" value="1" {{ old('status', $agent->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="agentStatus">Active</label>
        </div>
    </div>
    <div class="col-12 border-top pt-4">
        <button type="submit" class="btn btn-primary px-4">{{ $buttonText }}</button>
        <a href="{{ route('cms.agents.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('invalid', function(e) {
    const invalidTabPane = e.target.closest('.tab-pane');
    if (invalidTabPane) {
        const tabBtn = document.querySelector(`[data-bs-target="#${invalidTabPane.id}"]`);
        if (tabBtn && !tabBtn.classList.contains('active')) {
            bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            setTimeout(() => { e.target.focus(); }, 150);
        }
    }
}, true);

const firstAgentInvalidField = document.querySelector('.tab-pane .is-invalid');
if (firstAgentInvalidField) {
    const invalidTabPane = firstAgentInvalidField.closest('.tab-pane');
    const tabBtn = invalidTabPane ? document.querySelector(`[data-bs-target="#${invalidTabPane.id}"]`) : null;
    if (tabBtn && !tabBtn.classList.contains('active')) {
        bootstrap.Tab.getOrCreateInstance(tabBtn).show();
    }
}
</script>
@endpush

<!--  -->
