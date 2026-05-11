@php
    $showLanguageUi = config('cms-kit.common.modules.languages', true);
    $filterItem = $filterItem ?? null;
    $fallbackStatus = $filterItem?->status ?? true;
    $languages = $languages ?? [];
    $fallbackLocale = config('app.fallback_locale', 'en');

    $filterOptions = [
        'property_type' => 'Property type',
        'category' => 'Category',
        'location' => 'Location',
        'bedrooms' => 'Beds',
        'bathrooms' => 'Baths',
        'bed_and_baths' => 'Beds & Baths',
        'price' => 'Price range',
    ];

    $selectedFilter = old('filter', $filterItem->key ?? 'property_type');
    $selectedColumns = old('columns', $filterItem->source_column ?? '');

    $labelTranslations = $filterItem->translations ?? [];

    $defaultUiTypeByFilter = [
        'property_type' => 'dropdown',
        'category' => 'dropdown',
        'location' => 'dropdown',
        'price' => 'dropdown',
        'bedrooms' => 'dropdown',
        'bathrooms' => 'dropdown',
        'bed_and_baths' => 'dropdown',
    ];

    $defaultUiType = $filterItem?->ui_type ?? ($defaultUiTypeByFilter[$selectedFilter] ?? 'dropdown');
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
    <strong>Note:</strong> For dropdown filters using data from `properties`, click <em>Refresh</em> after you create/update this definition.
</div>

<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label fw-bold">Filter <span class="text-danger">*</span></label>
        <select name="filter" class="form-select @error('filter') is-invalid @enderror" required>
            <option value="">Select filter</option>
            @foreach($filterOptions as $value => $label)
                <option value="{{ $value }}" {{ (string) $selectedFilter === (string) $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('filter')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    @if($showLanguageUi)
        <div class="col-12">
            <ul class="nav nav-pills mb-3 bg-light p-2 rounded-3" id="homeBannerFilterLabelTabs" role="tablist">
                @foreach($languages as $lang)
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium"
                            data-bs-toggle="tab"
                            data-bs-target="#home-filter-label-{{ $lang->code }}"
                            type="button"
                            role="tab"
                        >
                            <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach($languages as $lang)
                    <div
                        class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                        id="home-filter-label-{{ $lang->code }}"
                        role="tabpanel"
                    >
                        <label class="form-label fw-bold">Label <span class="text-danger">*</span></label>

                        <input
                            type="text"
                            name="translations[{{ $lang->code }}][label]"
                            class="form-control @error("translations.{$lang->code}.label") is-invalid @enderror"
                            value="{{ old("translations.{$lang->code}.label", $labelTranslations[$lang->code]['label'] ?? '') }}"
                            required
                        >
                        @error("translations.{$lang->code}.label")
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="col-md-6">
            <label class="form-label fw-bold">Label <span class="text-danger">*</span></label>
            <input
                type="text"
                name="label"
                class="form-control @error('label') is-invalid @enderror"
                value="{{ old('label', $filterItem->label ?? '') }}"
                required
            >
            @error('label')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    @endif

    <div class="col-md-6">
        <label class="form-label fw-bold">UI Type <span class="text-danger">*</span></label>
        <select name="ui_type" class="form-control @error('ui_type') is-invalid @enderror" required>
            @php
                $uiTypes = ['dropdown' => 'dropdown', 'text' => 'text', 'integer' => 'integer'];
            @endphp
            @foreach($uiTypes as $value => $label)
                <option value="{{ $value }}" {{ old('ui_type', $defaultUiType) === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('ui_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold">Sort Order</label>
        <input
            type="number"
            name="sort_order"
            class="form-control @error('sort_order') is-invalid @enderror"
            value="{{ old('sort_order', $filterItem->sort_order ?? 1) }}"
            min="1"
        >
        @error('sort_order')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    @if((string) $selectedFilter !== 'price')
        <div id="filterColumnsWrap" class="col-md-12">
            <label class="form-label fw-bold">Property Columns <span class="text-danger">*</span></label>
            <select id="filterColumns" name="columns" class="form-select @error('columns') is-invalid @enderror" required data-selected="{{ $selectedColumns }}">
                <option value="">Select column(s)</option>
            </select>
            @error('columns')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <div class="form-text mt-1">This controls which distinct values will be cached in `home_banner_filter_values`.</div>
        </div>
    @endif

    <div class="col-12">
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="status" value="0">
            <input
                class="form-check-input"
                type="checkbox"
                name="status"
                value="1"
                id="filterStatus"
                {{ old('status', $fallbackStatus) ? 'checked' : '' }}
            >
            <label class="form-check-label fw-bold" for="filterStatus">Status</label>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterSelect = document.querySelector('select[name="filter"]');
        const columnsSelect = document.getElementById('filterColumns');
        const columnsWrap = document.getElementById('filterColumnsWrap');

        if (!filterSelect || !columnsSelect || !columnsWrap) {
            return;
        }

        const optionsByFilter = {
            property_type: [
                ['property_type', 'property_type'],
            ],
            category: [
                ['category', 'category'],
            ],
            location: [
                ['city', 'city'],
                ['community', 'community'],
                ['address', 'address'],
                ['title', 'title'],
                ['city,community', 'city + community'],
                ['city,community,address', 'city + community + address'],
                ['city,community,address,title', 'city + community + address + title'],
            ],
            bedrooms: [
                ['bedrooms', 'bedrooms'],
            ],
            bathrooms: [
                ['bathrooms', 'bathrooms'],
            ],
            bed_and_baths: [
                ['bedrooms,bathrooms', 'bedrooms + bathrooms'],
            ],
        };

        function renderColumnOptions() {
            const filter = filterSelect.value;
            const previous = columnsSelect.value || columnsSelect.dataset.selected || '';
            const rows = optionsByFilter[filter] || [];
            const needsColumns = filter !== 'price';

            columnsWrap.classList.toggle('d-none', !needsColumns);
            columnsSelect.required = needsColumns;
            columnsSelect.disabled = !needsColumns;

            if (!needsColumns) {
                columnsSelect.innerHTML = '';
                columnsSelect.append(new Option('Select column(s)', ''));
                columnsSelect.value = '';
                columnsSelect.dataset.selected = '';
                return;
            }

            columnsSelect.innerHTML = '';
            columnsSelect.append(new Option('Select column(s)', ''));

            rows.forEach(([value, label]) => {
                columnsSelect.append(new Option(label, value, false, value === previous));
            });

            if (!rows.some(([value]) => value === columnsSelect.value)) {
                columnsSelect.value = rows.length === 1 ? rows[0][0] : '';
            }

            columnsSelect.dataset.selected = columnsSelect.value;
        }

        filterSelect.addEventListener('change', function () {
            columnsSelect.dataset.selected = '';
            renderColumnOptions();
        });

        columnsSelect.addEventListener('change', function () {
            columnsSelect.dataset.selected = columnsSelect.value;
        });

        renderColumnOptions();
    });
</script>
@endpush
