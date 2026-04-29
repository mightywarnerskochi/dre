@php
    $showLanguageUi = config('cms-kit.common.modules.languages', true);
    $fallbackLocale = config('app.fallback_locale', 'en');
    $neighborhoodItem = $neighborhood ?? null;

    $orderValue = old('order_index', $neighborhoodItem->order_index ?? ($nextOrder ?? 1));
    $latitudeValue = old('latitude', $neighborhoodItem->latitude ?? '');
    $longitudeValue = old('longitude', $neighborhoodItem->longitude ?? '');
    $statusValue = old('status', $neighborhoodItem->status ?? true);
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
    <strong>Note:</strong> Please ensure all required fields <span class="text-danger">*</span> are filled{{ $showLanguageUi ? ' across all language tabs' : '' }}.
</div>

<div class="row g-4">
    
    @if($showLanguageUi)
        <div class="col-12">
            <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3" id="neighborhoodLanguageTabs" role="tablist">
                @foreach($languages as $lang)
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium"
                            data-bs-toggle="tab"
                            data-bs-target="#neighborhood-panel-{{ $lang->code }}"
                            type="button"
                            role="tab"
                        >
                            <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content mb-4">
                @foreach($languages as $lang)
                    @php
                        $translation = data_get($neighborhoodItem, "translations.{$lang->code}", []);
                        $oldName = old("translations.{$lang->code}.name");
                    @endphp
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="neighborhood-panel-{{ $lang->code }}" role="tabpanel">
                        <div class="card bg-light border-0">
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            name="translations[{{ $lang->code }}][name]"
                                            class="form-control @error("translations.{$lang->code}.name") is-invalid @enderror"
                                            value="{{ $oldName !== null ? $oldName : ($translation['name'] ?? '') }}"
                                            required
                                        >
                                        @error("translations.{$lang->code}.name")
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="col-12">
            <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
            <input type="text" name="translations[{{ $fallbackLocale }}][name]" class="form-control" required>
        </div>
    @endif

    <div class="col-md-6">
        <label class="form-label fw-bold">Latitude <span class="text-danger">*</span></label>
        <input
            type="text"
            name="latitude"
            class="form-control @error('latitude') is-invalid @enderror"
            value="{{ $latitudeValue }}"
            required
        >
        @error('latitude')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold">Longitude <span class="text-danger">*</span></label>
        <input
            type="text"
            name="longitude"
            class="form-control @error('longitude') is-invalid @enderror"
            value="{{ $longitudeValue }}"
            required
        >
        @error('longitude')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Order <span class="text-danger">*</span></label>
        <input
            type="number"
            name="order_index"
            class="form-control @error('order_index') is-invalid @enderror"
            value="{{ $orderValue }}"
            min="1"
            required
        >
        @error('order_index')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 d-flex align-items-end pb-2">
        <div class="form-check form-switch mb-0">
            <input
                class="form-check-input"
                type="checkbox"
                name="status"
                id="neighborhoodStatus"
                value="1"
                {{ $statusValue ? 'checked' : '' }}
            >
            <label class="form-check-label fw-bold" for="neighborhoodStatus">Active</label>
        </div>
    </div>

    <div class="col-12 border-top pt-4">
        <button type="submit" class="btn btn-primary px-4">{{ $buttonText }}</button>
        <a href="{{ route('cms.neighborhoods.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('invalid', function(e) {
            const invalidTabPane = e.target.closest('.tab-pane');
            if (!invalidTabPane) return;

            const tabId = invalidTabPane.id;
            const tabBtn = document.querySelector(`[data-bs-target="#${tabId}"]`);

            if (tabBtn && !tabBtn.classList.contains('active')) {
                bootstrap.Tab.getOrCreateInstance(tabBtn).show();
                setTimeout(() => { e.target.focus(); }, 150);
            }
        }, true);
    </script>
@endpush

