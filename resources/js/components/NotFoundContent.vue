<template>
<section class="banner banner--page banner--page--listing position-relative" aria-label="Page header">
        <div class="banner--page__bg">
            <picture>
                <img :src="asset('public/images/inner-banner.jpg')" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
               
            </div>
        </div>
    </section>

    <section class="error-404 commonPadding-120" aria-labelledby="error-404-title">
        <div class="error-404__inner">
            <div class="error-404__gfx">
                <img :src="asset('public/images/404.png')" alt="Page Not Found" width="100%" height="100%">
            </div>
            <h1 class="error-404__title" id="error-404-title">Page Not Found</h1>
            <p class="error-404__lead">
                Sorry, the page you are looking for does not exist. If you think something is broken, report a problem.
            </p>
            <div class="error-404__actions">
                <a class="btn btn-theme" href="/">Back to Home Page</a>
                <a class="btn btn-theme error-404__btn-outline" href="/contact">Contact Support</a>
            </div>
        </div>
    </section>

</template>

<script setup>
import { asset } from '@/utils/asset';
</script>
