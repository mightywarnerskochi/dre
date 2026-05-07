<script setup>
import { computed, inject, onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { getHomeRentalSectionData } from '@/utils/publicContent';
import { getPublicSiteBoot } from '@/utils/publicSite';
import { siteTelHref, siteWhatsappHref } from '@/utils/siteContact';
import { dreNormalizePropertyImages, dreOnPropertyImgError } from '@/utils/propertyImages';

const { locale, t } = useI18n();

const injectedSite = inject('dreSite', null);
const dreSite = injectedSite ?? computed(() => getPublicSiteBoot());
const siteCallHref = computed(() => siteTelHref(dreSite.value));
const siteWhatsappLink = computed(() => siteWhatsappHref(dreSite.value));
const rentalSectionData = computed(() => getHomeRentalSectionData(locale.value));
const currentImageIndex = ref({});
let autoSlideTimer = null;

const rentalProperties = computed(() => rentalSectionData.value?.properties || []);
const sectionTitle = computed(() => rentalSectionData.value?.title || t('home.rentals.title'));
const sectionDescription = computed(
    () => rentalSectionData.value?.description || t('home.rentals.description'),
);
const isVisible = computed(
    () => rentalSectionData.value?.displayHome !== false && rentalProperties.value.length > 0,
);

function getImages(property) {
    return dreNormalizePropertyImages(property?.images).slice(0, 3);
}

function getActiveIndex(property) {
    const images = getImages(property);
    const index = Number(currentImageIndex.value[property.id] ?? 0);
    return Number.isInteger(index) && index >= 0 && index < images.length ? index : 0;
}

function setActiveImage(propertyId, index) {
    currentImageIndex.value[propertyId] = index;
}

function getActiveImage(property) {
    const images = getImages(property);
    return images[getActiveIndex(property)] || images[0];
}

function formatPrice(value) {
    const number = Number(value);
    if (!Number.isFinite(number)) return '-';
    return `${number.toLocaleString(locale.value === 'ar' ? 'ar-AE' : 'en-US')} د.إ`;
}

function formatSqft(value) {
    const number = Number(value);
    if (!Number.isFinite(number) || number <= 0) return '-';
    return `${number.toLocaleString(locale.value === 'ar' ? 'ar-AE' : 'en-US')} ft²`;
}

/** Used by global script.js card click handler (data-property-url). */
function propertyCardDetailUrl(property) {
    const fromBoot = property?.url && String(property.url).trim();
    if (fromBoot) return fromBoot;
    const slug = property?.slug && String(property.slug).trim();
    if (slug) return `/property-details/${slug}`;
    return null;
}

function tickAutoSlides() {
    rentalProperties.value.forEach((property) => {
        const images = getImages(property);
        if (images.length <= 1) return;
        const current = getActiveIndex(property);
        setActiveImage(property.id, (current + 1) % images.length);
    });
}

function startAutoSlide() {
    if (autoSlideTimer) clearInterval(autoSlideTimer);
    autoSlideTimer = setInterval(tickAutoSlides, 3500);
}

watch(rentalProperties, () => {
    currentImageIndex.value = {};
}, { deep: true });

onMounted(() => {
    startAutoSlide();
});

onUnmounted(() => {
    if (autoSlideTimer) clearInterval(autoSlideTimer);
});
</script>

<template>
    <section v-if="isVisible" class="rental-properties commonPadding" id="rental-properties">
        <div class="container-ctn">
            <div class="row">
                <div class="col-12">
                    <div class="head text-center">
                        <h2>{{ sectionTitle }}</h2>
                        <p>{{ sectionDescription }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid p-0">
            <div class="property-slider-wrap">
                <div class="property-slider">
                    <article
                        v-for="property in rentalProperties"
                        :key="property.id"
                        class="property-card"
                        :data-property-url="propertyCardDetailUrl(property)"
                    >
                        <div class="property-card__ghost" aria-hidden="true"></div>
                        <div class="property-card__inner">
                            <div class="property-card__media">
                                <span v-if="property.isFeatured" class="property-card__badge">Featured</span>
                                <picture>
                                    <img
                                        :key="`${property.id}-${getActiveIndex(property)}`"
                                        :src="getActiveImage(property)"
                                        :alt="property.title || t('home.rentals.card.imageAlt')"
                                        width="427"
                                        height="260"
                                        loading="eager"
                                        @error="dreOnPropertyImgError"
                                    >
                                </picture>
                                <div class="property-card__dots" role="tablist" aria-label="Photo gallery">
                                    <button
                                        v-for="(img, idx) in getImages(property)"
                                        :key="`${property.id}-${idx}`"
                                        type="button"
                                        :class="{ 'is-active': idx === getActiveIndex(property) }"
                                        @click="setActiveImage(property.id, idx)"
                                    />
                                </div>
                                <a
                                    v-if="property.virtualTourUrl"
                                    class="property-card__360"
                                    :href="property.virtualTourUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="360° virtual tour"
                                >360°</a>
                            </div>

                            <div class="property-card__body">
                                <h3 class="property-card__title">{{ property.title || '-' }}</h3>
                                <p class="property-card__location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z"
                                            fill="#2F3F58"
                                        />
                                    </svg>
                                    <span>{{ property.location || '-' }}</span>
                                </p>

                                <div class="property-card__meta">
                                    <div class="property-card__price-row">
                                        <div class="property-card__price">{{ formatPrice(property.price) }}</div>
                                        <div class="property-card__photos">
                                            {{ property.imageCount }} {{ t('home.rentals.card.photos') }}
                                        </div>
                                    </div>
                                    <div class="property-card__tags">
                                        <span class="property-tag property-tag--fill">{{ property.propertyTypeLabel || '-' }}</span>
                                        <span class="property-tag property-tag--outline">{{ property.listingTypeLabel || '-' }}</span>
                                    </div>
                                </div>

                                <div class="property-details">
                                    <div class="property-details__item">
                                        <span>{{ property.beds || 0 }} {{ t('home.rentals.card.bedroom') }}</span>
                                    </div>
                                    <div class="property-details__item">
                                        <span>{{ property.baths || 0 }} {{ t('home.rentals.card.bathroom') }}</span>
                                    </div>
                                    <div class="property-details__item">
                                        <span>{{ formatSqft(property.sqft) }}</span>
                                    </div>
                                </div>

                                <div class="property-card__actions">
                                    <a
                                        class="property-btn property-btn--primary"
                                        :href="property.inquireUrl || '#'"
                                        data-bs-toggle="modal"
                                        data-bs-target="#siteEnquiryForm"
                                    >
                                        {{ t('home.rentals.card.enquiry') }}
                                    </a>
                                    <a
                                        v-if="siteCallHref"
                                        class="property-btn property-btn--light"
                                        :href="siteCallHref"
                                    >
                                        {{ t('home.rentals.card.callNow') }}
                                    </a>
                                    <a
                                        v-if="siteWhatsappLink"
                                        class="property-btn property-btn--outline"
                                        :href="siteWhatsappLink"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {{ t('home.rentals.card.whatsapp') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <div class="property-slider-controls">
            <div class="property-slider__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="33">
                <span class="property-slider__progress-fill"></span>
            </div>
            <div class="property-slider__arrows">
                <button type="button" class="property-slider__arrow property-slider__arrow--prev" aria-label="Previous properties">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M20 12H4M4 12L10 6M4 12L10 18" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button type="button" class="property-slider__arrow property-slider__arrow--next" aria-label="Next properties">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M4 12L20 12M20 12L14 18M20 12L14 6" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </section>
</template>
<style scoped>
.property-card__dots {
    position: absolute;
    left: 14px;
    bottom: 12px;
    display: inline-flex;
    gap: 6px;
    z-index: 2;
}
.property-card__dots button {
    width: 9px;
    height: 9px;
    border-radius: 999px;
    border: 0;
    padding: 0;
    background: rgba(255, 255, 255, 0.45);
    cursor: pointer;
}

.property-card__dots button.is-active {
    background: #ffffff;
}

</style>