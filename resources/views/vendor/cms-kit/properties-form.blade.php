
@php
    $item = $property ?? null;
    $details = $item?->details;
    $showLanguageUi = config('cms-kit.common.modules.languages', true);
    $fallbackLocale = config('app.fallback_locale', 'en');
    $translationCollection = $item?->translations ? $item->translations->keyBy('language_code') : collect();
    $selectedNearbyPlaces = collect(old('nearby_places', $item?->nearbyPlaces?->groupBy('type')->map(fn ($group) => $group->pluck('id')->all())->toArray() ?? []));
    $propertyImageConfig = $propertyImageConfig ?? config('cms-kit.images.properties.image', []);
    $classificationValues = [
        'property_type' => old('property_type', $item->property_type ?? ''),
        'listing_type' => old('listing_type', $item->listing_type ?? ''),
        'source_type' => old('source_type', $item->source_type ?? 'manual'),
    ];
    $propertyTypeOptionsByLanguage = [];
    $listingTypeOptionsByLanguage = [];
    $sourceTypeOptionsByLanguage = [];
    $placeTypeSidebarLabels = [];
    foreach ($languages as $lang) {
        $propertyTypeOptionsByLanguage[$lang->code] = collect($propertyTypes)->mapWithKeys(function ($labels, $value) use ($lang, $fallbackLocale) {
            if (!is_array($labels)) {
                $labels = [$fallbackLocale => (string) $labels];
            }

            $english = trim((string) ($labels[$fallbackLocale] ?? reset($labels) ?: ''));
            $localized = trim((string) ($labels[$lang->code] ?? $english));

            return [$value => ['label' => $localized !== '' ? $localized : $english, 'english' => $english]];
        })->all();

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

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <label class="form-label fw-bold">Reference ID</label>
        <input type="text" name="prop_id" class="form-control @error('prop_id') is-invalid @enderror" value="{{ old('prop_id', $item->prop_id ?? '') }}" placeholder="Optional unique property code">
        @error('prop_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold">Slug</label>
        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $item->slug ?? '') }}" placeholder="Auto-generated from title if empty">
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <div class="h-100 d-flex align-items-end">
            <div class="text-muted small">Publishing, images, map, and nearby places are now grouped inside the shared setup tab for a wider editing layout.</div>
        </div>
    </div>
</div>

<div class="alert alert-light border-start border-primary border-4 py-2 mb-4 shadow-sm" style="font-size: 0.9rem;">
    <i class="fas fa-info-circle text-primary me-2"></i>
    <strong>Note:</strong> All translated content and shared listing setup now live inside tab sections, so the form has more working space and validation can jump directly to the right tab.
</div>

