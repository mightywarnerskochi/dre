<script setup>
import { computed, onMounted, onUnmounted, reactive, ref, watch } from 'vue';

const props = defineProps({
    hero: { type: Object, required: true },
    search: { type: Object, required: true },
});

const filterValues = reactive({});
const dynamicFilters = computed(() => props.search?.filters ?? []);
const slides = computed(() => props.hero?.slides ?? []);
const activeSlide = ref(0);
let autoSlideTimer = null;

function fieldIcon(filterKey) {
    if (filterKey === 'location') return 'location';
    if (filterKey === 'property_type') return 'building';
    if (filterKey === 'bedrooms' || filterKey === 'bathrooms' || filterKey === 'bed_and_baths') return 'bed';
    return 'location';
}

const currentSlide = computed(() => slides.value[activeSlide.value] ?? slides.value[0] ?? {});

function goToSlide(index) {
    if (!slides.value.length) return;
    activeSlide.value = index;
}

function nextSlide() {
    if (!slides.value.length) return;
    activeSlide.value = (activeSlide.value + 1) % slides.value.length;
}

function restartAutoSlide() {
    if (autoSlideTimer) {
        window.clearInterval(autoSlideTimer);
        autoSlideTimer = null;
    }

    if (slides.value.length <= 1 || typeof window === 'undefined') {
        return;
    }

    autoSlideTimer = window.setInterval(() => {
        nextSlide();
    }, 5000);
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    dynamicFilters.value.forEach((filter) => {
        const key = String(filter.key || '');
        if (!key) return;
        const queryParam = String(filter.queryParam || '');
        const valueFromUrl = queryParam ? params.get(queryParam) : null;
        filterValues[key] = valueFromUrl !== null ? valueFromUrl : '';
    });

    restartAutoSlide();
});

onUnmounted(() => {
    if (autoSlideTimer) {
        window.clearInterval(autoSlideTimer);
    }
});

watch(slides, () => {
    if (activeSlide.value >= slides.value.length) {
        activeSlide.value = 0;
    }
    restartAutoSlide();
});

function submitSearch() {
    const url = new URL(window.location.href);
    dynamicFilters.value.forEach((filter) => {
        const queryParam = String(filter.queryParam || '');
        if (!queryParam) return;
        url.searchParams.delete(queryParam);
    });
    dynamicFilters.value.forEach((filter) => {
        const key = String(filter.key || '');
        const queryParam = String(filter.queryParam || '');
        if (!key || !queryParam) return;
        const value = String(filterValues[key] ?? '');
        if (value !== '') {
            url.searchParams.set(queryParam, value);
        }
    });
    url.hash = 'rentals';
    window.history.replaceState({}, '', `${url.pathname}${url.search}${url.hash}`);
    document.getElementById('rentals')?.scrollIntoView({ behavior: 'smooth' });
}
</script>

<template>
    <section class="dre-hero">
        <div class="dre-hero__bg">
            <img
                :src="currentSlide.backgroundImage"
                alt=""
                fetchpriority="high"
            />
            <div class="dre-hero__overlay" />
        </div>
        <div class="dre-hero__content dre-container">
            <div class="dre-hero__center">
                <div class="dre-hero__title-wrap">
                    <p class="dre-hero__line1">{{ currentSlide.line1 }}</p>
                    <h1 class="dre-hero__title">{{ currentSlide.line2 }}</h1>
                </div>
                <a
                    v-if="currentSlide.primaryActionLabel"
                    :href="currentSlide.primaryActionUrl || '#rentals'"
                    class="dre-btn dre-btn--primary dre-hero__cta"
                >
                    {{ currentSlide.primaryActionLabel }}
                </a>
                <form class="dre-search-bar" @submit.prevent="submitSearch">
                    <div
                        v-for="filter in dynamicFilters"
                        :key="filter.key"
                        class="dre-search-bar__field"
                    >
                        <label class="dre-sr-only" :for="`dre-${filter.key}`">{{ filter.label }}</label>
                        <span class="dre-search-bar__icon" aria-hidden="true">
                            <svg
                                v-if="fieldIcon(filter.key) === 'location'"
                                class="dre-icon-sm"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"
                                />
                                <circle cx="12" cy="11" r="2.5" stroke-width="1.8" />
                            </svg>
                            <svg
                                v-else-if="fieldIcon(filter.key) === 'building'"
                                class="dre-icon-sm"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 21h16" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21V7h10v14" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 10h.01M14 10h.01M10 14h.01M14 14h.01" />
                            </svg>
                            <svg
                                v-else
                                class="dre-icon-sm"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12h18" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 12V9a2 2 0 114 0v3M13 12V9a2 2 0 114 0v3" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12v6h14v-6" />
                            </svg>
                        </span>
                        <select
                            v-if="filter.uiType === 'dropdown' || (filter.options && filter.options.length)"
                            :id="`dre-${filter.key}`"
                            v-model="filterValues[filter.key]"
                        >
                            <option
                                v-for="opt in filter.options || []"
                                :key="String(opt.value) + opt.label"
                                :value="opt.value"
                            >
                                {{ opt.label }}
                            </option>
                        </select>
                        <input
                            v-else
                            :id="`dre-${filter.key}`"
                            v-model="filterValues[filter.key]"
                            :type="filter.uiType === 'integer' ? 'number' : 'text'"
                            :placeholder="filter.label"
                        >
                        <svg class="dre-search-bar__chevron" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <button type="submit" class="dre-btn dre-btn--primary dre-search-bar__submit">
                        Search
                    </button>
                </form>
            </div>
            <div class="dre-hero__slider-dots" aria-hidden="true">
                <button
                    v-for="(_, index) in slides"
                    :key="index"
                    type="button"
                    class="dre-hero__slider-dot"
                    :class="{ 'is-active': index === activeSlide }"
                    @click="goToSlide(index); restartAutoSlide()"
                />
            </div>
        </div>
    </section>
</template>
