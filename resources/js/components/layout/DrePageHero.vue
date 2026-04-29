<script setup>
defineProps({
    title: { type: String, required: true },
    backgroundImage: { type: String, required: true },
    /** 'center' | 'left' — Figma: listing/about centered; property detail left */
    align: { type: String, default: 'center' },
    /** { label, href? }[] — last item may omit href for current page */
    crumbs: { type: Array, default: () => [] },
    showCarouselArrows: { type: Boolean, default: false },
});
</script>

<template>
    <section
        class="dre-page-hero"
        :class="align === 'left' ? 'dre-page-hero--left' : 'dre-page-hero--center'"
        :aria-labelledby="`dre-page-hero-${title?.slice(0, 8) || 't'}`"
    >
        <div
            class="dre-page-hero__media"
            :style="{ backgroundImage: `url(${backgroundImage})` }"
            role="presentation"
        />
        <div class="dre-page-hero__gradient" aria-hidden="true" />
        <div class="dre-page-hero__vignette" aria-hidden="true" />

        <div v-if="showCarouselArrows" class="dre-page-hero__arrows" aria-hidden="true">
            <button type="button" class="dre-page-hero__arrow" tabindex="-1">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 6l-6 6 6 6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <button type="button" class="dre-page-hero__arrow" tabindex="-1">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <div class="dre-page-hero__inner">
            <h1 :id="`dre-page-hero-${title?.slice(0, 8) || 't'}`" class="dre-page-hero__title">
                {{ title }}
            </h1>
            <nav v-if="crumbs.length" class="dre-page-hero__crumbs" aria-label="Breadcrumb">
                <template v-for="(c, i) in crumbs" :key="i">
                    <span v-if="i > 0" class="dre-page-hero__sep">/</span>
                    <a
                        v-if="c.href === '/'"
                        href="/"
                        class="dre-page-hero__home"
                        aria-label="Home"
                    >
                        <svg
                            class="dre-page-hero__home-icon"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8h5z" />
                        </svg>
                    </a>
                    <a v-else-if="c.href" :href="c.href">{{ c.label }}</a>
                    <span v-else>{{ c.label }}</span>
                </template>
            </nav>
        </div>
    </section>
</template>
