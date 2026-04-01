<script setup>
import { computed } from 'vue';
import AboutPageIntro from '../components/about/AboutPageIntro.vue';
import DrePageHero from '../components/layout/DrePageHero.vue';
import FollowSocial from '../components/home/FollowSocial.vue';
import JourneyTimeline from '../components/about/JourneyTimeline.vue';
import MissionVisionCards from '../components/about/MissionVisionCards.vue';
import SiteFooter from '../components/home/SiteFooter.vue';
import SiteHeader from '../components/home/SiteHeader.vue';
import WhyChooseAboutSection from '../components/about/WhyChooseAboutSection.vue';

const props = defineProps({
    pageData: {
        type: Object,
        default: () => ({}),
    },
});

const d = props.pageData;

const aboutCrumbs = computed(() => [
    { label: 'Home', href: '/' },
    { label: d.hero?.breadcrumbLabel || 'About Us' },
]);

const aboutHeroImage = computed(() => d.hero?.backgroundImage || '/images/dre/hero-dubai.jpg');
</script>

<template>
    <div class="dre-page dre-about-page">
        <SiteHeader
            v-if="d.site && d.header"
            variant="solid"
            :site="d.site"
            :header="d.header"
        />
        <DrePageHero
            v-if="d.hero"
            :title="d.hero.title || 'About Us'"
            :background-image="aboutHeroImage"
            align="center"
            :crumbs="aboutCrumbs"
            :show-carousel-arrows="true"
        />
        <AboutPageIntro v-if="d.about" :about="d.about" />
        <MissionVisionCards v-if="d.missionVision" :block="d.missionVision" />
        <WhyChooseAboutSection v-if="d.whyChooseUs" :block="d.whyChooseUs" />
        <JourneyTimeline v-if="d.journey" :journey="d.journey" />
        <FollowSocial v-if="d.social" :social="d.social" />
        <SiteFooter v-if="d.footer" :footer="d.footer" />
        <button type="button" class="dre-chat-fab">Chat with us</button>
    </div>
</template>
