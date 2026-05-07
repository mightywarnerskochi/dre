<template>
    <section class="banner banner--page banner--page--listing position-relative" aria-label="Page header">
        <div class="banner--page__bg">
            <picture>
                <img :src="asset('images/inner-banner.jpg')" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title" id="policy-page-title">{{ pageTitle }}</h1>
                <ol class="breadcrumb-minimal" aria-label="Breadcrumb">
                    <li>
                        <RouterLink :to="{ name: 'home' }" aria-label="Home">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </RouterLink>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">{{ pageTitle }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="terms-conditions commonPadding-120 policy" aria-labelledby="policy-page-title">
        <div class="container-ctn">
            <div v-if="pageContent" class="terms-conditions__inner list policy-content" v-html="pageContent"></div>
            <div v-else class="terms-conditions__inner list policy-content">
                <p>{{ t('legal.empty') }}</p>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, inject } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { asset } from '@/utils/asset';
import { getPublicSiteBoot } from '@/utils/publicSite';

const route = useRoute();
const { locale, t } = useI18n({ useScope: 'global' });
const injected = inject('dreSite', null);
const dreSite = injected ?? computed(() => getPublicSiteBoot());

const pageKey = computed(() => route.meta.policyKey || 'terms-conditions');
const policy = computed(() => dreSite.value.legalPages?.[pageKey.value] ?? null);

const pageTitle = computed(() => {
    const localized = policy.value?.title?.[locale.value] || policy.value?.title?.en;
    return localized || t(`legal.${pageKey.value}.title`);
});

const pageContent = computed(() => {
    const localized = policy.value?.content?.[locale.value] || policy.value?.content?.en || '';
    return String(localized).trim();
});
</script>
