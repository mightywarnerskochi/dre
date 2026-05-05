<template>
    <header>
        <div class="container-ctn">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <RouterLink :to="{ name: 'home' }" class="brand" :aria-label="t('brand.logoAria')">
                    <picture>
                        <img
                            :src="logoSrc"
                            width="320"
                            height="50"
                            class="img-fluid"
                            :alt="t('brand.logoAlt')"
                            loading="eager"
                        />
                    </picture>
                </RouterLink>

                <div class="header-right d-flex align-items-center">
                    <div class="dropdown lang-switcher">
                        <button
                            class="lang-switcher__toggle"
                            type="button"
                            id="langSwitcherMenu"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            :aria-label="t('lang.switcherAria')"
                        >
                            <img
                                :src="flagSrc"
                                :alt="locale === 'ar' ? t('lang.arFlagAlt') : t('lang.enFlagAlt')"
                                class="lang-switcher__flag"
                                width="22"
                                height="16"
                            />
                            <span class="lang-switcher__label">{{ displayCode }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <ul class="dropdown-menu lang-switcher__menu" aria-labelledby="langSwitcherMenu">
                            <li>
                                <button
                                    class="dropdown-item"
                                    type="button"
                                    :class="{ active: locale === 'en' }"
                                    :aria-pressed="locale === 'en' ? 'true' : 'false'"
                                    @click="setLang('en')"
                                >
                                    <img :src="asset('public/images/english.jpg')" alt="" width="22" height="16" />
                                    <span>{{ t('lang.english') }}</span>
                                </button>
                            </li>
                            <li>
                                <button
                                    class="dropdown-item"
                                    type="button"
                                    :class="{ active: locale === 'ar' }"
                                    :aria-pressed="locale === 'ar' ? 'true' : 'false'"
                                    @click="setLang('ar')"
                                >
                                    <img :src="asset('public/images/arabic.jpg')" alt="" width="22" height="16" />
                                    <span>{{ t('lang.arabic') }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#burgerMenu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path
                                d="M20.0001 11.3766C20.4037 11.3766 20.7907 11.2163 21.0761 10.931C21.3615 10.6456 21.5218 10.2586 21.5218 9.85498C21.5218 9.45141 21.3615 9.06437 21.0761 8.779C20.7907 8.49363 20.4037 8.33331 20.0001 8.33331C19.5965 8.33331 19.2095 8.49363 18.9241 8.779C18.6388 9.06437 18.4785 9.45141 18.4785 9.85498C18.4785 10.2586 18.6388 10.6456 18.9241 10.931C19.2095 11.2163 19.5965 11.3766 20.0001 11.3766ZM30.1468 11.3766C30.3466 11.3766 30.5445 11.3373 30.7291 11.2608C30.9137 11.1843 31.0815 11.0723 31.2228 10.931C31.3641 10.7897 31.4762 10.6219 31.5526 10.4373C31.6291 10.2527 31.6685 10.0548 31.6685 9.85498C31.6685 9.65515 31.6291 9.45728 31.5526 9.27266C31.4762 9.08805 31.3641 8.9203 31.2228 8.779C31.0815 8.6377 30.9137 8.52561 30.7291 8.44914C30.5445 8.37267 30.3466 8.33331 30.1468 8.33331C29.7432 8.33331 29.3562 8.49363 29.0708 8.779C28.7854 9.06437 28.6251 9.45141 28.6251 9.85498C28.6251 10.2586 28.7854 10.6456 29.0708 10.931C29.3562 11.2163 29.7432 11.3766 30.1468 11.3766ZM9.85345 11.3766C10.0533 11.3766 10.2512 11.3373 10.4358 11.2608C10.6204 11.1843 10.7881 11.0723 10.9294 10.931C11.0707 10.7897 11.1828 10.6219 11.2593 10.4373C11.3358 10.2527 11.3751 10.0548 11.3751 9.85498C11.3751 9.65515 11.3358 9.45728 11.2593 9.27266C11.1828 9.08805 11.0707 8.9203 10.9294 8.779C10.7881 8.6377 10.6204 8.52561 10.4358 8.44914C10.2512 8.37267 10.0533 8.33331 9.85345 8.33331C9.44988 8.33331 9.06284 8.49363 8.77747 8.779C8.4921 9.06437 8.33179 9.45141 8.33179 9.85498C8.33179 10.2586 8.4921 10.6456 8.77747 10.931C9.06284 11.2163 9.44988 11.3766 9.85345 11.3766ZM20.0001 21.5216C20.4037 21.5216 20.7907 21.3613 21.0761 21.076C21.3615 20.7906 21.5218 20.4036 21.5218 20C21.5218 19.5964 21.3615 19.2094 21.0761 18.924C20.7907 18.6386 20.4037 18.4783 20.0001 18.4783C19.5965 18.4783 19.2095 18.6386 18.9241 18.924C18.6388 19.2094 18.4785 19.5964 18.4785 20C18.4785 20.4036 18.6388 20.7906 18.9241 21.076C19.2095 21.3613 19.5965 21.5216 20.0001 21.5216ZM30.1468 21.5216C30.5504 21.5216 30.9374 21.3613 31.2228 21.076C31.5081 20.7906 31.6685 20.4036 31.6685 20C31.6685 19.5964 31.5081 19.2094 31.2228 18.924C30.9374 18.6386 30.5504 18.4783 30.1468 18.4783C29.7432 18.4783 29.3562 18.6386 29.0708 18.924C28.7854 19.2094 28.6251 19.5964 28.6251 20C28.6251 20.4036 28.7854 20.7906 29.0708 21.076C29.3562 21.3613 29.7432 21.5216 30.1468 21.5216ZM9.85345 21.5216C10.257 21.5216 10.6441 21.3613 10.9294 21.076C11.2148 20.7906 11.3751 20.4036 11.3751 20C11.3751 19.5964 11.2148 19.2094 10.9294 18.924C10.6441 18.6386 10.257 18.4783 9.85345 18.4783C9.44988 18.4783 9.06284 18.6386 8.77747 18.924C8.4921 19.2094 8.33179 19.5964 8.33179 20C8.33179 20.4036 8.4921 20.7906 8.77747 21.076C9.06284 21.3613 9.44988 21.5216 9.85345 21.5216ZM20.0001 31.6666C20.4037 31.6666 20.7907 31.5063 21.0761 31.221C21.3615 30.9356 21.5218 30.5486 21.5218 30.145C21.5218 29.7414 21.3615 29.3544 21.0761 29.069C20.7907 28.7836 20.4037 28.6233 20.0001 28.6233C19.5965 28.6233 19.2095 28.7836 18.9241 29.069C18.6388 29.3544 18.4785 29.7414 18.4785 30.145C18.4785 30.5486 18.6388 30.9356 18.9241 31.221C19.2095 31.5063 19.5965 31.6666 20.0001 31.6666ZM30.1468 31.6666C30.5504 31.6666 30.9374 31.5063 31.2228 31.221C31.5081 30.9356 31.6685 30.5486 31.6685 30.145C31.6685 29.7414 31.5081 29.3544 31.2228 29.069C30.9374 28.7836 30.5504 28.6233 30.1468 28.6233C29.7432 28.6233 29.3562 28.7836 29.0708 29.069C28.7854 29.3544 28.6251 29.7414 28.6251 30.145C28.6251 30.5486 28.7854 30.9356 29.0708 31.221C29.3562 31.5063 29.7432 31.6666 30.1468 31.6666ZM9.85345 31.6666C10.257 31.6666 10.6441 31.5063 10.9294 31.221C11.2148 30.9356 11.3751 30.5486 11.3751 30.145C11.3751 29.7414 11.2148 29.3544 10.9294 29.069C10.6441 28.7836 10.257 28.6233 9.85345 28.6233C9.44988 28.6233 9.06284 28.7836 8.77747 29.069C8.4921 29.3544 8.33179 29.7414 8.33179 30.145C8.33179 30.5486 8.4921 30.9356 8.77747 31.221C9.06284 31.5063 9.44988 31.6666 9.85345 31.6666Z"
                                stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { asset } from '@/utils/asset';

const { locale, t } = useI18n({ useScope: 'global' });
const route = useRoute();
const isScrolled = ref(false);

const displayCode = computed(() => (locale.value === 'ar' ? 'AR' : 'ENG'));

const flagSrc = computed(() => asset(locale.value === 'ar' ? 'public/images/arabic.jpg' : 'public/images/english.jpg'));
const logoSrc = computed(() => {
    const isHomeTop = route.name === 'home' && !isScrolled.value;
    return asset(isHomeTop ? 'public/images/logo.png' : 'public/images/logo-blue.png');
});

function updateScrolled() {
    isScrolled.value = window.scrollY > 20;
}

onMounted(() => {
    updateScrolled();
    window.addEventListener('scroll', updateScrolled, { passive: true });
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateScrolled);
});

function setLang(code) {
    const safe = code === 'ar' ? 'ar' : 'en';
    locale.value = safe;
    document.documentElement.setAttribute('lang', safe);
    document.documentElement.setAttribute('dir', safe === 'ar' ? 'rtl' : 'ltr');
    try {
        localStorage.setItem('dre_lang', safe);
    } catch (_e) {
        /* ignore */
    }
}
</script>
