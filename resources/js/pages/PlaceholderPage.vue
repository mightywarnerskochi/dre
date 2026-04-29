<template>
    <section class="banner banner--page banner--page--listing position-relative" aria-label="Page header">
        <div class="banner--page__bg">
            <picture>
                <img :src="asset('public/images/inner-banner.jpg')" alt="" width="1920" height="403" loading="eager" />
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">{{ heading }}</h1>
                <ol class="breadcrumb-minimal" aria-label="Breadcrumb">
                    <li>
                        <RouterLink :to="{ name: 'home' }" aria-label="Home">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </RouterLink>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">{{ heading }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="commonPadding container-ctn">
        <div class="head text-center py-5">
            <p class="mb-0">{{ blurb }}</p>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import { asset } from '@/utils/asset';

const route = useRoute();

const heading = computed(() => (typeof route.meta.pageTitle === 'string' ? route.meta.pageTitle : 'Page'));

const blurb = computed(() =>
    route.name === 'not-found'
        ? 'The page you requested could not be found.'
        : 'Content for this route will be migrated from the matching file under html/.'
);
</script>
