<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import PropertyCard from './PropertyCard.vue';

const props = defineProps({
    section: { type: Object, required: true },
});

const list = computed(() => props.section.properties ?? []);
const n = computed(() => list.value.length);

const perView = ref(1);
const slide = ref(0);

function updatePerView() {
    if (typeof window === 'undefined') return;
    if (window.innerWidth >= 1024) perView.value = 3;
    else if (window.innerWidth >= 640) perView.value = 2;
    else perView.value = 1;
}

function clampSlide() {
    const max = Math.max(0, n.value - perView.value);
    if (slide.value > max) slide.value = max;
}

function onResize() {
    updatePerView();
    clampSlide();
}

onMounted(() => {
    updatePerView();
    window.addEventListener('resize', onResize);
    clampSlide();
});

onUnmounted(() => {
    window.removeEventListener('resize', onResize);
});

watch([n, perView], clampSlide);

const maxSlide = computed(() => Math.max(0, n.value - perView.value));

const trackStyle = computed(() => {
    if (!n.value) return {};
    const trackPct = (n.value * 100) / perView.value;
    const translatePct = (-slide.value * 100) / n.value;
    return {
        width: `${trackPct}%`,
        transform: `translateX(${translatePct}%)`,
    };
});

const slideBasis = computed(() => (n.value ? `${100 / n.value}%` : '100%'));

const progressPct = computed(() => {
    if (n.value <= perView.value) return 100;
    if (maxSlide.value <= 0) return 100;
    return ((slide.value + 1) / (maxSlide.value + 1)) * 100;
});

function prev() {
    slide.value = Math.max(0, slide.value - 1);
}

function next() {
    slide.value = Math.min(maxSlide.value, slide.value + 1);
}
</script>

<template>
    <section id="rentals" class="dre-section dre-section--soft dre-rentals">
        <div class="dre-container">
            <div class="dre-section__head">
                <h2 class="dre-section__title">{{ section.title }}</h2>
                <p v-if="section.description" class="dre-section__desc">
                    {{ section.description }}
                </p>
            </div>
            <div class="dre-carousel">
                <div class="dre-carousel__viewport">
                    <div class="dre-carousel__track" :style="trackStyle">
                        <div
                            v-for="p in list"
                            :key="p.id"
                            class="dre-carousel__slide"
                            :style="{ flex: `0 0 ${slideBasis}` }"
                        >
                            <PropertyCard :property="p" />
                        </div>
                    </div>
                </div>
                <div v-if="n > perView" class="dre-carousel__nav">
                    <button type="button" class="dre-carousel__arrow" aria-label="Previous" @click="prev">
                        <svg class="dre-icon-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </button>
                    <button type="button" class="dre-carousel__arrow" aria-label="Next" @click="next">
                        <svg class="dre-icon-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>
                </div>
                <div class="dre-carousel__nav" style="margin-top: 0.75rem">
                    <div class="dre-carousel__progress" aria-hidden="true">
                        <span class="dre-carousel__progress-fill" :style="{ width: progressPct + '%' }" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
