<script setup>
defineProps({
    about: { type: Object, required: true },
});
</script>

<template>
    <section class="dre-about-intro dre-section dre-section--white" aria-labelledby="about-intro-title">
        <div class="dre-container">
            <p class="dre-about-intro__eyebrow">{{ about.eyebrow }}</p>
            <h2 id="about-intro-title" class="dre-about-intro__title">{{ about.title }}</h2>
            <div class="dre-about-intro__prose dre-cms-html" v-html="about.bodyHtml" />
            <div v-if="about.gallery?.length" class="dre-about-intro__gallery">
                <img
                    v-for="(img, idx) in about.gallery"
                    :key="idx"
                    :src="img.src"
                    :alt="img.alt || ''"
                    loading="lazy"
                >
            </div>
        </div>
    </section>
</template>
