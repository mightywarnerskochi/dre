<template>
    <HomeRentalPropertiesSection />


    <HomeNeighborhoodMapSection />


    <AboutSection
        v-if="homeAbout.isAvailable"
        :about="{
            ...homeAbout,
            eyebrow: homeAbout.eyebrow || t('footer.navAbout'),
            readMoreUrl: homeAbout.readMoreUrl || '/about',
        }"
    />

    <NewsInsightsSection />

    

    <section v-if="appLinks.googlePlayUrl || appLinks.appStoreUrl || appLinks.qrCodeUrl" class="download-app d-flex align-items-center position-relative">
        <div class="container-app ">
            <div class="d-flex align-items-center position-relative justify-content-between">
                <div class="download-app-content text-center">
                    <h2 class="download-app__title">
                        {{ t('appDownload.title') }}
                    </h2>
                    <p class="download-app__desc">
                        {{ t('appDownload.description') }}
                    </p>
                    <div v-if="appLinks.googlePlayUrl || appLinks.appStoreUrl" class="download-app__stores d-flex flex-wrap justify-content-center align-items-center">
                        <a v-if="appLinks.googlePlayUrl" :href="appLinks.googlePlayUrl" class="store-btn" target="_blank" rel="noopener noreferrer">
                            <img :src="asset('public/images/home/google-play.png')" :alt="t('appDownload.googlePlayAlt')" class="img-fluid">
                        </a>
                        <a v-if="appLinks.appStoreUrl" :href="appLinks.appStoreUrl" class="store-btn" target="_blank" rel="noopener noreferrer">
                            <img :src="asset('public/images/home/appstore.png')" :alt="t('appDownload.appleStoreAlt')"
                                class="img-fluid">
                        </a>
                    </div>
                </div>
                
                <div v-if="appLinks.qrCodeUrl" class="qr-code-wrapper">
                    <div class="download-app__qr text-center bg-white shadow-sm">
                        <img :src="appLinks.qrCodeUrl" :alt="t('appDownload.qrImgAlt')" class="img-fluid">
                        <p>{{ t('appDownload.qrCaption') }}</p>
                    </div>
                </div>

                <div class="mobile-image-wrapper">
                    <div class="download-app__phone-wrap">
                        <img :src="asset('public/images/home/mobile.png')" :alt="t('appDownload.phoneAlt')" class="download-app__phone">
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
import AboutSection from '@/components/home/AboutSection.vue';
import HomeNeighborhoodMapSection from '@/components/home/HomeNeighborhoodMapSection.vue';
import HomeRentalPropertiesSection from '@/components/home/HomeRentalPropertiesSection.vue';
import NewsInsightsSection from '@/components/home/NewsInsightsSection.vue';
import { getHomeAboutData } from '@/utils/publicContent';
import { getPublicSiteBoot } from '@/utils/publicSite';

const { t, locale } = useI18n();
const homeAbout = computed(() => getHomeAboutData(locale.value));
const siteBoot = computed(() => getPublicSiteBoot());
const appLinks = computed(() => siteBoot.value.appLinks);
</script>
