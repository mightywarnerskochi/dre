@php
    $item = $property ?? null;
    $details = $item?->details;
    $showLanguageUi = config('cms-kit.common.modules.languages', true);
    $fallbackLocale = config('app.fallback_locale', 'en');
    $translationCollection = $item?->translations ? $item->translations->keyBy('language_code') : collect();
    $selectedNearbyPlaces = collect(old('nearby_places', $item?->nearbyPlaces?->groupBy('type')->map(fn ($group) => $group->pluck('id')->all())->toArray() ?? []));
    $propertyImageConfig = $propertyImageConfig ?? config('cms-kit.images.properties.image', []);
    $sectionIconConfig = config('cms-kit.images.properties.section_icon', []);
    $classificationValues = [
        'property_type' => old('property_type', $item->property_type ?? ''),
        'listing_type' => old('listing_type', $item->listing_type ?? ''),
        'source_type' => old('source_type', $item->source_type ?? 'manual'),
    ];
    $customPropertyTypeValue = old('custom_property_type', '');
    $propertyTypeOptionsByLanguage = [];
    $listingTypeOptionsByLanguage = [];
    $sourceTypeOptionsByLanguage = [];
    $placeTypeSidebarLabels = [];

    if (
        $classificationValues['property_type'] !== ''
        && !array_key_exists($classificationValues['property_type'], $propertyTypes)
        && $classificationValues['property_type'] !== 'custom'
    ) {
        $customPropertyTypeValue = $customPropertyTypeValue !== '' ? $customPropertyTypeValue : $classificationValues['property_type'];
        $classificationValues['property_type'] = 'custom';
    }

    foreach ($languages as $lang) {
        $propertyTypeOptionsByLanguage[$lang->code] = collect($propertyTypes)->mapWithKeys(function ($labels, $value) use ($lang, $fallbackLocale) {
            if (!is_array($labels)) {
                $labels = [$fallbackLocale => (string) $labels];
            }

            $english = trim((string) ($labels[$fallbackLocale] ?? reset($labels) ?: ''));
            $localized = trim((string) ($labels[$lang->code] ?? $english));

            return [$value => ['label' => $localized !== '' ? $localized : $english, 'english' => $english]];
        })->all();
        $propertyTypeOptionsByLanguage[$lang->code]['custom'] = [
            'label' => $lang->code === 'ar' ? 'أخرى / مخصص' : 'Other / Custom',
            'english' => 'Other / Custom',
        ];

        $listingTypeOptionsByLanguage[$lang->code] = collect($listingTypes)->mapWithKeys(function ($labels, $value) use ($lang, $fallbackLocale) {
            if (!is_array($labels)) {
                $labels = [$fallbackLocale => (string) $labels];
            }

            $english = trim((string) ($labels[$fallbackLocale] ?? reset($labels) ?: ''));
            $localized = trim((string) ($labels[$lang->code] ?? $english));

            return [$value => ['label' => $localized !== '' ? $localized : $english, 'english' => $english]];
        })->all();

        $sourceTypeOptionsByLanguage[$lang->code] = collect($sourceTypes)->mapWithKeys(function ($labels, $value) use ($lang, $fallbackLocale) {
            if (!is_array($labels)) {
                $labels = [$fallbackLocale => (string) $labels];
            }
            $english = trim((string) ($labels[$fallbackLocale] ?? reset($labels) ?: ''));
            $localized = trim((string) ($labels[$lang->code] ?? $english));

            return [$value => ['label' => $localized !== '' ? $localized : $english, 'english' => $english]];
        })->all();
    }
    $currentLocale = app()->getLocale();
    $placeTypeSidebarLabels = collect($placeTypes)->mapWithKeys(function ($labels, $value) use ($currentLocale, $fallbackLocale) {
        if (!is_array($labels)) {
            $labels = [$fallbackLocale => (string) $labels];
        }

        $english = trim((string) ($labels[$fallbackLocale] ?? reset($labels) ?: ''));
        $localized = trim((string) ($labels[$currentLocale] ?? $english));

        return [$value => $localized !== '' ? $localized : $english];
    })->all();
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

<input type="hidden" name="property_type" id="propertyTypeMaster" value="{{ $classificationValues['property_type'] }}">
<input type="hidden" name="listing_type" id="listingTypeMaster" value="{{ $classificationValues['listing_type'] }}">
<input type="hidden" name="source_type" id="sourceTypeMaster" value="{{ $classificationValues['source_type'] }}">

