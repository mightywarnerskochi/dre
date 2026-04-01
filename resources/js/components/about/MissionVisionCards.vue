<script setup>
defineProps({
    block: { type: Object, default: () => ({ items: [] }) },
});
</script>

<template>
    <section v-if="block.items?.length" class="dre-mvv" aria-labelledby="mvv-heading">
        <div class="dre-container">
            <h2 id="mvv-heading" class="dre-sr-only">Mission, vision, and values</h2>
            <div class="dre-mvv__grid">
                <article
                    v-for="card in block.items"
                    :key="card.index + card.title"
                    class="dre-mvv-card"
                    :class="{ 'dre-mvv-card--overlay': card.variant === 'overlay' }"
                >
                    <template v-if="card.variant === 'overlay' && card.image">
                        <div
                            class="dre-mvv-card__media"
                            role="img"
                            :style="{ backgroundImage: `url(${card.image})` }"
                            :aria-label="card.title"
                        />
                        <div class="dre-mvv-card__shade" aria-hidden="true" />
                    </template>
                    <span class="dre-mvv-card__num">{{ card.index }}</span>
                    <h3 class="dre-mvv-card__title">{{ card.title }}</h3>
                    <p class="dre-mvv-card__body">{{ card.body }}</p>
                </article>
            </div>
        </div>
    </section>
</template>