<div class="card bg-light border-0 mb-4 property-main-tabs-card">
    <div class="card-body p-4 p-xl-5">
        @if($showLanguageUi)
        <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3 property-top-tabs" id="propertyLanguageTabs" role="tablist">
            @foreach($languages as $lang)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }} px-4 py-2 fw-medium" data-bs-toggle="tab" data-bs-target="#property-panel-{{ $lang->code }}" type="button" role="tab">
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
            @foreach($languages as $lang)
                @php
                    $translation = $translationCollection->get($lang->code);
                    $translatedAddressPreview = collect([
                        old("translations.{$lang->code}.address", $translation?->address ?? ($lang->code === $fallbackLocale ? $item->address ?? '' : '')),
                        old("translations.{$lang->code}.community", $translation?->community ?? ($lang->code === $fallbackLocale ? $item->community ?? '' : '')),
                        old("translations.{$lang->code}.city", $translation?->city ?? ($lang->code === $fallbackLocale ? $item->city ?? '' : '')),
                        old("translations.{$lang->code}.zip_code", $translation?->zip_code ?? ($lang->code === $fallbackLocale ? $item->zip_code ?? '' : '')),
                        old("translations.{$lang->code}.country", $translation?->country ?? ($lang->code === $fallbackLocale ? $item->country ?? '' : '')),
                    ])->filter(fn ($value) => trim((string) $value) !== '')->implode(', ');
                    $translatedSectionRows = [
                        'easy_to_access' => old("translations.{$lang->code}.easy_to_access", $translation?->easy_to_access ?? ($lang->code === $fallbackLocale ? ($details?->easy_to_access ?? [['icon' => '', 'label' => '']]) : [['icon' => '', 'label' => '']])),
                        'key_features' => old("translations.{$lang->code}.key_features", $translation?->key_features ?? ($lang->code === $fallbackLocale ? ($details?->key_features ?? [['text' => '']]) : [['text' => '']])),
                        'amenities' => old("translations.{$lang->code}.amenities", $translation?->amenities ?? ($lang->code === $fallbackLocale ? ($details?->amenities ?? [['icon' => '', 'name' => '']]) : [['icon' => '', 'name' => '']])),
                        'property_attributes' => old("translations.{$lang->code}.property_attributes", $translation?->property_attributes ?? ($lang->code === $fallbackLocale ? ($details?->property_attributes ?? [['icon' => '', 'name' => '']]) : [['icon' => '', 'name' => '']])),
                    ];
                @endphp
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="property-panel-{{ $lang->code }}" role="tabpanel">
                    <div class="row g-4 property-language-tab-layout">
                        <div class="col-xl-3">
                            <div class="nav flex-column nav-pills property-section-tabs" id="propertySectionTabs-{{ $lang->code }}" role="tablist" aria-orientation="vertical">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#property-section-classification-{{ $lang->code }}" type="button" role="tab">Classification</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-section-content-{{ $lang->code }}" type="button" role="tab">Content</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-section-address-{{ $lang->code }}" type="button" role="tab">Address</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-section-access-{{ $lang->code }}" type="button" role="tab">Easy Access</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-section-features-{{ $lang->code }}" type="button" role="tab">Key Features</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-section-amenities-{{ $lang->code }}" type="button" role="tab">Amenities</button>
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#property-section-attributes-{{ $lang->code }}" type="button" role="tab">Attributes</button>
                            </div>
                        </div>
                        <div class="col-xl-9">
                            <div class="tab-content property-section-tab-content">
                                <div class="tab-pane fade show active property-content-section-pane" id="property-section-classification-{{ $lang->code }}" role="tabpanel">
                                    <div class="card bg-light border-0 property-classification-card">
                                        <div class="card-body p-4">
                                            <div class="row g-4 property-classification-row">
                                                @foreach([
                                                    'property_type' => ['label' => 'Property Type', 'options' => $propertyTypeOptionsByLanguage[$lang->code] ?? []],
                                                    'listing_type' => ['label' => 'Listing Type', 'options' => $listingTypeOptionsByLanguage[$lang->code] ?? []],
                                                    'source_type' => ['label' => 'Source Type', 'options' => $sourceTypeOptionsByLanguage[$lang->code] ?? []],
                                                ] as $field => $meta)
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">{{ $meta['label'] }} <span class="text-danger">*</span></label>
                                                    <div class="career-dual-dropdown property-classification-proxy @error($field) is-invalid @enderror" data-master-field="{{ $field }}" data-placeholder="Search {{ strtolower($meta['label']) }}">
                                                        <input type="text" class="form-control career-dual-dropdown-input" value="{{ data_get($meta['options'], $classificationValues[$field] . '.label', '') }}" placeholder="Search {{ strtolower($meta['label']) }}" autocomplete="off">
                                                        <div class="career-dual-dropdown-menu">
                                                            @foreach($meta['options'] as $option => $optionMeta)
                                                                <button type="button" class="career-dual-dropdown-option {{ $classificationValues[$field] === $option ? 'active' : '' }}" data-value="{{ $option }}" data-label="{{ $optionMeta['label'] ?? '' }}">
                                                                    <span class="career-dual-dropdown-primary">{{ $optionMeta['label'] ?? '' }}</span>
                                                                    @if(($optionMeta['english'] ?? '') !== ($optionMeta['label'] ?? ''))
                                                                        <span class="career-dual-dropdown-secondary">{{ $optionMeta['english'] ?? '' }}</span>
                                                                    @endif
                                                                </button>
                                                            @endforeach
                                                            <div class="career-dual-dropdown-empty d-none">No options found.</div>
                                                        </div>
                                                    </div>
                                                    @error($field)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade property-content-section-pane" id="property-section-content-{{ $lang->code }}" role="tabpanel">
                                    <div class="card bg-light border-0">
                                        <div class="card-body p-4">
                                            <div class="row g-4">
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="translations[{{ $lang->code }}][title]" class="form-control @error("translations.{$lang->code}.title") is-invalid @enderror" value="{{ old("translations.{$lang->code}.title", $translation?->title ?? ($lang->code === $fallbackLocale ? $item->title ?? '' : '')) }}" required>
                                                    @error("translations.{$lang->code}.title")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Description</label>
                                                    <textarea name="translations[{{ $lang->code }}][description]" rows="8" class="form-control tinymce-editor @error("translations.{$lang->code}.description") is-invalid @enderror">{{ old("translations.{$lang->code}.description", $translation?->description ?? ($lang->code === $fallbackLocale ? $details?->description ?? '' : '')) }}</textarea>
                                                    @error("translations.{$lang->code}.description")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
                                                    <label class="form-label fw-bold">Address</label>
                                                    <input type="text" name="translations[{{ $lang->code }}][address]" class="form-control translated-address-part @error("translations.{$lang->code}.address") is-invalid @enderror" data-lang="{{ $lang->code }}" value="{{ old("translations.{$lang->code}.address", $translation?->address ?? ($lang->code === $fallbackLocale ? $item->address ?? '' : '')) }}" placeholder="Street / building / block">
                                                    @error("translations.{$lang->code}.address")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Community</label>
                                                    <input type="text" name="translations[{{ $lang->code }}][community]" class="form-control translated-address-part @error("translations.{$lang->code}.community") is-invalid @enderror" data-lang="{{ $lang->code }}" value="{{ old("translations.{$lang->code}.community", $translation?->community ?? ($lang->code === $fallbackLocale ? $item->community ?? '' : '')) }}">
                                                    @error("translations.{$lang->code}.community")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">City</label>
                                                    <input type="text" name="translations[{{ $lang->code }}][city]" class="form-control translated-address-part @error("translations.{$lang->code}.city") is-invalid @enderror" data-lang="{{ $lang->code }}" value="{{ old("translations.{$lang->code}.city", $translation?->city ?? ($lang->code === $fallbackLocale ? $item->city ?? '' : '')) }}">
                                                    @error("translations.{$lang->code}.city")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Zip Code</label>
                                                    <input type="text" name="translations[{{ $lang->code }}][zip_code]" class="form-control translated-address-part @error("translations.{$lang->code}.zip_code") is-invalid @enderror" data-lang="{{ $lang->code }}" value="{{ old("translations.{$lang->code}.zip_code", $translation?->zip_code ?? ($lang->code === $fallbackLocale ? $item->zip_code ?? '' : '')) }}">
                                                    @error("translations.{$lang->code}.zip_code")<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Country</label>
                                                    <input type="text" name="translations[{{ $lang->code }}][country]" class="form-control translated-address-part @error("translations.{$lang->code}.country") is-invalid @enderror" data-lang="{{ $lang->code }}" value="{{ old("translations.{$lang->code}.country", $translation?->country ?? ($lang->code === $fallbackLocale ? $item->country ?? '' : '')) }}">
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
                                    'easy_to_access' => ['tab' => 'access', 'title' => 'Easy Access', 'fields' => ['icon' => ['label' => 'Icon Class / Image URL', 'type' => 'text'], 'label' => ['label' => 'Label', 'type' => 'text']]],
                                    'key_features' => ['tab' => 'features', 'title' => 'Key Features', 'fields' => ['text' => ['label' => 'Text', 'type' => 'textarea']]],
                                    'amenities' => ['tab' => 'amenities', 'title' => 'Amenities', 'fields' => ['icon' => ['label' => 'Icon Class / Image URL', 'type' => 'text'], 'name' => ['label' => 'Name', 'type' => 'text']]],
                                    'property_attributes' => ['tab' => 'attributes', 'title' => 'Property Attributes', 'fields' => ['icon' => ['label' => 'Icon Class / Image URL', 'type' => 'text'], 'name' => ['label' => 'Name', 'type' => 'text']]],
                                ] as $section => $meta)
                                    <div class="tab-pane fade property-content-section-pane" id="property-section-{{ $meta['tab'] }}-{{ $lang->code }}" role="tabpanel">
                                        <div class="card bg-light border-0 repeater-card" data-repeater="translations[{{ $lang->code }}][{{ $section }}]">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h6 class="fw-bold mb-1">{{ $meta['title'] }}</h6>
                                                        @if(in_array($section, ['easy_to_access', 'amenities', 'property_attributes'], true))
                                                            <small class="text-muted">Use a Font Awesome class like `fa-solid fa-school` or an image path like `/storage/icons/school.svg`. This content is translated per language.</small>
                                                        @elseif($section === 'key_features')
                                                            <small class="text-muted">Use textarea rows so each key feature can hold longer translated copy.</small>
                                                        @endif
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary repeater-add">Add Row</button>
                                                </div>
                                                <div class="repeater-rows">
                                                    @foreach($translatedSectionRows[$section] as $index => $row)
                                                        <div class="row g-3 align-items-end repeater-row mb-3">
                                                            @foreach($meta['fields'] as $field => $fieldMeta)
                                                                <div class="col-md-{{ count($meta['fields']) === 1 ? 10 : 5 }}">
                                                                    <label class="form-label fw-bold">{{ $fieldMeta['label'] }}</label>
                                                                    @if($fieldMeta['type'] === 'textarea')
                                                                        <textarea name="translations[{{ $lang->code }}][{{ $section }}][{{ $index }}][{{ $field }}]" rows="3" class="form-control" placeholder="Write translated feature text">{{ $row[$field] ?? '' }}</textarea>
                                                                    @else
                                                                        <input type="text" name="translations[{{ $lang->code }}][{{ $section }}][{{ $index }}][{{ $field }}]" class="form-control" value="{{ $row[$field] ?? '' }}" placeholder="{{ str_contains(strtolower($fieldMeta['label']), 'icon') ? 'fa-solid fa-star or /storage/icons/item.svg' : '' }}">
                                                                    @endif
                                                                </div>
                                                            @endforeach
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

            <div class="tab-pane fade" id="property-panel-shared" role="tabpanel">
                <div class="row g-4 property-language-tab-layout">
                    <div class="col-xl-3">
                        <div class="nav flex-column nav-pills property-section-tabs" id="propertySharedSectionTabs" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#property-shared-pricing" type="button" role="tab">Pricing</button>
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
                                                <label class="form-label fw-bold">Price</label>
                                                <input type="number" step="0.01" min="0" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $item->price ?? '') }}">
                                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Currency</label>
                                                <select name="currency" class="form-select @error('currency') is-invalid @enderror">
                                                    <option value="">Select currency</option>
                                                    @foreach($currencies as $value => $label)
                                                        <option value="{{ $value }}" @selected(old('currency', $item->currency ?? '') === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Security Deposit</label>
                                                <input type="number" step="0.01" min="0" name="security_deposit" class="form-control @error('security_deposit') is-invalid @enderror" value="{{ old('security_deposit', $details?->security_deposit ?? '') }}">
                                                @error('security_deposit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Bedrooms</label>
                                                <input type="number" min="0" name="bedrooms" class="form-control @error('bedrooms') is-invalid @enderror" value="{{ old('bedrooms', $item->bedrooms ?? '') }}">
                                                @error('bedrooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Bathrooms</label>
                                                <input type="number" min="0" name="bathrooms" class="form-control @error('bathrooms') is-invalid @enderror" value="{{ old('bathrooms', $item->bathrooms ?? '') }}">
                                                @error('bathrooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Sqft</label>
                                                <input type="number" min="0" name="sqft" class="form-control @error('sqft') is-invalid @enderror" value="{{ old('sqft', $item->sqft ?? '') }}">
                                                @error('sqft')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Year Built</label>
                                                <input type="number" min="1800" name="year_built" class="form-control @error('year_built') is-invalid @enderror" value="{{ old('year_built', $details?->year_built ?? '') }}">
                                                @error('year_built')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Direct From Owner</label>
                                                <input type="text" name="direct_from_owner" class="form-control @error('direct_from_owner') is-invalid @enderror" value="{{ old('direct_from_owner', $details?->direct_from_owner ?? '') }}" placeholder="Example: Yes, Owner Listed, Exclusive Owner Unit">
                                                @error('direct_from_owner')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                                @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Longitude</label>
                                                <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $item->longitude ?? '') }}">
                                                @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade property-content-section-pane" id="property-shared-images" role="tabpanel">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">Images</h6>
                                                <small class="text-muted d-block">Upload many images at once, then manage alt text, order, featured image, and remove items before saving.</small>
                                                <small class="text-muted d-block">Recommended: {{ $propertyImageConfig['width'] ?? 1400 }}x{{ $propertyImageConfig['height'] ?? 900 }}px. Max size: {{ $propertyImageConfig['max_size'] ?? 4096 }} KB.</small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="triggerBulkImagePicker">Add Multiple Images</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="addImageRow">Add Single Row</button>
                                            </div>
                                        </div>
                                        <input type="file" id="bulkImagePicker" class="d-none" accept="image/*" multiple>

                                        @if($item?->images?->count())
                                            <div class="mb-4">
                                                <h6 class="text-muted text-uppercase small mb-3">Existing Images</h6>
                                                @foreach($item->images as $image)
                                                    <div class="row g-3 align-items-center border rounded-3 p-3 mb-3 property-image-existing-row">
                                                        <div class="col-md-2 text-center">
                                                            <img src="{{ asset('storage/' . $image->image) }}" alt="" class="img-fluid rounded border property-image-thumb">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold">Alt Text</label>
                                                            <input type="text" name="existing_images[{{ $image->id }}][alt_text]" class="form-control" value="{{ old("existing_images.{$image->id}.alt_text", $image->alt_text) }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label fw-bold">Order</label>
                                                            <input type="number" min="1" name="existing_images[{{ $image->id }}][order]" class="form-control" value="{{ old("existing_images.{$image->id}.order", $image->order) }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-check mt-md-4 pt-md-2">
                                                                <input class="form-check-input featured-image-toggle" type="checkbox" name="existing_images[{{ $image->id }}][is_featured]" value="1" {{ old("existing_images.{$image->id}.is_featured", $image->is_featured) ? 'checked' : '' }}>
                                                                <label class="form-check-label">Featured</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-check mt-md-4 pt-md-2">
                                                                <input class="form-check-input" type="checkbox" name="existing_images[{{ $image->id }}][delete]" value="1">
                                                                <label class="form-check-label text-danger">Remove</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div id="newImageRows">
                                            @foreach(old('new_images', [['alt_text' => '', 'order' => ($item?->images?->max('order') ?? 0) + 1, 'is_featured' => false]]) as $index => $imageRow)
                                                <div class="row g-3 align-items-center border rounded-3 p-3 mb-3 image-row">
                                                    <div class="col-md-2">
                                                        <div class="image-row-preview-wrap">
                                                            <div class="image-row-preview-placeholder">New</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-bold">Image</label>
                                                        <input type="file" name="new_images[{{ $index }}][file]" class="form-control property-image-file-input" accept="image/*">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-bold">Alt Text</label>
                                                        <input type="text" name="new_images[{{ $index }}][alt_text]" class="form-control" value="{{ $imageRow['alt_text'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label fw-bold">Order</label>
                                                        <input type="number" min="1" name="new_images[{{ $index }}][order]" class="form-control" value="{{ $imageRow['order'] ?? ($index + 1) }}">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-check mt-md-4 pt-md-2 text-center">
                                                            <input class="form-check-input featured-image-toggle" type="checkbox" name="new_images[{{ $index }}][is_featured]" value="1" {{ !empty($imageRow['is_featured']) ? 'checked' : '' }}>
                                                            <label class="form-check-label small d-block mt-1">Featured</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-outline-danger w-100 remove-image-row">X</button>
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
                                                <small class="text-muted d-block">Choose only the schools, hospitals, restaurants, and attractions that belong to this property.</small>
                                                <small class="text-muted d-block">First create the master place in Nearby Places, then select the ones relevant to this listing.</small>
                                            </div>
                                            <a href="{{ route('cms.nearby-places.index') }}" class="btn btn-sm btn-outline-secondary">Manage</a>
                                        </div>
                                        @foreach($placeTypeSidebarLabels as $type => $label)
                                            <div class="border rounded-3 p-3 mb-3 bg-white">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-bold mb-0">{{ $label }}</label>
                                                    <a href="{{ route('cms.nearby-places.create', ['type' => $type]) }}" class="btn btn-sm btn-link text-decoration-none px-0">Add {{ strtolower($label) }}</a>
                                                </div>
                                                @if(($nearbyPlaces[$type] ?? collect())->isEmpty())
                                                    <div class="text-muted small">No {{ strtolower($label) }} added yet. Use the add link first.</div>
                                                @else
                                                    @php
                                                        $selectedTypePlaces = collect($selectedNearbyPlaces->get($type, []))->map(fn ($value) => (string) $value);
                                                    @endphp
                                                    <div class="small text-muted mb-2">Search and choose which {{ strtolower($label) }} are near this property.</div>
                                                    <select name="nearby_places[{{ $type }}][]" class="form-select nearby-places-select2" multiple data-placeholder="Search {{ strtolower($label) }}">
                                                        @foreach(($nearbyPlaces[$type] ?? collect()) as $place)
                                                            @php
                                                                $placeName = $place->getTranslation('name') ?: $place->name;
                                                                $placeAddress = $place->getTranslation('address') ?: $place->address;
                                                            @endphp
                                                            <option value="{{ $place->id }}" @selected($selectedTypePlaces->contains((string) $place->id))>
                                                                {{ $placeName }}@if($placeAddress) - {{ $placeAddress }}@endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
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
                                                @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Agent</label>
                                                <select name="agent_id" class="form-select @error('agent_id') is-invalid @enderror">
                                                    <option value="">Select agent</option>
                                                    @foreach($agents as $agent)
                                                        <option value="{{ $agent->id }}" @selected((string) old('agent_id', $item->agent_id ?? '') === (string) $agent->id)>{{ $agent->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('agent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Order</label>
                                                <input type="number" min="1" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $item->order ?? $nextOrder ?? 1) }}">
                                                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6 d-flex align-items-end">
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="status" id="propertyStatus" value="1" {{ old('status', $item->status ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="propertyStatus">Active</label>
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
        </div>

        <div class="border-top pt-4">
            <button type="submit" class="btn btn-primary px-4">{{ $submitLabel }}</button>
            <a href="{{ route('cms.properties.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>
    </div>
</div>
@push('styles')
<style id="propertyDropdownStyles">
    .property-classification-card,.property-classification-card .card-body,.property-classification-row,.property-classification-row > div { overflow: visible !important; }
    .career-dual-dropdown { position: relative; }
    .career-dual-dropdown-input { cursor: pointer; background-image: linear-gradient(45deg, transparent 50%, #6b7280 50%), linear-gradient(135deg, #6b7280 50%, transparent 50%); background-position: calc(100% - 18px) calc(50% - 3px), calc(100% - 12px) calc(50% - 3px); background-size: 6px 6px, 6px 6px; background-repeat: no-repeat; padding-right: 2.5rem; }
    .career-dual-dropdown-menu { position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 1055; display: none; max-height: 260px; overflow-y: auto; background: #fff; border: 1px solid #dfe5ec; border-radius: 12px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12); }
    .career-dual-dropdown.open .career-dual-dropdown-menu { display: block; }
    .career-dual-dropdown-option { width: 100%; padding: 0.85rem 1rem; display: flex; justify-content: space-between; gap: 1rem; border: 0; background: transparent; text-align: left; }
    .career-dual-dropdown-option + .career-dual-dropdown-option { border-top: 1px solid #edf1f5; }
    .career-dual-dropdown-option:hover,.career-dual-dropdown-option.active { background: #f8fafc; }
    .career-dual-dropdown-primary { color: #1f2937; font-weight: 500; }
    .career-dual-dropdown-secondary { color: #6b7280; font-size: 0.875rem; white-space: nowrap; }
    .career-dual-dropdown-empty { padding: 0.9rem 1rem; color: #6b7280; font-size: 0.9rem; }
    .property-main-tabs-card { border-radius: 24px; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06); }
    .property-top-tabs { gap: 0.75rem; }
    .property-top-tabs .nav-link { border-radius: 14px; color: #475569; }
    .property-top-tabs .nav-link.active { background: linear-gradient(135deg, #0d6efd, #3b82f6); box-shadow: 0 12px 25px rgba(13, 110, 253, 0.18); }
    .property-language-tab-layout { align-items: flex-start; }
    .property-section-tabs { gap: 0.5rem; position: sticky; top: 1rem; }
    .property-section-tabs .nav-link { text-align: left; border-radius: 14px; padding: 0.85rem 1rem; background: #f8fafc; color: #475569; font-weight: 600; border: 1px solid #e2e8f0; }
    .property-section-tabs .nav-link.active { background: linear-gradient(135deg, #0d6efd, #3b82f6); color: #fff; border-color: transparent; box-shadow: 0 12px 25px rgba(13, 110, 253, 0.2); }
    .property-section-tab-content .tab-pane > .card { box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04); border-radius: 18px; }
    .property-image-thumb { max-height: 92px; object-fit: cover; }
    .image-row-preview-wrap { height: 90px; border: 1px dashed #cfd7e3; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: #f8fafc; overflow: hidden; }
    .image-row-preview { width: 100%; height: 100%; object-fit: cover; }
    .image-row-preview-placeholder { font-size: 0.82rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
    .select2-container--default .select2-selection--multiple { min-height: 46px; border: 1px solid #dfe5ec; border-radius: 12px; padding: 0.35rem 0.5rem; }
    .select2-container--default.select2-container--focus .select2-selection--multiple { border-color: #86b7fe; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15); }
    .select2-container--default .select2-selection--multiple .select2-selection__choice { background: rgba(13, 110, 253, 0.08); border: 1px solid rgba(13, 110, 253, 0.18); border-radius: 999px; padding: 0.2rem 0.55rem; color: #1f2937; }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable { background: #0d6efd; }
    .select2-dropdown { border: 1px solid #dfe5ec; border-radius: 12px; overflow: hidden; }
</style>
@endpush

@push('scripts')
<script>
    const propertyClassificationMasterIds = { property_type: 'propertyTypeMaster', listing_type: 'listingTypeMaster', source_type: 'sourceTypeMaster' };
    tinymce.init({ selector: '.tinymce-editor', height: 320, plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount', toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help' });

    function updatePropertyDropdownState(dropdown, value) {
        const input = dropdown.querySelector('.career-dual-dropdown-input');
        const selectedOption = dropdown.querySelector(`.career-dual-dropdown-option[data-value="${CSS.escape(value)}"]`);
        dropdown.querySelectorAll('.career-dual-dropdown-option').forEach((option) => option.classList.toggle('active', option.dataset.value === value));
        input.value = selectedOption ? (selectedOption.dataset.label || '') : '';
    }
    function syncPropertyClassification(field, value, source) {
        const masterInput = document.getElementById(propertyClassificationMasterIds[field] || '');
        if (masterInput) masterInput.value = value;
        document.querySelectorAll(`.property-classification-proxy[data-master-field="${field}"]`).forEach((element) => { if (element !== source) updatePropertyDropdownState(element, value); });
    }
    document.querySelectorAll('.property-classification-proxy').forEach((element) => {
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
                syncPropertyClassification(field, this.dataset.value, element);
                updatePropertyDropdownState(element, this.dataset.value);
                element.classList.remove('open');
            });
        });
    });
    ['property_type', 'listing_type', 'source_type'].forEach((field) => {
        const masterInput = document.getElementById(propertyClassificationMasterIds[field] || '');
        if (masterInput) syncPropertyClassification(field, masterInput.value, null);
    });
    document.addEventListener('click', function(e) {
        document.querySelectorAll('.career-dual-dropdown').forEach((dropdown) => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
                const field = dropdown.dataset.masterField;
                const masterInput = document.getElementById(propertyClassificationMasterIds[field] || '');
                updatePropertyDropdownState(dropdown, masterInput ? masterInput.value : '');
            }
        });
    });
    function escapeRegExp(value) {
        return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    function updateTranslatedAddressPreview(lang) {
        const values = Array.from(document.querySelectorAll(`.translated-address-part[data-lang="${lang}"]`)).map((input) => input.value.trim()).filter((value) => value !== '');
        const preview = document.querySelector(`.translated-full-address-preview[data-lang="${lang}"]`);
        if (preview) preview.value = values.join(', ');
    }
    document.querySelectorAll('.translated-address-part').forEach((input) => {
        input.addEventListener('input', function() {
            updateTranslatedAddressPreview(this.dataset.lang);
        });
    });
    document.querySelectorAll('.repeater-card').forEach((card) => {
        const name = card.dataset.repeater;
        const rows = card.querySelector('.repeater-rows');
        const pattern = new RegExp(`${escapeRegExp(name)}\\[[0-9]+\\]`);
        const reindexRows = () => rows.querySelectorAll('.repeater-row').forEach((row, index) => row.querySelectorAll('input, textarea').forEach((input) => input.name = input.name.replace(pattern, `${name}[${index}]`)));
        card.querySelector('.repeater-add').addEventListener('click', () => {
            const clone = rows.querySelector('.repeater-row').cloneNode(true);
            clone.querySelectorAll('input, textarea').forEach((input) => input.value = '');
            rows.appendChild(clone);
            reindexRows();
        });
        rows.addEventListener('click', (event) => {
            if (!event.target.classList.contains('repeater-remove')) return;
            if (rows.querySelectorAll('.repeater-row').length === 1) {
                event.target.closest('.repeater-row').querySelectorAll('input, textarea').forEach((input) => input.value = '');
                return;
            }
            event.target.closest('.repeater-row').remove();
            reindexRows();
        });
    });
    const imageRows = document.getElementById('newImageRows');
    const bulkImagePicker = document.getElementById('bulkImagePicker');
    function nextImageIndex() {
        return imageRows.querySelectorAll('.image-row').length;
    }
    function nextImageOrder() {
        const orderInputs = Array.from(document.querySelectorAll('input[name^="existing_images["][name$="[order]"], input[name^="new_images["][name$="[order]"]'));
        const maxOrder = orderInputs.reduce((max, input) => Math.max(max, parseInt(input.value || '0', 10) || 0), 0);
        return maxOrder + 1;
    }
    function setPreview(row, file) {
        const previewWrap = row.querySelector('.image-row-preview-wrap');
        if (!previewWrap) return;
        if (file) {
            const preview = document.createElement('img');
            preview.className = 'image-row-preview';
            preview.alt = file.name || 'Image preview';
            preview.src = URL.createObjectURL(file);
            preview.onload = () => URL.revokeObjectURL(preview.src);
            previewWrap.innerHTML = '';
            previewWrap.appendChild(preview);
        } else {
            previewWrap.innerHTML = '<div class="image-row-preview-placeholder">New</div>';
        }
    }
    function buildImageRow(index, order) {
        const wrapper = document.createElement('div');
        wrapper.className = 'row g-3 align-items-center border rounded-3 p-3 mb-3 image-row';
        wrapper.innerHTML = `<div class="col-md-2"><div class="image-row-preview-wrap"><div class="image-row-preview-placeholder">New</div></div></div><div class="col-md-3"><label class="form-label fw-bold">Image</label><input type="file" name="new_images[${index}][file]" class="form-control property-image-file-input" accept="image/*"></div><div class="col-md-3"><label class="form-label fw-bold">Alt Text</label><input type="text" name="new_images[${index}][alt_text]" class="form-control"></div><div class="col-md-2"><label class="form-label fw-bold">Order</label><input type="number" min="1" name="new_images[${index}][order]" class="form-control" value="${order}"></div><div class="col-md-1"><div class="form-check mt-md-4 pt-md-2 text-center"><input class="form-check-input featured-image-toggle" type="checkbox" name="new_images[${index}][is_featured]" value="1"><label class="form-check-label small d-block mt-1">Featured</label></div></div><div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100 remove-image-row">X</button></div>`;
        bindFeaturedToggle(wrapper);
        bindImagePreview(wrapper);
        imageRows.appendChild(wrapper);
        return wrapper;
    }
    function bindImagePreview(scope) {
        scope.querySelectorAll('.property-image-file-input').forEach((input) => {
            input.addEventListener('change', function() {
                setPreview(this.closest('.image-row'), this.files && this.files[0] ? this.files[0] : null);
            });
        });
    }
    document.getElementById('addImageRow').addEventListener('click', () => {
        buildImageRow(nextImageIndex(), nextImageOrder());
    });
    document.getElementById('triggerBulkImagePicker').addEventListener('click', () => bulkImagePicker.click());
    bulkImagePicker.addEventListener('change', function() {
        const files = Array.from(this.files || []);
        files.forEach((file) => {
            const row = buildImageRow(nextImageIndex(), nextImageOrder());
            const input = row.querySelector('.property-image-file-input');
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;
            setPreview(row, file);
        });
        this.value = '';
    });
    function bindFeaturedToggle(scope) { scope.querySelectorAll('.featured-image-toggle').forEach((toggle) => toggle.addEventListener('change', function () { if (!this.checked) return; document.querySelectorAll('.featured-image-toggle').forEach((item) => { if (item !== this) item.checked = false; }); })); }
    imageRows.addEventListener('click', (event) => { if (!event.target.classList.contains('remove-image-row')) return; const allRows = imageRows.querySelectorAll('.image-row'); if (allRows.length === 1) { const row = allRows[0]; row.querySelectorAll('input').forEach((input) => { if (input.type === 'file') { input.value = ''; } else { input.value = ''; input.checked = false; } }); setPreview(row, null); return; } event.target.closest('.image-row').remove(); });
    bindFeaturedToggle(document); bindImagePreview(document); document.querySelectorAll('.translated-full-address-preview').forEach((preview) => updateTranslatedAddressPreview(preview.dataset.lang));
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.nearby-places-select2').each(function() {
            const placeholder = this.dataset.placeholder || 'Search places';
            jQuery(this).select2({
                width: '100%',
                placeholder,
                allowClear: true,
                closeOnSelect: false
            });
        });
    }
    function activateAncestorTabs(element) {
        let pane = element.closest('.tab-pane');
        while (pane) {
            const trigger = document.querySelector(`[data-bs-target="#${pane.id}"]`);
            if (trigger && !trigger.classList.contains('active')) {
                bootstrap.Tab.getOrCreateInstance(trigger).show();
            }
            pane = pane.parentElement ? pane.parentElement.closest('.tab-pane') : null;
        }
    }
    document.addEventListener('invalid', function(e) {
        activateAncestorTabs(e.target);
        setTimeout(() => { e.target.focus(); }, 150);
    }, true);
    const firstPropertyInvalidField = document.querySelector('.tab-pane .is-invalid');
    if (firstPropertyInvalidField) {
        activateAncestorTabs(firstPropertyInvalidField);
    }
</script>
@endpush






