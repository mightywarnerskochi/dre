<template>
    <div>
        <section class="banner banner--page banner--page--listing position-relative" aria-label="Page header">
            <div class="banner--page__bg">
                <picture>
                    <img :src="asset('public/images/inner-banner.jpg')" alt="" width="1920" height="403" loading="eager">
                </picture>
            </div>
            <div class="banner--page__content">
                <div class="container-ctn">
                    <h1 class="banner--page__title">{{ heading }}</h1>
                    <ol class="breadcrumb-minimal" aria-label="Breadcrumb">
                        <li>
                            <RouterLink to="/" aria-label="Home">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </RouterLink>
                        </li>
                        <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                        <li class="breadcrumb-minimal__current" aria-current="page">{{ heading }}</li>
                    </ol>
                </div>
            </div>
        </section>

        <section v-if="insightsItems.length" class="insights-section commonPadding-120" aria-labelledby="insights-heading">
            <div class="container-ctn">
                <h2 id="insights-heading" class="visually-hidden">Latest articles</h2>
                <ul class="insights-grid">
                    <li v-for="item in insightsItems" :key="item.id">
                        <article class="insight-card">
                            <RouterLink class="insight-card__link" :to="{ name: 'insights-details', params: { slug: item.slug } }">
                                <div class="insight-card__media">
                                    <div class="insight-card__image-wrap">
                                        <img :src="item.image || dummyImage" alt="" width="247" height="453" loading="lazy" @error="onImgError">
                                    </div>
                                    <time class="insight-card__date" :datetime="item.publishedAt || ''">
                                        <span class="insight-card__date-inner">
                                            <span class="insight-card__date-text">{{ formatDate(item.publishedAt) }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M9.24546 20.7424V3.2494M4.74725 16.6331H2.74805M4.74725 7.38675H2.74805M4.74725 6.74801L4.74725 17.2438C4.74725 18.1717 5.11585 19.0616 5.77197 19.7177C6.42808 20.3738 7.31797 20.7424 8.24586 20.7424H17.7421C18.67 20.7424 19.5598 20.3738 20.216 19.7177C20.8721 19.0616 21.2407 18.1717 21.2407 17.2438V6.74801C21.2407 5.82012 20.8721 4.93023 20.216 4.27412C19.5598 3.618 18.67 3.2494 17.7421 3.2494H8.24586C7.31797 3.2494 6.42808 3.618 5.77197 4.27412C5.11585 4.93023 4.74725 5.82012 4.74725 6.74801Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </time>
                                </div>
                                <div class="insight-card__body">
                                    <h3 class="insight-card__title">{{ item.title }}</h3>
                                    <p class="insight-card__excerpt">{{ item.excerpt }}</p>
                                </div>
                                <span class="insight-card__arrow" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 17L17 7M17 7H9M17 7V15"/>
                                    </svg>
                                </span>
                            </RouterLink>
                        </article>
                    </li>
                </ul>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { asset } from '@/utils/asset';
import { getInsightsListingData } from '@/utils/publicContent';

const { locale } = useI18n({ useScope: 'global' });
const dummyImage = asset('public/images/news/blog-placeholder-new.png');

const data = computed(() => getInsightsListingData(locale.value));
const heading = computed(() => data.value.eyebrow || 'Insights');
const insightsItems = computed(() => data.value.items);

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

function onImgError(event) {
    if (!event?.target) return;
    if (event.target.src !== dummyImage) {
        event.target.src = dummyImage;
    }
}
</script>
