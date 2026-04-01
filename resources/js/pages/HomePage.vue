<script setup>
import AboutSection from '../components/home/AboutSection.vue';
import AppDownloadSection from '../components/home/AppDownloadSection.vue';
import ArticleGrid from '../components/home/ArticleGrid.vue';
import FollowSocial from '../components/home/FollowSocial.vue';
import HeroSection from '../components/home/HeroSection.vue';
import NeighborhoodsSection from '../components/home/NeighborhoodsSection.vue';
import PropertyCarousel from '../components/home/PropertyCarousel.vue';
import SiteFooter from '../components/home/SiteFooter.vue';
import SiteHeader from '../components/home/SiteHeader.vue';

const props = defineProps({
    pageData: {
        type: Object,
        default: () => ({}),
    },
});

const d = props.pageData;
</script>

<template>
    <div class="dre-page">
        <SiteHeader v-if="d.site && d.header" :site="d.site" :header="d.header" />
        <HeroSection v-if="d.hero && d.search" :hero="d.hero" :search="d.search" />
        <PropertyCarousel v-if="d.rentalSection" :section="d.rentalSection" />
        <NeighborhoodsSection v-if="d.neighborhoods" :neighborhoods="d.neighborhoods" />
        <AboutSection v-if="d.about" :about="d.about" />
        <ArticleGrid v-if="d.articlesSection" :section="d.articlesSection" />
        <AppDownloadSection v-if="d.appCta" :app-cta="d.appCta" />
        <FollowSocial v-if="d.social" :social="d.social" />
        <SiteFooter v-if="d.footer" :footer="d.footer" />
    </div>
</template>
