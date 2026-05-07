@php
    $showLanguageUi = config('cms-kit.common.modules.languages', true);
    $fallbackLocale = config('app.fallback_locale', 'en');
    $item = $place ?? null;
    $typeValue = old('type', $place->type ?? ($defaultType ?? ''));
    $placeTypeOptionsByLanguage = [];
    foreach ($languages as $lang) {
        $placeTypeOptionsByLanguage[$lang->code] = collect($placeTypes)->mapWithKeys(function ($labels, $value) use ($lang, $fallbackLocale) {
            if (!is_array($labels)) {
                $labels = [$fallbackLocale => (string) $labels];
            }

            $english = trim((string) ($labels[$fallbackLocale] ?? reset($labels) ?: ''));
            $localized = trim((string) ($labels[$lang->code] ?? $english));

            return [$value => [
                'label' => $localized !== '' ? $localized : $english,
                'english' => $english,
            ]];
        })->all();
    }
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

<input type="hidden" name="type" id="nearbyPlaceTypeMaster" value="{{ $typeValue }}">

<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label fw-bold">Latitude <span class="text-danger">*</span></label>
        <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $place->latitude ?? '') }}" required>
        @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Longitude <span class="text-danger">*</span></label>
        <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $place->longitude ?? '') }}" required>
        @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if($showLanguageUi)
    <div class="col-12">
        <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3" id="placeLanguageTabs" role="tablist">
            @foreach($languages as $lang)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium" data-bs-toggle="tab" data-bs-target="#place-panel-{{ $lang->code }}" type="button" role="tab">
                    <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
                </button>
            </li>
            @endforeach
        </ul>

        <div class="tab-content mb-4">
            @foreach($languages as $lang)
                @php $translation = data_get($item, "translations.{$lang->code}", []); @endphp
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="place-panel-{{ $lang->code }}" role="tabpanel">
                    <div class="card bg-light border-0 place-classification-card">
                        <div class="card-body p-4">
                            <div class="row g-4 place-classification-row">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                                    <div class="career-dual-dropdown place-classification-proxy @error('type') is-invalid @enderror" data-master-field="type" data-placeholder="Search type">
                                        <input type="text" class="form-control career-dual-dropdown-input" value="{{ data_get($placeTypeOptionsByLanguage, $lang->code . '.' . $typeValue . '.label', '') }}" placeholder="Search type" autocomplete="off" required>
                                        <div class="career-dual-dropdown-menu">
                                            @foreach($placeTypeOptionsByLanguage[$lang->code] ?? [] as $option => $meta)
                                                <button type="button" class="career-dual-dropdown-option {{ $typeValue === $option ? 'active' : '' }}" data-value="{{ $option }}" data-label="{{ $meta['label'] ?? '' }}">
                                                    <span class="career-dual-dropdown-primary">{{ $meta['label'] ?? '' }}</span>
                                                    <span class="career-dual-dropdown-secondary">{{ $meta['english'] ?? '' }}</span>
                                                </button>
                                            @endforeach
                                            <div class="career-dual-dropdown-empty d-none">No options found.</div>
                                        </div>
                                    </div>
                                    @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="translations[{{ $lang->code }}][name]" class="form-control @error("translations.{$lang->code}.name") is-invalid @enderror" value="{{ old("translations.{$lang->code}.name", $translation['name'] ?? ($lang->code === $fallbackLocale ? $item->name ?? '' : '')) }}" required>
                                    @error("translations.{$lang->code}.name")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Address</label>
                                    <input type="text" name="translations[{{ $lang->code }}][address]" class="form-control @error("translations.{$lang->code}.address") is-invalid @enderror" value="{{ old("translations.{$lang->code}.address", $translation['address'] ?? ($lang->code === $fallbackLocale ? $item->address ?? '' : '')) }}">
                                    @error("translations.{$lang->code}.address")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="col-md-3 d-flex align-items-center">
        <div class="form-check form-switch mt-4">
            <input class="form-check-input" type="checkbox" name="status" id="placeStatus" value="1" {{ old('status', $place->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="placeStatus">Active</label>
        </div>
    </div>
    <div class="col-12 border-top pt-4">
        <button type="submit" class="btn btn-primary px-4">{{ $buttonText }}</button>
        <a href="{{ route('cms.nearby-places.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </div>
</div>

@push('scripts')
<script>
    const nearbyPlaceMasterIds = { type: 'nearbyPlaceTypeMaster' };
    const nearbyPlaceDropdownStyles = `
        <style>
            .place-classification-card,
            .place-classification-card .card-body,
            .place-classification-row,
            .place-classification-row > div { overflow: visible !important; }
            .career-dual-dropdown { position: relative; }
            .career-dual-dropdown-input { cursor: pointer; background-image: linear-gradient(45deg, transparent 50%, #6b7280 50%), linear-gradient(135deg, #6b7280 50%, transparent 50%); background-position: calc(100% - 18px) calc(50% - 3px), calc(100% - 12px) calc(50% - 3px); background-size: 6px 6px, 6px 6px; background-repeat: no-repeat; padding-right: 2.5rem; }
            .career-dual-dropdown-menu { position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 1055; display: none; max-height: 260px; overflow-y: auto; background: #fff; border: 1px solid #dfe5ec; border-radius: 12px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12); }
            .career-dual-dropdown.open .career-dual-dropdown-menu { display: block; }
            .career-dual-dropdown-option { width: 100%; padding: 0.85rem 1rem; display: flex; justify-content: space-between; gap: 1rem; border: 0; background: transparent; text-align: left; }
            .career-dual-dropdown-option + .career-dual-dropdown-option { border-top: 1px solid #edf1f5; }
            .career-dual-dropdown-option:hover,.career-dual-dropdown-option.active { background: #f8fafc; }
            .career-dual-dropdown-primary { color: #1f2937; font-weight: 500; }
            .career-dual-dropdown-secondary { color: #6b7280; font-size: 0.875rem; white-space: nowrap; }
        </style>
    `;
    if (!document.getElementById('nearbyPlaceDropdownStyles')) {
        document.head.insertAdjacentHTML('beforeend', nearbyPlaceDropdownStyles.replace('<style>', '<style id="nearbyPlaceDropdownStyles">'));
    }
    function updateNearbyPlaceDropdownState(dropdown, value) {
        const input = dropdown.querySelector('.career-dual-dropdown-input');
        const selectedOption = dropdown.querySelector(`.career-dual-dropdown-option[data-value="${CSS.escape(value)}"]`);
        dropdown.querySelectorAll('.career-dual-dropdown-option').forEach((option) => option.classList.toggle('active', option.dataset.value === value));
        input.value = selectedOption ? (selectedOption.dataset.label || '') : '';
    }
    function syncNearbyPlaceField(field, value, source) {
        const masterInput = document.getElementById(nearbyPlaceMasterIds[field] || '');
        if (masterInput) masterInput.value = value;
        document.querySelectorAll(`.place-classification-proxy[data-master-field="${field}"]`).forEach((element) => {
            if (element !== source) updateNearbyPlaceDropdownState(element, value);
        });
    }
    document.querySelectorAll('.place-classification-proxy').forEach((element) => {
        const field = element.dataset.masterField;
        const input = element.querySelector('.career-dual-dropdown-input');
        input.readOnly = true;
        input.addEventListener('click', function() {
            const willOpen = !element.classList.contains('open');
            document.querySelectorAll('.career-dual-dropdown').forEach((dropdown) => dropdown.classList.remove('open'));
            if (willOpen) element.classList.add('open');
        });
        element.querySelectorAll('.career-dual-dropdown-option').forEach((option) => {
            option.addEventListener('click', function() {
                syncNearbyPlaceField(field, this.dataset.value, element);
                updateNearbyPlaceDropdownState(element, this.dataset.value);
                element.classList.remove('open');
            });
        });
    });
    const typeMasterInput = document.getElementById('nearbyPlaceTypeMaster');
    if (typeMasterInput) syncNearbyPlaceField('type', typeMasterInput.value, null);
    document.addEventListener('click', function(e) {
        document.querySelectorAll('.career-dual-dropdown').forEach((dropdown) => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
                const masterInput = document.getElementById('nearbyPlaceTypeMaster');
                updateNearbyPlaceDropdownState(dropdown, masterInput ? masterInput.value : '');
            }
        });
    });
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

    const firstNearbyPlaceInvalidField = document.querySelector('.tab-pane .is-invalid');
    if (firstNearbyPlaceInvalidField) {
        const invalidTabPane = firstNearbyPlaceInvalidField.closest('.tab-pane');
        const tabBtn = invalidTabPane ? document.querySelector(`[data-bs-target="#${invalidTabPane.id}"]`) : null;
        if (tabBtn && !tabBtn.classList.contains('active')) {
            bootstrap.Tab.getOrCreateInstance(tabBtn).show();
        }
    }
</script>
@endpush

