<script setup>
defineProps({
    journey: { type: Object, required: true },
});
</script>

<template>
    <section v-if="journey.items?.length" class="dre-journey" aria-labelledby="journey-title">
        <div class="dre-container">
            <h2 id="journey-title" class="dre-journey__title">{{ journey.title }}</h2>
            <div class="dre-journey__scroll">
                <div class="dre-journey__track">
                    <div v-for="(item, idx) in journey.items" :key="item.year + '-' + idx" class="dre-journey-item">
                        <div class="dre-journey-item__dot" />
                        <p class="dre-journey-item__year">{{ item.year }}</p>
                        <div class="dre-journey-item__prose dre-cms-html" v-html="item.bodyHtml" />
                        <div v-if="item.images?.length" class="dre-journey-item__thumbs">
                            <img v-for="(src, i) in item.images" :key="i" :src="src" alt="" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
