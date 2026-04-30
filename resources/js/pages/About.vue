<template>
    <section class="banner banner--page banner--page--listing position-relative" aria-label="Page header">
        <div class="banner--page__bg">
            <picture>
                <img :src="aboutData.hero.backgroundImage || asset('public/images/inner-banner.jpg')" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">{{ aboutData.hero.title }}</h1>
                <ol class="breadcrumb-minimal" aria-label="Breadcrumb">
                    <li>
                        <RouterLink :to="{ name: 'home' }" aria-label="Home">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </RouterLink>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">{{ aboutData.hero.breadcrumb }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="about-intro commonPadding-pt commonPadding-120-pb" aria-labelledby="about-intro-heading">
        <div class="container-ctn">
            <div class="head text-center mb-0">
                <span class="about-intro__eyebrow">{{ aboutData.intro.title }}</span>
                <h2 id="about-intro-heading" class="about-intro__title">{{ aboutData.intro.eyebrow }}</h2>
                <div v-html="aboutData.intro.bodyHtml"></div>
            </div>
            <div class="about-gallery__track d-flex flex-wrap justify-content-between">
                <figure v-for="(img, index) in aboutData.intro.gallery" :key="img.src + index" class="about-gallery__cell" :class="`about-gallery__cell--h${index + 1}`">
                    <img :src="img.src" :alt="img.alt || ''" width="400" height="520" loading="lazy">
                </figure>
            </div>
        </div>
    </section>

    <section class="about-values" aria-labelledby="about-values-heading">
        <div class="container-ctn">
            <h2 id="about-values-heading" class="visually-hidden">{{ aboutData.missionVision.title }}</h2>
            <div class="about-values__grid d-flex flex-wrap justify-content-between">
                <article v-for="(card, idx) in aboutData.missionVision.items" :key="card.index + card.title" class="about-value-card position-relative">
                    <picture v-if="card.image"><img :src="card.image" :alt="card.title"></picture>
                    <div>
                        <div class="about-value-card__index" aria-hidden="true"></div>
                        <h3 class="about-value-card__title">{{ card.title }}</h3>
                        <p class="about-value-card__text">{{ card.body }}</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section v-if="aboutData.whyChooseUs.active" class="why-choose-us">
        <div class="container-ctn">
            <div class="head text-center">
                <span>{{ aboutData.whyChooseUs.section.title }}</span>
                <h2 class="about-team__title">{{ aboutData.whyChooseUs.section.eyebrow }}</h2>
                <p>{{ aboutData.whyChooseUs.section.intro }}</p>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="why-choose-left d-flex flex-wrap">
                    <div v-for="item in aboutData.whyChooseUs.items" :key="item.index + item.title" class="why-choose-item">
                        <div class="count" aria-hidden="true"></div>
                        <h3>{{ item.title }}</h3>
                        <p>{{ item.body }}</p>
                    </div>
                </div>
                <picture v-if="aboutData.whyChooseUs.section.sectionImage || aboutData.whyChooseUs.section.collage[0]">
                    <img :src="aboutData.whyChooseUs.section.sectionImage || aboutData.whyChooseUs.section.collage[0]" :alt="aboutData.whyChooseUs.section.sectionImageAlt || aboutData.whyChooseUs.section.title" width="671" height="628" loading="lazy">
                </picture>
            </div>
        </div>
    </section>

    <section v-if="aboutData.journey.items.length" class="our-journey" aria-labelledby="journey-heading">
        <div class="container-fluid p-0">
            <h2 id="journey-heading" class="our-journey__title text-center">{{ aboutData.journey.title }}</h2>
            <div class="journey-timeline" role="region" aria-label="Company timeline">
                <ol class="journey-timeline__list">
                    <li v-for="(item, idx) in aboutData.journey.items" :key="item.year + '-' + idx" class="journey-timeline__item">
                        <time class="journey-timeline__year" :datetime="item.year">{{ item.year }}</time>
                        <div class="journey-timeline__axis">
                            <span class="journey-timeline__dot" aria-hidden="true"></span>
                        </div>
                        <div class="journey-timeline__content">
                            <div v-html="item.bodyHtml"></div>
                            <div v-if="item.images.length" class="journey-timeline__thumbs" role="group">
                                <img v-for="(src, imageIdx) in item.images" :key="src + imageIdx" :src="src" :alt="item.year" width="120" height="80" loading="lazy">
                            </div>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { asset } from '@/utils/asset';
import { getAboutPageData } from '@/utils/publicContent';

const { locale } = useI18n({ useScope: 'global' });
const aboutData = computed(() => getAboutPageData(locale.value));
</script>