<div class="card bg-light border-0 mb-4 property-main-tabs-card">
    <div class="card-body p-4 p-xl-5">
        @if($showLanguageUi)
        <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3 property-top-tabs" id="propertyLanguageTabs" role="tablist">
            @foreach($languages as $lang)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $lang->code === $fallbackLocale ? 'active' : '' }} px-4 py-2 fw-medium" data-bs-toggle="tab" data-bs-target="#property-panel-{{ $lang->code }}" type="button" role="tab">
                    <i class="fas fa-language me-2 opacity-75"></i>{{ $lang->name }}
                </button>
            </li>
            @endforeach
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2 fw-medium" data-bs-toggle="tab" data-bs-target="#property-panel-shared" type="button" role="tab">
                    <i class="fas fa-sliders me-2 opacity-75"></i>Shared Setup
                </button>
            </li>
        </ul>
        @endif

        <div class="tab-content mb-4">
            {{-- Language Specific Panels --}}
            @foreach($languages as $lang)
                @php
                    $translation = $translationCollection->get($lang->code);
                    $keyFeaturesRows = old("translations.{$lang->code}.key_features", $translation?->key_features ?? ($lang->code === $fallbackLocale ? ($details?->key_features ?? []) : []));
                    $keyFeaturesText = old("translations.{$lang->code}.key_features_text", data_get($keyFeaturesRows, '0.text', ''));
                    $translatedAddressPreview = collect([
                        old("translations.{$lang->code}.address", $translation?->address ?? ($lang->code === $fallbackLocale ? $item->address ?? '' : '')),
                        old("translations.{$lang->code}.community", $translation?->community ?? ($lang->code === $fallbackLocale ? $item->community ?? '' : '')),
                        old("translations.{$lang->code}.city", $translation?->city ?? ($lang->code === $fallbackLocale ? $item->city ?? '' : '')),
                        old("translations.{$lang->code}.postal_code", $translation?->postal_code ?? ($lang->code === $fallbackLocale ? $item->postal_code ?? '' : '')),
                        old("translations.{$lang->code}.country", $translation?->country ?? ($lang->code === $fallbackLocale ? $item->country ?? '' : '')),
                    ])->filter(fn ($value) => trim((string) $value) !== '')->implode(', ');
                    $translatedSectionRows = [
                        'easy_to_access' => old("translations.{$lang->code}.easy_to_access", ($translation?->easy_to_access && count($translation->easy_to_access)) ? $translation->easy_to_access : [['icon' => '', 'label' => '']]),
                        'amenities' => old("translations.{$lang->code}.amenities", ($translation?->amenities && count($translation->amenities)) ? $translation->amenities : [['icon' => '', 'name' => '']]),
                        'property_attributes' => old("translations.{$lang->code}.property_attributes", ($translation?->property_attributes && count($translation->property_attributes)) ? $translation->property_attributes : [['icon' => '', 'name' => '']]),
                    ];
                @endphp
                <div class="tab-pane fade {{ $lang->code === $fallbackLocale ? 'show active' : '' }}" id="property-panel-{{ $lang->code }}" role="tabpanel">
                    <div class="row g-4 property-language-tab-layout">
                        <div class="col-xl-3">
                            <div class="nav flex-column nav-pills property-section-tabs" id="propertySectionTabs-{{ $lang->code }}" role="tablist" aria-orientation="vertical">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#property-section-classification-{{ $lang->code }}" type="button" role="tab">Classification</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-section-address-{{ $lang->code }}" type="button" role="tab">Address</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-section-access-{{ $lang->code }}" type="button" role="tab">Easy Access</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-section-amenities-{{ $lang->code }}" type="button" role="tab">Amenities</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-section-attributes-{{ $lang->code }}" type="button" role="tab">Attributes</button>
                            </div>
                        </div>
                        <div class="col-xl-9">
                            <div class="tab-content property-section-tab-content">
                                <div class="tab-pane fade show active property-content-section-pane" id="property-section-classification-{{ $lang->code }}" role="tabpanel">
                                    <div class="card bg-light border-0 property-classification-card">
                                        <div class="card-body p-4">
                                            {{-- Reference ID & Slug --}}
                                            @if($lang->code === $fallbackLocale)
                                            <div class="row g-4 mb-4">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Reference ID <span class="text-danger">*</span></label>
                                                    <input type="text" name="prop_id" class="form-control @error('prop_id') is-invalid @enderror" value="{{ old('prop_id', $item->prop_id ?? '') }}" placeholder="Optional unique property code" required>
                                                    @error('prop_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Slug</label>
                                                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $item->slug ?? '') }}" placeholder="Auto-generated from title">
                                                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                            </div>
                                            @endif

                                            <div class="row g-4 property-classification-row">
                                                @foreach([
                                                    'property_type' => ['label' => 'Property Type', 'options' => $propertyTypeOptionsByLanguage[$lang->code] ?? []],
                                                    'listing_type' => ['label' => 'Listing Type', 'options' => $listingTypeOptionsByLanguage[$lang->code] ?? []],
                                                ] as $field => $meta)
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">{{ $meta['label'] }} <span class="text-danger">*</span></label>
                                                    <div class="career-dual-dropdown property-classification-proxy @error($field) is-invalid @enderror" data-master-field="{{ $field }}" data-placeholder="Search {{ strtolower($meta['label']) }}">
                                                        <input type="text" class="form-control career-dual-dropdown-input" value="{{ data_get($meta['options'], $classificationValues[$field] . '.label', '') }}" placeholder="Search {{ strtolower($meta['label']) }}" autocomplete="off" required oninvalid="this.setCustomValidity('Please select a {{ strtolower($meta['label']) }}')" oninput="this.setCustomValidity('')">
                                                        <div class="career-dual-dropdown-menu">
                                                            @foreach($meta['options'] as $option => $optionMeta)
                                                                <button type="button" class="career-dual-dropdown-option {{ $classificationValues[$field] === $option ? 'active' : '' }}" data-value="{{ $option }}" data-label="{{ $optionMeta['label'] ?? '' }}">
                                                                    <span class="career-dual-dropdown-primary">{{ $optionMeta['label'] ?? '' }}</span>
                                                                    @if(($optionMeta['english'] ?? '') !== ($optionMeta['label'] ?? ''))
                                                                        <span class="career-dual-dropdown-secondary">{{ $optionMeta['english'] ?? '' }}</span>
                                                                    @endif
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @error($field)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                </div>
                                                @endforeach
                                                <div class="col-md-4 property-custom-type-wrap {{ $classificationValues['property_type'] === 'custom' ? '' : 'd-none' }}">
                                                    <label class="form-label fw-bold">Custom Property Type <span class="text-danger">*</span></label>
                                                    <input type="text" name="custom_property_type" class="form-control" value="{{ $customPropertyTypeValue }}" placeholder="Enter custom type">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="translations[{{ $lang->code }}][title]" class="form-control @error("translations.{$lang->code}.title") is-invalid @enderror" value="{{ old("translations.{$lang->code}.title", $translation?->title ?? ($lang->code === $fallbackLocale ? $item->title ?? '' : '')) }}" required>
                                                    @error("translations.{$lang->code}.title")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Key Features</label>
                                                    <textarea name="translations[{{ $lang->code }}][key_features_text]" rows="4" class="form-control">{{ $keyFeaturesText }}</textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Description</label>
                                                    <textarea name="translations[{{ $lang->code }}][description]" rows="8" class="form-control tinymce-editor">{{ old("translations.{$lang->code}.description", $translation?->description ?? ($lang->code === $fallbackLocale ? $details?->description ?? '' : '')) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade property-content-section-pane" id="property-section-address-{{ $lang->code }}" role="tabpanel">
                                    <div class="card bg-light border-0">
                                        <div class="card-body p-4">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Address <span class="text-danger">*</span></label>
                                                    <input type="text" name="translations[{{ $lang->code }}][address]" class="form-control translated-address-part @error("translations.{$lang->code}.address") is-invalid @enderror" data-lang="{{ $lang->code }}" value="{{ old("translations.{$lang->code}.address", $translation?->address ?? ($lang->code === $fallbackLocale ? $item->address ?? '' : '')) }}" required>
                                                    @error("translations.{$lang->code}.address")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Community</label>
                                                    <input type="text" name="translations[{{ $lang->code }}][community]" class="form-control translated-address-part" data-lang="{{ $lang->code }}" value="{{ old("translations.{$lang->code}.community", $translation?->community ?? ($lang->code === $fallbackLocale ? $item->community ?? '' : '')) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">City <span class="text-danger">*</span></label>
                                                    <input type="text" name="translations[{{ $lang->code }}][city]" class="form-control translated-address-part @error("translations.{$lang->code}.city") is-invalid @enderror" data-lang="{{ $lang->code }}" value="{{ old("translations.{$lang->code}.city", $translation?->city ?? ($lang->code === $fallbackLocale ? $item->city ?? '' : '')) }}" required>
                                                    @error("translations.{$lang->code}.city")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Postal Code</label>
                                                    <input type="text" name="translations[{{ $lang->code }}][postal_code]" class="form-control translated-address-part" data-lang="{{ $lang->code }}" value="{{ old("translations.{$lang->code}.postal_code", $translation?->postal_code ?? ($lang->code === $fallbackLocale ? $item->postal_code ?? '' : '')) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                                                    <input type="text" name="translations[{{ $lang->code }}][country]" class="form-control translated-address-part @error("translations.{$lang->code}.country") is-invalid @enderror" data-lang="{{ $lang->code }}" value="{{ old("translations.{$lang->code}.country", $translation?->country ?? ($lang->code === $fallbackLocale ? $item->country ?? '' : '')) }}" required>
                                                    @error("translations.{$lang->code}.country")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Full Address Preview</label>
                                                    <input type="text" class="form-control bg-white translated-full-address-preview" data-lang="{{ $lang->code }}" value="{{ $translatedAddressPreview }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @foreach([
                                    'easy_to_access' => ['tab' => 'access', 'title' => 'Easy Access', 'value_field' => 'label'],
                                    'amenities' => ['tab' => 'amenities', 'title' => 'Amenities', 'value_field' => 'name'],
                                    'property_attributes' => ['tab' => 'attributes', 'title' => 'Property Attributes', 'value_field' => 'name'],
                                ] as $section => $meta)
                                    <div class="tab-pane fade property-content-section-pane" id="property-section-{{ $meta['tab'] }}-{{ $lang->code }}" role="tabpanel">
                                        <div class="card bg-light border-0 repeater-card" data-repeater="translations[{{ $lang->code }}][{{ $section }}]">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-bold mb-1">{{ $meta['title'] }}</h6>
                                                    <button type="button" class="btn btn-sm btn-outline-primary repeater-add">Add Row</button>
                                                </div>
                                                <div class="repeater-rows">
                                                    @foreach($translatedSectionRows[$section] as $index => $row)
                                                        <div class="row g-3 align-items-end repeater-row mb-3">
                                                            <div class="col-md-3">
                                                                <div class="section-icon-preview-wrap">
                                                                    @if(!empty($row['icon']))
                                                                        <img src="{{ Str::startsWith($row['icon'], ['http://', 'https://', '/storage/']) ? $row['icon'] : asset('storage/' . $row['icon']) }}" class="section-icon-preview">
                                                                    @else
                                                                        <div class="section-icon-placeholder">No Icon</div>
                                                                    @endif
                                                                </div>
                                                                <input type="hidden" name="translations[{{ $lang->code }}][{{ $section }}][{{ $index }}][current_icon]" value="{{ $row['icon'] ?? '' }}">
                                                                <input type="file" name="translations[{{ $lang->code }}][{{ $section }}][{{ $index }}][icon_file]" class="form-control section-icon-file-input" accept="image/*">
                                                            </div>
                                                            <div class="col-md-7">
                                                                <input type="text" name="translations[{{ $lang->code }}][{{ $section }}][{{ $index }}][{{ $meta['value_field'] }}]" class="form-control" value="{{ $row[$meta['value_field']] ?? '' }}">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-outline-danger w-100 repeater-remove">Remove</button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Shared Setup Panel --}}
            <div class="tab-pane fade" id="property-panel-shared" role="tabpanel">
                <div class="row g-4 property-language-tab-layout">
                    <div class="col-xl-3">
                        <div class="nav flex-column nav-pills property-section-tabs" id="propertySharedSectionTabs" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#property-shared-pricing" type="button" role="tab">Pricing & Status</button>
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-shared-map" type="button" role="tab">Map</button>
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-shared-images" type="button" role="tab">Images</button>
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-shared-nearby" type="button" role="tab">Nearby Places</button>
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-shared-publishing" type="button" role="tab">Publishing</button>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        <div class="tab-content property-section-tab-content">
                            <div class="tab-pane fade show active property-content-section-pane" id="property-shared-pricing" role="tabpanel">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-4">
                                        <div class="row g-4">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Price <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="0" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $item->price ?? '') }}" required>
                                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Currency <span class="text-danger">*</span></label>
                                                <select name="currency" class="form-select @error('currency') is-invalid @enderror" required>
                                                    <option value="">Select currency</option>
                                                    @foreach($currencies as $value => $label)
                                                        <option value="{{ $value }}" @selected(old('currency', $item->currency ?? '') === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Agent <span class="text-danger">*</span></label>
                                                <select name="agent_id" class="form-select @error('agent_id') is-invalid @enderror" required>
                                                    <option value="">Select agent</option>
                                                    @foreach($agents as $agent)
                                                        <option value="{{ $agent->id }}" @selected((string) old('agent_id', $item->agent_id ?? '') === (string) $agent->id)>{{ $agent->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('agent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Direct From Owner</label>
                                                <input type="text" name="direct_from_owner" class="form-control @error('direct_from_owner') is-invalid @enderror" value="{{ old('direct_from_owner', $details?->direct_from_owner ?? '') }}" placeholder="Example: Yes, Owner Listed">
                                                @error('direct_from_owner')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Security Deposit</label>
                                                <input type="number" step="0.01" min="0" name="security_deposit" class="form-control @error('security_deposit') is-invalid @enderror" value="{{ old('security_deposit', $details?->security_deposit ?? '') }}">
                                                @error('security_deposit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold">Bedrooms</label>
                                                <input type="number" min="0" name="bedrooms" class="form-control @error('bedrooms') is-invalid @enderror" value="{{ old('bedrooms', $item->bedrooms ?? '') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold">Bathrooms</label>
                                                <input type="number" min="0" name="bathrooms" class="form-control @error('bathrooms') is-invalid @enderror" value="{{ old('bathrooms', $item->bathrooms ?? '') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fw-bold">Sqft</label>
                                                <input type="number" min="0" name="sqft" class="form-control @error('sqft') is-invalid @enderror" value="{{ old('sqft', $item->sqft ?? '') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Year Built</label>
                                                <input type="number" min="1800" name="year_built" class="form-control @error('year_built') is-invalid @enderror" value="{{ old('year_built', $details?->year_built ?? '') }}">
                                            </div>
                                            <div class="col-md-4 d-flex align-items-end">
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="status" id="propertyStatus" value="1" {{ old('status', $item->status ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="propertyStatus">Active Status</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade property-content-section-pane" id="property-shared-map" role="tabpanel">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-4">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Latitude</label>
                                                <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $item->latitude ?? '') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Longitude</label>
                                                <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $item->longitude ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade property-content-section-pane" id="property-shared-images" role="tabpanel">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
                                            <div>
                                                <h6 class="fw-bold mb-1">Images</h6>
                                                <small class="text-muted d-block">Recommended: {{ $propertyImageConfig['width'] ?? 1400 }}x{{ $propertyImageConfig['height'] ?? 900 }}px. Max size: {{ $propertyImageConfig['max_size'] ?? 4096 }} KB.</small>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="propertyImageReorderToggle"><i class="fas fa-arrows-alt me-1"></i>Reorder</button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="propertyImageQuickPick"><i class="fas fa-folder-open me-1"></i>Select Images</button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="propertyImageClearAll"><i class="fas fa-trash-alt me-1"></i>Remove All</button>
                                            </div>
                                        </div>
                                        <input type="file" id="propertyImageQuickInput" class="d-none" accept="image/*" multiple>
                                        <div id="propertyImageRows" class="property-image-board">
                                            @foreach($item?->images ?? [] as $image)
                                                <div class="image-row existing-image-row" draggable="false" data-image-kind="existing">
                                                    <div class="property-image-card property-image-card-existing h-100">
                                                        <div class="property-image-card-preview">
                                                            <img src="{{ asset('storage/' . $image->image) }}" alt="" class="property-image-card-thumb">
                                                            <span class="property-image-rank-badge">{{ $loop->iteration }}</span>
                                                            <span class="property-image-feature-badge {{ $loop->first ? '' : 'd-none' }}">Featured</span>
                                                        </div>
                                                        <div class="property-image-card-body">
                                                            <div class="property-image-actions">
                                                                <button type="button" class="property-image-action-icon-btn remove-image-row">&times;</button>
                                                            </div>
                                                            <input type="hidden" name="existing_images[{{ $image->id }}][order]" value="{{ old("existing_images.{$image->id}.order", $image->order) }}" class="property-image-order-input">
                                                            <input type="hidden" name="existing_images[{{ $image->id }}][delete]" value="0" class="property-image-delete-input">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @foreach(old('new_images', []) as $index => $imageRow)
                                                <div class="image-row" draggable="false" data-image-kind="new">
                                                    <div class="property-image-card h-100">
                                                        <div class="property-image-card-preview image-row-preview-wrap">
                                                            <div class="image-row-preview-placeholder">New</div>
                                                            <span class="property-image-rank-badge">{{ $loop->iteration + (count($item?->images ?? [])) }}</span>
                                                            <span class="property-image-feature-badge d-none">Featured</span>
                                                        </div>
                                                        <div class="property-image-card-body">
                                                            <input type="file" name="new_images[{{ $index }}][file]" class="form-control property-image-file-input d-none" accept="image/*">
                                                            <input type="hidden" name="new_images[{{ $index }}][order]" value="{{ $imageRow['order'] ?? ($index + 1) }}" class="property-image-order-input">
                                                            <div class="property-image-actions">
                                                                <button type="button" class="property-image-action-icon-btn remove-image-row">&times;</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade property-content-section-pane" id="property-shared-nearby" role="tabpanel">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3 gap-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">Assign Nearby Places</h6>
                                                <small class="text-muted d-block">Choose specific schools, hospitals, etc. near this property.</small>
                                            </div>
                                            <a href="{{ route('cms.nearby-places.index') }}" class="btn btn-sm btn-outline-secondary">Manage</a>
                                        </div>
                                        @foreach($placeTypeSidebarLabels as $type => $label)
                                            <div class="nearby-place-card mb-3" id="nearby-place-card-{{ $type }}">
                                                <label class="form-label fw-bold mb-1">{{ $label }}</label>
                                                @php
                                                    $selectedTypePlaces = collect($selectedNearbyPlaces->get($type, []))->map(fn ($value) => (string) $value);
                                                @endphp
                                                <select name="nearby_places[{{ $type }}][]" class="form-select nearby-places-select2" multiple data-placeholder="Search {{ strtolower($label) }}" data-dropdown-parent="#nearby-place-card-{{ $type }}">
                                                    @foreach(($nearbyPlaces[$type] ?? collect()) as $place)
                                                        @php
                                                            $placeName = $place->getTranslation('name') ?: $place->name;
                                                        @endphp
                                                        <option value="{{ $place->id }}" @selected($selectedTypePlaces->contains((string) $place->id))>{{ $placeName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade property-content-section-pane" id="property-shared-publishing" role="tabpanel">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-4">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Published At</label>
                                                <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', optional($item->published_at ?? now())->format('Y-m-d\TH:i')) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Display Order</label>
                                                <input type="number" min="1" name="order" class="form-control" value="{{ old('order', $item->order ?? $nextOrder ?? 1) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-top pt-4">
            <button type="submit" class="btn btn-primary px-4">{{ $submitLabel ?? 'Save Property' }}</button>
            <a href="{{ route('cms.properties.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>
    </div>
</div>

@push('styles')
<style>
    .property-classification-card,.property-classification-card .card-body,.property-classification-row { overflow: visible !important; }
    .career-dual-dropdown { position: relative; }
    .career-dual-dropdown-input { cursor: pointer; background-image: linear-gradient(45deg, transparent 50%, #6b7280 50%), linear-gradient(135deg, #6b7280 50%, transparent 50%); background-position: calc(100% - 18px) calc(50% - 3px), calc(100% - 12px) calc(50% - 3px); background-size: 6px 6px, 6px 6px; background-repeat: no-repeat; padding-right: 2.5rem; }
    .career-dual-dropdown-menu { position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 1055; display: none; max-height: 260px; overflow-y: auto; background: #fff; border: 1px solid #dfe5ec; border-radius: 12px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12); }
    .career-dual-dropdown.open .career-dual-dropdown-menu { display: block; }
    .career-dual-dropdown-option { width: 100%; padding: 0.85rem 1rem; display: flex; justify-content: space-between; gap: 1rem; border: 0; background: transparent; text-align: left; transition: background 0.2s; }
    .career-dual-dropdown-option + .career-dual-dropdown-option { border-top: 1px solid #edf1f5; }
    .career-dual-dropdown-option:hover,.career-dual-dropdown-option.active { background: #f8fafc; }
    .career-dual-dropdown-primary { color: #1f2937; font-weight: 500; }
    .career-dual-dropdown-secondary { color: #64748b; font-size: 0.875rem; }

    .property-main-tabs-card { border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .property-top-tabs .nav-link { border-radius: 10px; color: #64748b; font-weight: 600; }
    .property-top-tabs .nav-link.active { background: #0d6efd; color: #fff; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2); }

    .property-section-tabs .nav-link { text-align: left; margin-bottom: 0.5rem; border-radius: 10px; font-weight: 500; color: #475569; background: #fff; border: 1px solid #e2e8f0; }
    .property-section-tabs .nav-link.active { background: #0d6efd; color: #fff; border-color: #0d6efd; }
    .property-section-tabs .nav-link.has-error { border-color: #dc3545; color: #dc3545; }
    .property-section-tabs .nav-link.has-error::after { content: ' \f06a'; font-family: 'Font Awesome 6 Free'; font-weight: 900; float: right; }

    .property-image-board { display: flex; flex-wrap: wrap; gap: 1rem; min-height: 120px; padding: 1rem; border: 2px dashed #cbd5e1; border-radius: 15px; transition: border-color 0.3s, background 0.3s; }
    .property-image-board.reordering-active { border-color: #0d6efd; background: #f0f7ff; cursor: grab; }
    .property-image-card { width: 100px; position: relative; }
    .property-image-card-preview { height: 100px; border-radius: 10px; overflow: hidden; background: #f1f5f9; position: relative; }
    .property-image-card-thumb { width:100%; height:100%; object-fit: cover; }
    .property-image-rank-badge { position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.6); color:#fff; font-size: 10px; padding: 2px 6px; border-radius: 10px; }
    .property-image-feature-badge { position: absolute; bottom: 5px; left: 5px; background: #0d6efd; color:#fff; font-size: 9px; padding: 2px 6px; border-radius: 10px; text-transform: uppercase; }
    .property-image-actions { position: absolute; top: -8px; left: -8px; }
    .property-image-action-icon-btn { width: 20px; height: 20px; border-radius: 50%; background: #dc3545; color:#fff; border: 0; display: flex; align-items: center; justify-content: center; font-size: 12px; }

    .section-icon-preview-wrap { width: 60px; height: 60px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .section-icon-preview { width: 100%; height: 100%; object-fit: contain; }
    .section-icon-placeholder { font-size: 10px; color: #94a3b8; text-align: center; }

    .nearby-place-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; background: #fff; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    const propertyClassificationMasterIds = { property_type: 'propertyTypeMaster', listing_type: 'listingTypeMaster', source_type: 'sourceTypeMaster' };

    // TinyMCE Init
    if (typeof tinymce !== 'undefined') {
        tinymce.init({ selector: '.tinymce-editor', height: 320, plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount', toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help' });
    }

    // Classification Proxy Logic
    function updatePropertyDropdownState(dropdown, value) {
        const input = dropdown.querySelector('.career-dual-dropdown-input');
        const options = dropdown.querySelectorAll('.career-dual-dropdown-option');
        let label = '';
        options.forEach(opt => {
            const match = opt.dataset.value === value;
            opt.classList.toggle('active', match);
            if (match) label = opt.dataset.label;
        });
        if (input) {
            input.value = label;
            // Clear the browser's required error message once a selection is made
            if (typeof input.setCustomValidity === 'function') {
                input.setCustomValidity('');
            }
            // Dispatch event to trigger any listeners (like the oninput I added earlier)
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
        if (dropdown.dataset.masterField === 'property_type') toggleCustomPropertyType(value === 'custom');
    }

    function toggleCustomPropertyType(show) {
        document.querySelectorAll('.property-custom-type-wrap').forEach(el => el.classList.toggle('d-none', !show));
    }

    function syncPropertyClassification(field, value, source) {
        const masterInput = document.getElementById(propertyClassificationMasterIds[field]);
        if (masterInput) masterInput.value = value;
        document.querySelectorAll(`.property-classification-proxy[data-master-field="${field}"]`).forEach(el => {
            if (el !== source) updatePropertyDropdownState(el, value);
        });
    }

    document.querySelectorAll('.property-classification-proxy').forEach(el => {
        const field = el.dataset.masterField;
        const input = el.querySelector('.career-dual-dropdown-input');

        input.addEventListener('focus', function() {
            document.querySelectorAll('.career-dual-dropdown').forEach(d => d.classList.remove('open'));
            el.classList.add('open');
            this.select();
        });

        el.querySelectorAll('.career-dual-dropdown-option').forEach(opt => {
            opt.addEventListener('click', function() {
                const val = this.dataset.value;
                syncPropertyClassification(field, val, el);
                updatePropertyDropdownState(el, val);
                el.classList.remove('open');
            });
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.career-dual-dropdown')) {
            document.querySelectorAll('.career-dual-dropdown').forEach(d => d.classList.remove('open'));
        }
    });

    // Address Preview
    function updateTranslatedAddressPreview(lang) {
        const parts = Array.from(document.querySelectorAll(`.translated-address-part[data-lang="${lang}"]`)).map(i => i.value.trim()).filter(v => v !== '');
        const preview = document.querySelector(`.translated-full-address-preview[data-lang="${lang}"]`);
        if (preview) preview.value = parts.join(', ');
    }
    document.querySelectorAll('.translated-address-part').forEach(i => i.addEventListener('input', function() { updateTranslatedAddressPreview(this.dataset.lang); }));

    // Repeater Logic
    document.querySelectorAll('.repeater-card').forEach(card => {
        const rows = card.querySelector('.repeater-rows');
        card.querySelector('.repeater-add').addEventListener('click', () => {
            const first = rows.querySelector('.repeater-row');
            if (first) {
                const clone = first.cloneNode(true);
                clone.querySelectorAll('input, textarea').forEach(i => i.value = '');
                clone.querySelector('.section-icon-preview-wrap').innerHTML = '<div class="section-icon-placeholder">No Icon</div>';
                rows.appendChild(clone);
                reindexRepeater(card);
            }
        });
        rows.addEventListener('click', (e) => {
            if (e.target.classList.contains('repeater-remove')) {
                const count = rows.querySelectorAll('.repeater-row').length;
                if (count > 1) {
                    e.target.closest('.repeater-row').remove();
                    reindexRepeater(card);
                } else {
                    e.target.closest('.repeater-row').querySelectorAll('input, textarea').forEach(i => i.value = '');
                }
            }
        });
    });

    function reindexRepeater(card) {
        const name = card.dataset.repeater;
        card.querySelectorAll('.repeater-row').forEach((row, idx) => {
            row.querySelectorAll('input, textarea').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, `[${idx}]`);
            });
        });
    }

    // Image Board Sorting/Management
    const imageRows = document.getElementById('propertyImageRows');
    function syncImageBoardState() {
        if (!imageRows) return;
        const rows = Array.from(imageRows.querySelectorAll('.image-row:not(.d-none)'));
        rows.forEach((row, idx) => {
            const order = idx + 1;
            const orderInput = row.querySelector('.property-image-order-input');
            const rankBadge = row.querySelector('.property-image-rank-badge');
            const featureBadge = row.querySelector('.property-image-feature-badge');
            if (orderInput) orderInput.value = order;
            if (rankBadge) rankBadge.textContent = order;
            if (featureBadge) featureBadge.classList.toggle('d-none', idx !== 0);
        });
    }

    const imageQuickPick = document.getElementById('propertyImageQuickPick');
    const imageQuickInput = document.getElementById('propertyImageQuickInput');
    const imageClearAll = document.getElementById('propertyImageClearAll');

    if (imageQuickPick && imageQuickInput) {
        imageQuickPick.addEventListener('click', () => imageQuickInput.click());
        imageQuickInput.addEventListener('change', function() {
            if (!this.files.length) return;
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const idx = document.querySelectorAll('.image-row').length;
                    const html = `
                        <div class="image-row" draggable="false" data-image-kind="new">
                            <div class="property-image-card h-100">
                                <div class="property-image-card-preview image-row-preview-wrap">
                                    <img src="${e.target.result}" class="property-image-card-thumb">
                                    <span class="property-image-rank-badge">${idx + 1}</span>
                                    <span class="property-image-feature-badge d-none">Featured</span>
                                </div>
                                <div class="property-image-card-body">
                                    <input type="file" name="new_images[${idx}][file]" class="form-control property-image-file-input d-none">
                                    <input type="hidden" name="new_images[${idx}][order]" value="${idx + 1}" class="property-image-order-input">
                                    <div class="property-image-actions">
                                        <button type="button" class="property-image-action-icon-btn remove-image-row">&times;</button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    const container = document.createElement('div');
                    container.innerHTML = html;
                    const row = container.firstElementChild;
                    imageRows.appendChild(row);

                    const dt = new DataTransfer();
                    dt.items.add(file);
                    row.querySelector('.property-image-file-input').files = dt.files;
                    syncImageBoardState();
                };
                reader.readAsDataURL(file);
            });
            this.value = '';
        });
    }

    if (imageRows) {
        imageRows.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-image-row')) {
                const row = e.target.closest('.image-row');
                if (row.dataset.imageKind === 'existing') {
                    row.classList.add('d-none');
                    row.querySelector('.property-image-delete-input').value = '1';
                } else {
                    row.remove();
                }
                syncImageBoardState();
            }
        });
    }

    if (imageClearAll) {
        imageClearAll.addEventListener('click', () => {
            if (confirm('Are you sure you want to remove all images?')) {
                imageRows.querySelectorAll('.image-row').forEach(row => {
                    if (row.dataset.imageKind === 'existing') {
                        row.classList.add('d-none');
                        row.querySelector('.property-image-delete-input').value = '1';
                    } else {
                        row.remove();
                    }
                });
                syncImageBoardState();
            }
        });
    }

    const imageReorderToggle = document.getElementById('propertyImageReorderToggle');
    let imageSortable = null;
    if (typeof Sortable !== 'undefined' && imageRows) {
        imageSortable = new Sortable(imageRows, { 
            animation: 150, 
            onEnd: syncImageBoardState,
            disabled: true 
        });
    }

    if (imageReorderToggle && imageSortable) {
        imageReorderToggle.addEventListener('click', function() {
            const currentlyDisabled = imageSortable.options.disabled;
            imageSortable.option('disabled', !currentlyDisabled);
            
            this.classList.toggle('btn-primary', currentlyDisabled);
            this.classList.toggle('btn-outline-secondary', !currentlyDisabled);
            imageRows.classList.toggle('reordering-active', currentlyDisabled);
            
            this.innerHTML = currentlyDisabled 
                ? '<i class="fas fa-check me-1"></i>Done Reordering' 
                : '<i class="fas fa-arrows-alt me-1"></i>Reorder';
        });
    }

    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('section-icon-file-input')) {
            const wrap = e.target.closest('.col-md-3').querySelector('.section-icon-preview-wrap');
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (re) => {
                    wrap.innerHTML = `<img src="${re.target.result}" class="section-icon-preview">`;
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        }
    });

    // Tab Navigation Logic for Validation
    function activateAncestorTabs(element) {
        let pane = element.closest('.tab-pane');
        while (pane) {
            const trigger = document.querySelector(`[data-bs-target="#${pane.id}"]`);
            if (trigger) {
                const tab = bootstrap.Tab.getOrCreateInstance(trigger);
                tab.show();
            }
            pane = pane.parentElement ? pane.parentElement.closest('.tab-pane') : null;
        }
    }

    function markErrorTabs() {
        document.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
        document.querySelectorAll('.is-invalid, :invalid').forEach(el => {
            let pane = el.closest('.tab-pane');
            while (pane) {
                const trigger = document.querySelector(`[data-bs-target="#${pane.id}"]`);
                if (trigger) trigger.classList.add('has-error');
                pane = pane.parentElement ? pane.parentElement.closest('.tab-pane') : null;
            }
        });
    }

    let isValidationSwitching = false;
    document.addEventListener('invalid', function(e) {
        if (isValidationSwitching) return;
        isValidationSwitching = true;
        activateAncestorTabs(e.target);
        markErrorTabs();
        setTimeout(() => { isValidationSwitching = false; }, 100);
    }, true);

    window.addEventListener('load', () => {
        if (document.querySelector('.is-invalid')) {
            markErrorTabs();
            activateAncestorTabs(document.querySelector('.is-invalid'));
        }
    });

    // Select2 Init
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.nearby-places-select2').select2({ width: '100%', allowClear: true });
    }
</script>
@endpush
