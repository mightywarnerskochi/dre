<template>
    <div class="dre-vue-root">
        <Header />
        <Navbar />
        <main>
            <RouterView />
        </main>
        <Footer />
        <ModalEnquiry />
        <FloatingWidgets />
    </div>
</template>

<script setup>
import { computed, provide } from 'vue';
import { RouterView } from 'vue-router';
import Header from '@/components/Header.vue';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import ModalEnquiry from '@/components/ModalEnquiry.vue';
import FloatingWidgets from '@/components/FloatingWidgets.vue';
import { getPublicSiteBoot } from '@/utils/publicSite';

const dreSite = computed(() => getPublicSiteBoot());
provide('dreSite', dreSite);
</script>
