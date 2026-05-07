<template>
    <section v-if="articles.length" class="news commonPadding" style="background: #F7F9FB;">
        <div class="container-center">
            <div class="head text-center">
                <span>{{ title }}</span>
                <h2>{{ eyebrow }}</h2>
            </div>

            <div class="news-slider-wrap position-relative">
                <div class="news-card-slider">
                    <article v-for="item in articles" :key="item.id" class="news-card">
                        <div class="news-card__img">
                            <picture>
                                <img
                                    :src="item.image || dummyImage"
                                    :alt="item.title || 'News Image'"
                                    width="247"
                                    height="453"
                                    class="img-fluid w-100"
                                    style="object-fit: cover;"
                                    @error="onImageError"
                                >
                            </picture>
                        </div>
                        <div class="news-card__content">
                            <div class="news-card__meta d-flex flex-wrap align-items-center">
                                <span class="author d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    {{ item.author }}
                                </span>
                                <span class="date d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    {{ formatDate(item.publishedAt) }}
                                </span>
                            </div>
                            <h3 class="news-card__title">{{ item.title }}</h3>
                            <p class="news-card__desc">{{ item.excerpt }}</p>
                            <a :href="item.url" class="theme-btn d-inline-flex align-items-center mt-auto">
                                <span>{{ t('common.learnMore') }}</span>
                                <i>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                                        <path d="M7.54255 15.0849L15.085 7.54241M15.085 7.54241H9.42817M15.085 7.54241V13.1993" stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </i>
                            </a>
                        </div>
                    </article>
                </div>
                <div class="property-slider-controls w-100 m-0 mt-5 mx-0 d-flex justify-content-between align-items-center" style="max-width: 100%;">
                    <div class="property-slider__progress mb-0" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="40">
                        <span class="property-slider__progress-fill" style="width: 40%;"></span>
                    </div>
                    <div class="property-slider__arrows ml-4 pl-4 flex-shrink-0">
                        <button type="button" class="property-slider__arrow property-slider__arrow--prev bg-transparent border-0" aria-label="Previous news">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 12H4M4 12L10 6M4 12L10 18"></path>
                            </svg>
                        </button>
                        <button type="button" class="property-slider__arrow property-slider__arrow--next bg-transparent border-0 ml-2" aria-label="Next news">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2A559C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 12L20 12M20 12L14 18M20 12L14 6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { asset } from '@/utils/asset';
import { getNewsInsightsData } from '@/utils/publicContent';

const { locale, t } = useI18n({ useScope: 'global' });

const newsData = computed(() => getNewsInsightsData(locale.value));
const eyebrow = computed(() => newsData.value.eyebrow);
const title = computed(() => newsData.value.title);
const articles = computed(() => newsData.value.items);
const dummyImage = asset('public/images/news/blog-placeholder-new.png');

function formatDate(raw) {
    if (!raw) return '';
    const date = new Date(raw);
    if (Number.isNaN(date.getTime())) return '';

    return new Intl.DateTimeFormat(locale.value === 'ar' ? 'ar' : 'en', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
    }).format(date);
}

function onImageError(event) {
    if (!event?.target) return;
    if (event.target.src !== dummyImage) {
        event.target.src = dummyImage;
    }
}
</script>

<style scoped>
@media (min-width: 768px) {
    .news-card__img {
        width: 247px !important;
    }

    .news-card__img picture,
    .news-card__img img {
        height: 453px !important;
        min-height: 453px !important;
    }
}
</style>

