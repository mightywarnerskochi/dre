<script setup>
defineProps({
    about: { type: Object, required: true },
});
</script>

<template>
    <section id="about" class="dre-section dre-section--white">
        <div class="dre-container">
            <div class="dre-about-shell">
                <div class="dre-about-grid">
                    <div class="dre-about__image">
                        <img :src="about.image" alt="" />
                    </div>
                    <div class="dre-about__copy">
                        <p class="dre-about__eyebrow">{{ about.eyebrow }}</p>
                        <h2 class="dre-about__title">{{ about.title }}</h2>
                        <p class="dre-about__text">{{ about.body }}</p>
                        <div class="dre-about__cta">
                            <a :href="about.readMoreUrl" class="dre-btn dre-btn--outline-dark">
                                <span class="dre-btn-icon-circle" aria-hidden="true">
                                    <svg
                                        class="dre-icon-sm"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5l7 7-7 7"
                                        />
                                    </svg>
                                </span>
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
