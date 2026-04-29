<script setup>
import ArticleCard from './ArticleCard.vue';

defineProps({
    section: { type: Object, required: true },
});
</script>

<template>
    <section id="news" class="dre-section dre-section--white">
        <div class="dre-container">
            <div class="dre-section__head">
                <p v-if="section.eyebrow" class="dre-section__eyebrow">{{ section.eyebrow }}</p>
                <h2 class="dre-section__title">{{ section.title }}</h2>
            </div>
            <div class="dre-news-grid">
                <ArticleCard
                    v-for="(a, index) in section.articles"
                    :key="a.id"
                    :article="a"
                    :reverse="index % 2 === 1"
                />
            </div>
        </div>
    </section>
</template>
