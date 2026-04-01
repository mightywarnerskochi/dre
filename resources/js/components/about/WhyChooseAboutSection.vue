<script setup>
import { computed } from 'vue';

const props = defineProps({
    block: { type: Object, required: true },
});

const images = computed(() => props.block.collageImages || []);
</script>

<template>
    <section v-if="block.active" class="dre-why-about" aria-labelledby="why-about-title">
        <div class="dre-container">
            <header v-if="block.section" class="dre-why-about__head">
                <p class="dre-why-about__eyebrow">{{ block.section.eyebrow }}</p>
                <h2 id="why-about-title" class="dre-why-about__title">{{ block.section.title }}</h2>
                <p v-if="block.section.intro" class="dre-why-about__intro">{{ block.section.intro }}</p>
            </header>
            <div class="dre-why-about__layout">
                <div class="dre-why-about__features">
                    <div v-for="item in block.items" :key="item.index + item.title" class="dre-why-feature">
                        <div class="dre-why-feature__num">{{ item.index }}</div>
                        <h3 class="dre-why-feature__title">{{ item.title }}</h3>
                        <p class="dre-why-feature__text">{{ item.body }}</p>
                    </div>
                </div>
                <div v-if="images.length" class="dre-why-about__collage" aria-hidden="true">
                    <img
                        class="dre-why-about__collage-main"
                        :src="images[0]"
                        :alt="block.section?.sectionImageAlt || ''"
                        loading="lazy"
                    >
                    <div class="dre-why-about__collage-stack">
                        <img v-if="images[1]" :src="images[1]" alt="" loading="lazy">
                        <img v-if="images[2]" :src="images[2]" alt="" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
