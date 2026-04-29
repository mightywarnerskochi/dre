<script setup>
defineProps({
    article: { type: Object, required: true },
    reverse: { type: Boolean, default: false },
});
</script>

<template>
    <article
        class="dre-article-row"
        :class="{ 'dre-article-row--reverse': reverse }"
    >
        <div class="dre-article-row__media">
            <img :src="article.image" :alt="article.title" />
        </div>
        <div class="dre-article-row__body">
            <p class="dre-article-row__tag">{{ article.category }}</p>
            <h3 class="dre-article-row__title">{{ article.title }}</h3>
            <p class="dre-article-row__excerpt">{{ article.excerpt }}</p>
            <a :href="article.url" class="dre-btn dre-btn--ghost dre-article-row__link">
                Read More
                <svg class="dre-icon-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3"
                    />
                </svg>
            </a>
        </div>
    </article>
</template>
