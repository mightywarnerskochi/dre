<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    site: { type: Object, required: true },
    header: { type: Object, required: true },
    /**
     * hero: transparent over homepage carousel until scroll (default).
     * solid: Figma inner pages — white bar, bottom hairline, dark chrome.
     */
    variant: { type: String, default: 'hero' },
});

const defaultNavItems = [
    { label: 'Home', href: '/' },
    { label: 'Properties', href: '#rentals' },
    { label: 'About', href: '#about' },
    { label: 'Contact', href: '#contact' },
];

const navItems = computed(() => props.header.navItems || defaultNavItems);

const isScrolled = ref(false);

const isLightChrome = computed(() => props.variant === 'solid' || isScrolled.value);

function updateScrolled() {
    if (typeof window === 'undefined') return;
    isScrolled.value = window.scrollY > 32;
}

onMounted(() => {
    updateScrolled();
    window.addEventListener('scroll', updateScrolled, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('scroll', updateScrolled);
});
</script>

<template>
    <header
        class="dre-header"
        :class="{
            'is-scrolled': isLightChrome,
            'dre-header--solid': variant === 'solid',
        }"
    >
        <div class="dre-header-inner">
            <a href="/" class="dre-logo">
                <img
                    v-if="site.logoUrl"
                    :src="site.logoUrl"
                    :alt="site.logoAlt || site.fullName"
                    class="dre-logo__image"
                >
                <span v-else class="dre-logo__text">{{ site.fullName }}</span>
            </a>
            <nav v-show="isLightChrome" class="dre-header-nav" aria-label="Primary">
                <a
                    v-for="item in navItems"
                    :key="item.label + item.href"
                    :href="item.href"
                    class="dre-header-nav__link"
                >
                    {{ item.label }}
                </a>
            </nav>
            <div class="dre-header-actions">
                <a :href="header.listPropertyUrl" class="dre-btn dre-btn--list">
                    Tenant Portal
                </a>
                <button type="button" class="dre-btn dre-btn--menu" aria-label="Open menu">
                    <svg class="dre-icon-md" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <circle cx="5" cy="5" r="1.6" />
                        <circle cx="12" cy="5" r="1.6" />
                        <circle cx="19" cy="5" r="1.6" />
                        <circle cx="5" cy="12" r="1.6" />
                        <circle cx="12" cy="12" r="1.6" />
                        <circle cx="19" cy="12" r="1.6" />
                        <circle cx="5" cy="19" r="1.6" />
                        <circle cx="12" cy="19" r="1.6" />
                        <circle cx="19" cy="19" r="1.6" />
                    </svg>
                </button>
            </div>
        </div>
    </header>
</template>
