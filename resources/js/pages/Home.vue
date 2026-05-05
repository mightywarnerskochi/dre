<template>
    <div class="home-page">
        <HomeBanner />
        <HomePageContent />
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import HomeBanner from '@/components/HomeBanner.vue';
import HomePageContent from '@/components/HomePageContent.vue';

onMounted(() => {
    if (typeof window !== 'undefined' && typeof window.dreInitHomeUi === 'function') {
        window.dreInitHomeUi();
    }
});
</script>
