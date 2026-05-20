<script setup>
import { useI18n } from 'vue-i18n';

defineProps({
    about: { type: Object, required: true },
});

const { t } = useI18n({ useScope: 'global' });
</script>

<template>
    <section class="about-home commonPadding-120">
        <div class="container-center ">
            <div class="d-flex flex-wrap justify-content-between">
                <div class="about-home-img position-relative">
                    <picture>
                        <img :src="about.image" width="665" height="461" :alt="about.imageAlt || 'about us image'" class="img-fluid w-100" style="object-fit: cover;">
                    </picture>
                    <svg xmlns="http://www.w3.org/2000/svg" width="665" height="461" viewBox="0 0 665 461" fill="none">
                        <path
                            d="M49.9999 0.5H664.5V362.181L608.518 404.448L608.507 404.457L608.496 404.466L542.35 460.5H0.499878V50C0.499883 22.6619 22.6618 0.5 49.9999 0.5Z"
                            stroke="#2A559C" />
                    </svg>
                </div>
                <div class="about-home-content">
                    <div class="head w-100">
                        <span>{{ about.title }}</span>
                        <h2>{{ about.eyebrow || t('footer.navAbout') }}</h2>
                        <p>{{ about.body }}</p>
                    </div>
                    <a :href="about.readMoreUrl || '/about'" class="btn theme-btn d-flex align-items-center">
                        <span>{{ t('common.learnMore') }}</span>
                        <i>
                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23"
                                fill="none">
                                <path d="M7.54255 15.0849L15.085 7.54241M15.085 7.54241H9.42817M15.085 7.54241V13.1993"
                                    stroke="white" stroke-width="1.33333" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </i>
                    </a>
                </div>
            </div>

        </div>
    </section>
</template>
