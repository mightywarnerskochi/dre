<template>
    <section class="banner position-relative">
        <div class="banner-slider">
            <div class="banner-slide" v-for="slide in slides" :key="slide.id">
                <picture>
                    <img :src="slide.backgroundImage" :alt="slideAltText(slide)">
                </picture>
            </div>
        </div>

        <div class="banner-content">
            <div class="container-ctn">
                <div class="banner-content-inner">
                    <h1>
                        <span>{{ localizedSlideText(currentSlide.line1) }}</span>
                        {{ localizedSlideText(currentSlide.line2) }}
                    </h1>
                    <a
                        v-if="currentSlideActionIsExternal"
                        :href="currentSlideActionTarget"
                        class="banner-btn"
                    >
                        {{ localizedSlideText(currentSlide.primaryActionLabel) }}
                    </a>
                    <RouterLink v-else :to="currentSlideActionTarget" class="banner-btn">{{ localizedSlideText(currentSlide.primaryActionLabel) }}</RouterLink>

                    <div class="banner-search">
                        <form @submit.prevent="onSubmit" class="search-form" dir="ltr">
                            <div class="banner-search__pill">
                                <div class="search-field search-field--location position-relative">
                                    <svg class="search-field__lead-icon" xmlns="http://www.w3.org/2000/svg" width="18"
                                        height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                        <path
                                            d="M9 1.5C5.7 1.5 3 4.2 3 7.5C3 11.55 8.25 16.125 8.475 16.35C8.625 16.425 8.85 16.5 9 16.5C9.15 16.5 9.375 16.425 9.525 16.35C9.75 16.125 15 11.55 15 7.5C15 4.2 12.3 1.5 9 1.5ZM9 14.775C7.425 13.275 4.5 10.05 4.5 7.5C4.5 5.025 6.525 3 9 3C11.475 3 13.5 5.025 13.5 7.5C13.5 9.975 10.575 13.275 9 14.775ZM9 4.5C7.35 4.5 6 5.85 6 7.5C6 9.15 7.35 10.5 9 10.5C10.65 10.5 12 9.15 12 7.5C12 5.85 10.65 4.5 9 4.5ZM9 9C8.175 9 7.5 8.325 7.5 7.5C7.5 6.675 8.175 6 9 6C9.825 6 10.5 6.675 10.5 7.5C10.5 8.325 9.825 9 9 9Z"
                                            fill="#4B5C77" />
                                    </svg>
                                    <input
                                        type="text"
                                        name="location"
                                        id="locationInput"
                                        v-model="formState.location"
                                        :placeholder="locationFilterLabel"
                                        :dir="locale === 'ar' ? 'rtl' : 'ltr'"
                                        autocomplete="off"
                                        @focus="locationDropdownOpen = true"
                                        @blur="closeLocationDropdown"
                                        @input="locationDropdownOpen = true"
                                    >
                                    <div v-if="locationDropdownOpen && locationSuggestions.length > 0" class="search-results-dropdown show" id="searchResults">
                                        <div class="search-results-title">{{ t('listing.popularLocations') }}</div>
                                        <div class="search-results-list custom-scrollbar">
                                            <button
                                                v-for="suggestion in locationSuggestions"
                                                :key="suggestion.value"
                                                type="button"
                                                class="search-results-item"
                                                @mousedown.prevent="selectLocation(suggestion.value)"
                                            >
                                                <div class="search-results-icon">
                                                    <svg class="search-field__lead-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"> <path d="M9 1.5C5.7 1.5 3 4.2 3 7.5C3 11.55 8.25 16.125 8.475 16.35C8.625 16.425 8.85 16.5 9 16.5C9.15 16.5 9.375 16.425 9.525 16.35C9.75 16.125 15 11.55 15 7.5C15 4.2 12.3 1.5 9 1.5ZM9 14.775C7.425 13.275 4.5 10.05 4.5 7.5C4.5 5.025 6.525 3 9 3C11.475 3 13.5 5.025 13.5 7.5C13.5 9.975 10.575 13.275 9 14.775ZM9 4.5C7.35 4.5 6 5.85 6 7.5C6 9.15 7.35 10.5 9 10.5C10.65 10.5 12 9.15 12 7.5C12 5.85 10.65 4.5 9 4.5ZM9 9C8.175 9 7.5 8.325 7.5 7.5C7.5 6.675 8.175 6 9 6C9.825 6 10.5 6.675 10.5 7.5C10.5 8.325 9.825 9 9 9Z" fill="#4B5C77"></path> </svg>
                                                </div>
                                                <div class="search-results-content">
                                                    <div class="search-results-name">{{ locationOptionName(suggestion) }}</div>
                                                    <div v-if="locationOptionSubtitle(suggestion)" class="search-results-city">{{ locationOptionSubtitle(suggestion) }}</div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="search-btn search-btn--hero" :aria-label="t('listing.searchAria')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 15 15"
                                        fill="none" aria-hidden="true">
                                        <path
                                            d="M14.7982 13.7332L12.0157 10.9732C13.0957 9.62648 13.6188 7.91715 13.4773 6.19665C13.3358 4.47615 12.5404 2.87526 11.2548 1.72316C9.9692 0.571055 8.29103 -0.0446921 6.56537 0.00252822C4.8397 0.0497485 3.19772 0.756347 1.97703 1.97703C0.756347 3.19772 0.0497485 4.8397 0.00252822 6.56537C-0.0446921 8.29103 0.571055 9.9692 1.72316 11.2548C2.87526 12.5404 4.47615 13.3358 6.19665 13.4773C7.91715 13.6188 9.62648 13.0957 10.9732 12.0157L13.7332 14.7757C13.8029 14.846 13.8858 14.9018 13.9772 14.9398C14.0686 14.9779 14.1667 14.9975 14.2657 14.9975C14.3647 14.9975 14.4627 14.9779 14.5541 14.9398C14.6455 14.9018 14.7285 14.846 14.7982 14.7757C14.9334 14.6358 15.0089 14.4489 15.0089 14.2544C15.0089 14.0599 14.9334 13.873 14.7982 13.7332ZM6.76568 12.0157C5.72732 12.0157 4.71229 11.7078 3.84893 11.1309C2.98557 10.554 2.31267 9.73408 1.91531 8.77476C1.51795 7.81545 1.41398 6.75985 1.61655 5.74145C1.81912 4.72305 2.31914 3.78759 3.05336 3.05336C3.78759 2.31914 4.72305 1.81912 5.74145 1.61655C6.75985 1.41398 7.81545 1.51795 8.77476 1.91531C9.73408 2.31267 10.554 2.98557 11.1309 3.84893C11.7078 4.71229 12.0157 5.72732 12.0157 6.76568C12.0157 8.15806 11.4626 9.49342 10.478 10.478C9.49342 11.4626 8.15806 12.0157 6.76568 12.0157Z"
                                            fill="white" />
                                        </svg>
                                    <span class="search-btn__text d-none d-lg-inline">{{ t('listing.search') }}</span>
                                </button>
                            </div>

                            <div class="search-field dropdown-field dropdown search-field--filter" id="propertyTypeField">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                    <path d="M11.25 16.5H2.25V3.75C2.25 1.8885 2.6385 1.5 4.5 1.5H9C10.8615 1.5 11.25 1.8885 11.25 3.75V16.5ZM11.25 16.5V6H13.5C15.3615 6 15.75 6.3885 15.75 8.25V16.5H11.25Z" stroke="#4B5C77" stroke-width="1.125" stroke-linejoin="round" />
                                    <path d="M6 4.5H7.5M6 6.75H7.5M6 9H7.5" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8.625 16.5V13.5C8.625 12.7927 8.625 12.4395 8.40525 12.2197C8.1855 12 7.83225 12 7.125 12H6.375C5.66775 12 5.3145 12 5.09475 12.2197C4.875 12.4395 4.875 12.7927 4.875 13.5V16.5" stroke="#4B5C77" stroke-width="1.125" stroke-linejoin="round" />
                                </svg>
                                <button type="button" class="dropdown-trigger bg-transparent border-0 w-100"
                                    :class="locale === 'ar' ? 'text-end' : 'text-start'"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <span class="selected-text">{{ selectedPropertyTypeLabel }}</span>
                                </button>
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                                </svg>
                                <div class="dropdown-menu property-dropdown border-0 custom-dropdown-menu">
                                    <div class="dropdown-section">
                                        <div class="dropdown-section-title">{{ propertyTypeFilterDisplayLabel }}</div>
                                        <div class="option-buttons" data-type="property" :class="{ 'property-options--collapsed': !propertyTypesExpanded }">
                                            <button v-for="(opt, idx) in propertyTypeOptions" :key="opt.value" type="button" 
                                                    :class="{ 
                                                        'property-option--extra': idx >= 5 && !propertyTypesExpanded,
                                                        active: formState.property_type === opt.value 
                                                    }"
                                                    @click="formState.property_type = opt.value">
                                                {{ propertyTypeOptionLabel(opt) }}
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="propertyTypeOptions.length > 5" class="dropdown-footer">
                                        <button v-if="propertyTypeOptions.length > 5" type="button" class="view-more" @click.stop="propertyTypesExpanded = !propertyTypesExpanded">
                                            {{ propertyTypesExpanded ? (locale === 'ar' ? 'عرض أقل' : 'View less') : (locale === 'ar' ? 'عرض المزيد' : 'View more') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="search-field dropdown search-field--filter" id="bedsBathsField">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                    <path d="M1.6875 14.625V10.6875C1.68926 10.0913 1.92688 9.52003 2.34846 9.09846C2.77003 8.67688 3.3413 8.43926 3.9375 8.4375H14.0625C14.6587 8.43926 15.23 8.67688 15.6515 9.09846C16.0731 9.52003 16.3107 10.0913 16.3125 10.6875V14.625M13.5 8.4375H3.375V4.78125C3.37611 4.40863 3.52463 4.05159 3.78811 3.78811C4.05159 3.52463 4.40863 3.37611 4.78125 3.375H13.2188C13.5914 3.37611 13.9484 3.52463 14.2119 3.78811C14.4754 4.05159 14.6239 4.40863 14.625 4.78125V8.4375H13.5Z" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M1.6875 14.625V14.3438C1.68815 14.1202 1.77725 13.9059 1.93535 13.7478C2.09344 13.5898 2.30767 13.5006 2.53125 13.5H15.4688C15.6923 13.5006 15.9066 13.5898 16.0647 13.7478C16.2227 13.9059 16.3119 14.1202 16.3125 14.3438V14.625M3.9375 8.4375V7.875C3.93833 7.57689 4.05713 7.29122 4.26793 7.08043C4.47872 6.86963 4.76439 6.75083 5.0625 6.75H7.875C8.17311 6.75083 8.45878 6.86963 8.66957 7.08043C8.88037 7.29122 8.99917 7.57689 9 7.875M9 7.875V8.4375M9 7.875C9.00083 7.57689 9.11963 7.29122 9.33043 7.08043C9.54122 6.86963 9.82689 6.75083 10.125 6.75H12.9375C13.2356 6.75083 13.5213 6.86963 13.7321 7.08043C13.9429 7.29122 14.0617 7.57689 14.0625 7.875V8.4375" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <button type="button" class="dropdown-trigger bg-transparent border-0 w-100"
                                    :class="locale === 'ar' ? 'text-end' : 'text-start'"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <span class="selected-text">{{ selectedBedsBathsLabel }}</span>
                                </button>
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                                </svg>
                                <div class="dropdown-menu beds-baths-dropdown border-0 custom-dropdown-menu">
                                    <div class="dropdown-section">
                                        <div class="dropdown-section-title">{{ locale === 'ar' ? 'غرف النوم' : 'Bedrooms' }}</div>
                                        <div class="option-buttons" data-type="bedrooms">
                                            <label v-for="bed in bedOptions" :key="'bed-'+bed" class="custom-check-btn">
                                                <input type="radio" name="bedrooms" class="btn-check bed-check" :value="bed" v-model="formState.bedrooms">
                                                {{ bed }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="dropdown-section">
                                        <div class="dropdown-section-title">{{ locale === 'ar' ? 'الحمامات' : 'Bathrooms' }}</div>
                                        <div class="option-buttons" data-type="bathrooms">
                                            <label v-for="bath in bathOptions" :key="'bath-'+bath" class="custom-check-btn">
                                                <input type="radio" name="bathrooms" class="btn-check bath-check" :value="bath" v-model="formState.bathrooms">
                                                {{ bath }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="dropdown-footer">
                                        <button type="button" class="clear-filters" @click="clearBedsBaths">{{ locale === 'ar' ? 'مسح التصفية' : 'Clear Filter' }}</button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { asset } from '@/utils/asset';

const { locale, t } = useI18n({ useScope: 'global' });
const router = useRouter();

const propertyTypesExpanded = ref(false);

const content = typeof window !== 'undefined' ? window.__DRE_CONTENT__ : {};
const slides = computed(() => {
    const arr = content?.hero?.slides || [];
    if (arr.length > 0) return arr;
    return [{
        id: 0,
        line1: { en: 'Discover Your', ar: 'اكتشف' },
        line2: { en: 'Perfect Living Spot', ar: 'مكانك المثالي للعيش' },
        backgroundImage: asset('public/images/hero-dubai.jpg'),
        primaryActionLabel: { en: 'Explore Rentals', ar: 'استكشف الإيجارات' },
        primaryActionUrl: '#rentals'
    }];
});
const activeSlideIndex = ref(0);
const bannerSliderRetryTimer = ref(null);
const currentSlide = computed(() => slides.value[activeSlideIndex.value] || slides.value[0]);
const currentSlideActionUrl = computed(() => {
    const url = currentSlide.value?.primaryActionUrl;
    return typeof url === 'string' ? url.trim() : '';
});
const currentSlideActionIsExternal = computed(() => /^https?:\/\//i.test(currentSlideActionUrl.value));
const currentSlideActionTarget = computed(() => {
    const url = currentSlideActionUrl.value;

    if (currentSlideActionIsExternal.value) {
        return url;
    }

    if (url.startsWith('/')) {
        return url;
    }

    return { name: 'our-property' };
});

function localizedSlideText(value, fallback = '') {
    if (value && typeof value === 'object') {
        return value[locale.value] || value.en || Object.values(value).find(Boolean) || fallback;
    }

    return value || fallback;
}

function slideAltText(slide) {
    return [localizedSlideText(slide?.line1), localizedSlideText(slide?.line2)]
        .filter(Boolean)
        .join(' ');
}

function syncActiveSlideIndex(index) {
    const total = slides.value.length;

    if (!total) {
        activeSlideIndex.value = 0;
        return;
    }

    const normalized = Number.parseInt(index, 10);
    activeSlideIndex.value = Number.isFinite(normalized) ? Math.max(0, Math.min(normalized, total - 1)) : 0;
}

function bindBannerSliderContent(attempt = 0) {
    if (typeof window === 'undefined' || !window.jQuery) {
        if (attempt < 20) {
            bannerSliderRetryTimer.value = window.setTimeout(() => bindBannerSliderContent(attempt + 1), 150);
        }
        return;
    }

    const $slider = window.jQuery('.banner-slider');

    if (!$slider.length) {
        return;
    }

    $slider
        .off('init.dreVueContent afterChange.dreVueContent')
        .on('init.dreVueContent afterChange.dreVueContent', (_event, slick, currentSlideIndex) => {
            syncActiveSlideIndex(currentSlideIndex ?? slick?.currentSlide ?? 0);
        });

    if ($slider.hasClass('slick-initialized')) {
        try {
            syncActiveSlideIndex($slider.slick('slickCurrentSlide'));
        } catch (_error) {
            syncActiveSlideIndex(0);
        }
    } else if (attempt < 20) {
        bannerSliderRetryTimer.value = window.setTimeout(() => bindBannerSliderContent(attempt + 1), 150);
    }
}

onMounted(() => {
    nextTick(() => bindBannerSliderContent());
    loadDynamicLocationOptions();
});

onBeforeUnmount(() => {
    if (bannerSliderRetryTimer.value) {
        window.clearTimeout(bannerSliderRetryTimer.value);
    }

    if (typeof window !== 'undefined' && window.jQuery) {
        window.jQuery('.banner-slider').off('init.dreVueContent afterChange.dreVueContent');
    }
});

const filters = computed(() => content?.search?.filters || []);

// Setup dynamic options for property type from CMS
const propertyTypeFilter = computed(() => filters.value.find(f => f.key === 'property_type'));
const rawPropertyTypeOptions = computed(() => propertyTypeFilter.value?.options || [
    { value: '', label: { en: 'Property type', ar: 'نوع العقار' } },
    { value: 'apartment', label: { en: 'Apartment', ar: 'شقة' } },
    { value: 'villa', label: { en: 'Villa', ar: 'فيلا' } },
    { value: 'townhouse', label: { en: 'Townhouse', ar: 'تاون هاوس' } }
]);

function localizedLabel(raw, fallback = '') {
    if (raw && typeof raw === 'object') {
        const localized = raw[locale.value] || raw.en || Object.values(raw).find(Boolean) || fallback;
        return localized && typeof localized === 'object'
            ? String(localized.label || fallback)
            : String(localized || fallback);
    }

    return String(raw || fallback);
}

const propertyTypeFallbackLabels = {
    '': { en: 'All Type', ar: '\u0643\u0644 \u0627\u0644\u0623\u0646\u0648\u0627\u0639' },
    apartment: { en: 'Apartment', ar: '\u0634\u0642\u0629' },
    villa: { en: 'Villa', ar: '\u0641\u064a\u0644\u0627' },
    townhouse: { en: 'Townhouse', ar: '\u0645\u0646\u0632\u0644 \u062a\u0627\u0648\u0646' },
    penthouse: { en: 'Penthouse', ar: '\u0628\u064a\u062a \u0639\u0644\u0649 \u0627\u0644\u0637\u0627\u0628\u0642 \u0627\u0644\u0639\u0644\u0648\u064a' },
    compound: { en: 'Compound', ar: '\u0645\u062c\u0645\u0639' },
    duplex: { en: 'Duplex', ar: '\u062f\u0648\u0628\u0644\u0643\u0633' },
    'full floor': { en: 'Full Floor', ar: '\u0637\u0627\u0628\u0642 \u0643\u0627\u0645\u0644' },
    'half floor': { en: 'Half Floor', ar: '\u0646\u0635\u0641 \u0637\u0627\u0628\u0642' },
    'whole building': { en: 'Whole Building', ar: '\u0645\u0628\u0646\u0649 \u0643\u0627\u0645\u0644' },
    'bulk rent unit': { en: 'Bulk Rent Unit', ar: '\u0648\u062d\u062f\u0629 \u0625\u064a\u062c\u0627\u0631 \u0628\u0627\u0644\u062c\u0645\u0644\u0629' },
    bungalow: { en: 'Bungalow', ar: '\u0628\u0646\u063a\u0644' },
    'hotel & hotel apartment': { en: 'Hotel & Hotel Apartment', ar: '\u0641\u0646\u062f\u0642 \u0648\u0634\u0642\u0642 \u0641\u0646\u062f\u0642\u064a\u0629' },
    office: { en: 'Office', ar: '\u0645\u0643\u062a\u0628' },
    warehouse: { en: 'Warehouse', ar: '\u0645\u0633\u062a\u0648\u062f\u0639' },
};

function propertyTypeOptionLabel(option) {
    const raw = option?.label;
    if (raw && typeof raw === 'object') {
        return localizedLabel(raw, option?.value || '');
    }

    const key = String(option?.value ?? '').toLowerCase();
    const fallback = propertyTypeFallbackLabels[key]?.[locale.value];
    return fallback || localizedLabel(raw, option?.value || '');
}

function sortPropertyTypeOptions(options) {
    return [...options].sort((a, b) => {
        const aEmpty = String(a?.value ?? '').trim() === '';
        const bEmpty = String(b?.value ?? '').trim() === '';

        if (aEmpty !== bEmpty) {
            return aEmpty ? -1 : 1;
        }

        return propertyTypeOptionLabel(a).localeCompare(propertyTypeOptionLabel(b), locale.value || undefined, {
            sensitivity: 'base',
            numeric: true,
        });
    });
}

const propertyTypeOptions = computed(() => sortPropertyTypeOptions(
    rawPropertyTypeOptions.value.filter((option) => String(option?.value ?? '').trim() !== '')
));

const propertyTypeFilterDisplayLabel = computed(() => {
    const raw = propertyTypeFilter.value?.label;
    if (raw && typeof raw === 'object') {
        return localizedLabel(raw, t('listing.propertyTypeHeading'));
    }

    if (locale.value === 'ar') {
        return t('listing.propertyTypeHeading');
    }

    return localizedLabel(raw, t('listing.propertyTypeHeading'));
});

const locationFilter = computed(() => filters.value.find(f => f.key === 'location'));
const apiLocationOptions = ref([]);
const locationOptions = computed(() => {
    const options = apiLocationOptions.value.length > 0 ? apiLocationOptions.value : (locationFilter.value?.options || []);
    return options.filter(o => o.value !== '');
});
const locationFilterLabel = computed(() => {
    const raw = locationFilter.value?.label;
    if (raw && typeof raw === 'object') {
        return raw[locale.value] || raw.en || t('listing.locationPlaceholder');
    }
    if (typeof raw === 'string' && raw.trim()) {
        if (locale.value === 'ar') {
            return t('listing.locationPlaceholder');
        }
        return raw;
    }
    return t('listing.locationPlaceholder');
});

const locationDropdownOpen = ref(false);
function locationOptionMeta(option) {
    const raw = option?.label;
    if (raw && typeof raw === 'object') {
        const localized = raw[locale.value] || raw.en || raw;
        return localized && typeof localized === 'object' ? localized : { label: localized };
    }

    return {
        label: raw || option?.value || '',
        subtitle: option?.subtitle || option?.city || '',
        type: option?.type || '',
    };
}

function locationOptionName(option) {
    return String(locationOptionMeta(option).label || option?.value || '');
}

function locationOptionSubtitle(option) {
    const meta = locationOptionMeta(option);
    return String(meta.subtitle || meta.city || meta.type || '');
}

const locationSuggestions = computed(() => {
    const search = formState.value.location.trim().toLowerCase();
    if (!search) return locationOptions.value.slice(0, 10);

    return locationOptions.value.filter(opt => {
        const haystack = [
            locationOptionName(opt),
            locationOptionSubtitle(opt),
            opt.value,
        ].join(' ').toLowerCase();

        return haystack.includes(search);
    }).slice(0, 10);
});

function selectLocation(val) {
    const opt = locationOptions.value.find(o => o.value === val);
    if (opt) {
        formState.value.location = locationOptionName(opt);
    } else {
        formState.value.location = val;
    }
    locationDropdownOpen.value = false;
}

function highlightMatch(text, search) {
    if (!search || !text) return text;
    const regex = new RegExp(`(${search})`, 'gi');
    return text.replace(regex, '<strong>$1</strong>');
}

function closeLocationDropdown() {
    locationDropdownOpen.value = false;
}

async function loadDynamicLocationOptions() {
    if (typeof window === 'undefined' || !window.axios) return;

    try {
        const { data } = await window.axios.get('/api/properties/filter-options', {
            params: { lang: locale.value || 'en' },
        });
        apiLocationOptions.value = Array.isArray(data.locations)
            ? data.locations.filter(o => o.value !== '')
            : [];
    } catch (_error) {
        apiLocationOptions.value = [];
    }
}

// Static options for Beds and Baths
const bedOptions = ['1', '2', '3', '4', '5', '6', '7', '7+'];
const bathOptions = ['1', '2', '3', '4', '5', '6', '7', '7+'];

const formState = ref({
    location: '',
    property_type: '',
    bedrooms: '',
    bathrooms: ''
});

const selectedPropertyTypeLabel = computed(() => {
    if (formState.value.property_type) {
        const opt = propertyTypeOptions.value.find(o => o.value === formState.value.property_type);
        if (opt) return propertyTypeOptionLabel(opt);
    }
    return t('listing.selectPropertyType');
});

const selectedBedsBathsLabel = computed(() => {
    const beds = formState.value.bedrooms;
    const baths = formState.value.bathrooms;
    
    if (!beds && !baths) {
        return locale.value === 'ar' ? 'حدد الغرف والحمامات' : 'Select Beds & Baths';
    }
    
    let label = [];
    if (beds) label.push(`${beds} ${locale.value === 'ar' ? 'غرف نوم' : 'Bed'}`);
    if (baths) label.push(`${baths} ${locale.value === 'ar' ? 'حمامات' : 'Bath'}`);
    
    return label.join(', ');
});

function clearBedsBaths() {
    formState.value.bedrooms = '';
    formState.value.bathrooms = '';
}

function onSubmit() {
    const query = {};
    if (formState.value.location) {
        query.location = formState.value.location;
    }
    if (formState.value.property_type) {
        query.type = formState.value.property_type;
    }
    if (formState.value.bedrooms) {
        query.beds = formState.value.bedrooms;
    }
    if (formState.value.bathrooms) {
        query.baths = formState.value.bathrooms;
    }
    router.push({ name: 'our-property', query });
}
</script>
