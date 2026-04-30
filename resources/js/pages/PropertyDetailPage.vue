<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import DrePageHero from '../components/layout/DrePageHero.vue';
import MortgageCalculator from '../components/properties/MortgageCalculator.vue';
import PropertyDetailMap from '../components/properties/PropertyDetailMap.vue';
import Footer from '../components/Footer.vue';
import Header from '../components/Header.vue';
import {
    dreNormalizePropertyImages,
    dreOnPropertyImgError,
    DRE_PROPERTY_PLACEHOLDER_IMAGE,
} from '../utils/propertyImages';

const props = defineProps({
    pageData: { type: Object, default: () => ({}) },
    propertyId: { type: [Number, String], required: true },
});

const d = props.pageData;
const property = ref(null);
const similar = ref([]);
const loading = ref(true);
const currentImage = ref('');
const galleryIndex = ref(0);

const galleryImages = computed(() => dreNormalizePropertyImages(property.value?.images));

const heroTitle = computed(() => property.value?.title || d.hero?.title || 'Property');

const heroBg = computed(() => {
    if (property.value) {
        return galleryImages.value[0];
    }

    return d.hero?.backgroundImage || DRE_PROPERTY_PLACEHOLDER_IMAGE;
});

const detailCrumbs = computed(() => {
    const t = property.value?.title || d.hero?.title || '';

    return [
        { label: 'Home', href: '/' },
        { label: 'Properties', href: '/properties' },
        { label: t || '...' },
    ];
});

const listingLabel = computed(() => {
    const type = String(property.value?.listing_type || '').trim().toLowerCase();

    if (type === 'rent') return 'For Rent';
    if (type === 'sale') return 'For Sale';

    return 'Property';
});

const propertyTypeLabel = computed(() => {
    const raw = String(property.value?.property_type || '').replace(/[_-]+/g, ' ').trim();

    return raw ? raw.replace(/\b\w/g, (char) => char.toUpperCase()) : 'Residence';
});

const factCards = computed(() => {
    if (!property.value) return [];

    return [
        { label: 'Bedrooms', value: property.value.bedrooms || 'N/A' },
        { label: 'Bathrooms', value: property.value.bathrooms || 'N/A' },
        { label: 'Area', value: property.value.sqft ? `${formatNumber(property.value.sqft)} sqft` : 'N/A' },
        { label: 'Type', value: propertyTypeLabel.value },
    ];
});

const detailCards = computed(() => {
    const rows = Array.isArray(property.value?.details_grid) ? property.value.details_grid : [];

    return rows
        .map((item, index) => ({
            id: item?.label || item?.title || `detail-${index}`,
            label: item?.label || item?.title || 'Detail',
            value: item?.value || item?.text || item?.description || 'N/A',
        }))
        .filter((item) => item.value !== '');
});

const amenityCards = computed(() => {
    const rows = Array.isArray(property.value?.amenities) ? property.value.amenities : [];

    return rows
        .map((item, index) => {
            if (typeof item === 'string') {
                return { id: `${item}-${index}`, label: item };
            }

            return {
                id: item?.label || item?.title || `amenity-${index}`,
                label: item?.label || item?.title || item?.value || 'Amenity',
            };
        })
        .filter((item) => item.label);
});

const featureList = computed(() => {
    const rows = Array.isArray(property.value?.features) ? property.value.features : [];

    return rows
        .map((item) => (typeof item === 'string' ? item : item?.label || item?.title || item?.value || ''))
        .filter(Boolean);
});

const accessPlaces = computed(() => {
    const rows = Array.isArray(property.value?.easy_access) ? property.value.easy_access : [];

    return rows.map((item, index) => {
        const dist = item?.distance;
        const distanceLabel =
            dist != null && String(dist).trim() !== '' ? String(dist) : 'Nearby';

        return {
            id: `${item?.name || 'place'}-${index}`,
            name: item?.name || 'Nearby location',
            distance: distanceLabel,
            icon: item?.icon || null,
        };
    });
});

const mapUrl = computed(() => {
    if (!property.value) return '#';

    const lat = Number(property.value.latitude);
    const lng = Number(property.value.longitude);

    if (Number.isFinite(lat) && Number.isFinite(lng) && lat !== 0 && lng !== 0) {
        return `https://www.google.com/maps?q=${lat},${lng}`;
    }

    return property.value.location
        ? `https://www.google.com/maps/search/${encodeURIComponent(property.value.location)}`
        : '#';
});

const hasMapCenter = computed(() => {
    const lat = Number(property.value?.latitude);
    const lng = Number(property.value?.longitude);

    return (
        Number.isFinite(lat)
        && Number.isFinite(lng)
        && (Math.abs(lat) > 1e-5 || Math.abs(lng) > 1e-5)
    );
});

const nearbyPlacesForMap = computed(() =>
    (Array.isArray(property.value?.nearby_places) ? property.value.nearby_places : []),
);

const mapPopupPriceDisplay = computed(() => {
    if (!property.value) return '';

    return formatPrice(property.value.price);
});

const agentInitials = computed(() => {
    const name = property.value?.agent?.name || '';

    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() || '')
        .join('') || 'AG';
});

async function fetchProperty() {
    loading.value = true;

    try {
        const response = await window.axios.get(`/api/properties/${props.propertyId}`);
        property.value = response.data.property;
        similar.value = response.data.similar || [];
    } catch (error) {
        console.error('Error fetching property:', error);
    } finally {
        loading.value = false;
    }
}

watch(
    () => property.value,
    (p) => {
        if (!p) return;

        const imgs = dreNormalizePropertyImages(p.images);
        currentImage.value = imgs[0];
        galleryIndex.value = 0;
    },
    { immediate: true },
);

function setHeroImage(img, index) {
    currentImage.value = img;
    galleryIndex.value = index;
}

function formatNumber(num) {
    const value = Number(num || 0);
    return new Intl.NumberFormat('en-AE').format(value);
}

function formatPrice(num) {
    return `${formatNumber(num)} ${property.value?.currency || 'AED'}`;
}

function similarUrl(item) {
    if (typeof item?.url === 'string' && item.url.trim() !== '') return item.url;
    if (item?.slug) return `/property-details/${item.slug}`;
    if (item?.id) return `/property-details/${item.id}`;
    return '#';
}

function similarImage(item) {
    const list = Array.isArray(item?.images) ? item.images : [];
    const first = list.find((img) => typeof img === 'string' && img.trim() !== '');
    return first || item?.image || DRE_PROPERTY_PLACEHOLDER_IMAGE;
}

onMounted(() => {
    fetchProperty();
});
</script>

<template>
    <div class="dre-page dre-property-detail-page">
        <Header />

        <DrePageHero
            v-if="d.hero"
            :title="heroTitle"
            :background-image="heroBg"
            align="left"
            :crumbs="detailCrumbs"
            :show-carousel-arrows="true"
        />

        <main class="dre-property-detail">
            <div v-if="loading" class="dre-loading-spinner">
                <div class="dre-loading-spinner__ring" role="status" aria-label="Loading" />
            </div>

            <div v-else-if="!property" class="dre-property-detail__empty">
                <p>Property not found.</p>
            </div>

            <template v-else>
                <section class="dre-gallery">
                    <div class="dre-gallery__rail">
                        <button
                            v-for="(img, i) in galleryImages"
                            :key="`${img}-${i}`"
                            type="button"
                            class="dre-gallery__thumb"
                            :class="{ 'is-active': i === galleryIndex }"
                            @click="setHeroImage(img, i)"
                        >
                            <img :src="img" :alt="`${property.title} image ${i + 1}`" @error="dreOnPropertyImgError">
                        </button>
                    </div>

                    <div class="dre-gallery__stage">
                        <img :src="currentImage" :alt="property.title" @error="dreOnPropertyImgError">
                    </div>

                    <div class="dre-gallery__stack">
                        <article
                            v-for="(img, i) in galleryImages.slice(1, 3)"
                            :key="`${img}-stack-${i}`"
                            class="dre-gallery__mini"
                        >
                            <img :src="img" :alt="`${property.title} preview ${i + 2}`" @error="dreOnPropertyImgError">
                        </article>
                    </div>
                </section>

                <section class="dre-summary">
                    <div class="dre-summary__main">
                        <div class="dre-summary__eyebrow">
                            <span class="dre-chip">{{ listingLabel }}</span>
                            <span class="dre-summary__type">{{ propertyTypeLabel }}</span>
                        </div>

                        <h2 class="dre-summary__title">{{ property.title }}</h2>

                        <p class="dre-summary__location">
                            <span class="dre-summary__pin" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s6-4.35 6-10a6 6 0 10-12 0c0 5.65 6 10 6 10z" />
                                    <circle cx="12" cy="11" r="2.5" stroke-width="1.8" />
                                </svg>
                            </span>
                            {{ property.location }}
                        </p>

                        <div class="dre-summary__facts">
                            <div v-for="fact in factCards" :key="fact.label" class="dre-summary__fact">
                                <span class="dre-summary__fact-label">{{ fact.label }}</span>
                                <strong class="dre-summary__fact-value">{{ fact.value }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="dre-summary__side">
                        <div class="dre-price-card">
                            <p class="dre-price-card__label">Starting Price</p>
                            <p class="dre-price-card__value">{{ formatPrice(property.price) }}</p>
                            <p class="dre-price-card__period">{{ property.period || 'One-time purchase' }}</p>
                        </div>
                    </div>
                </section>

                <section class="dre-detail-layout">
                    <div class="dre-detail-layout__main">
                        <article class="dre-panel">
                            <div class="dre-panel__head">
                                <h3>Description</h3>
                            </div>
                            <div class="dre-richtext" v-html="property.description || 'No description available yet.'"></div>
                        </article>

                        <article v-if="accessPlaces.length" class="dre-panel">
                            <div class="dre-panel__head">
                                <h3>{{ property.easy_access_from_nearby ? 'Nearby' : 'Easy Access To' }}</h3>
                            </div>
                            <div class="dre-access-grid">
                                <div v-for="place in accessPlaces" :key="place.id" class="dre-access-card">
                                    <div class="dre-access-card__icon">
                                        <img v-if="place.icon" :src="place.icon" :alt="place.name">
                                        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s6-4.35 6-10a6 6 0 10-12 0c0 5.65 6 10 6 10z" />
                                            <circle cx="12" cy="11" r="2.5" stroke-width="1.8" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="dre-access-card__name">{{ place.name }}</p>
                                        <p class="dre-access-card__meta">{{ place.distance }}</p>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <article v-if="detailCards.length" class="dre-panel">
                            <div class="dre-panel__head">
                                <h3>Property Details</h3>
                            </div>
                            <div class="dre-details-grid">
                                <div v-for="item in detailCards" :key="item.id" class="dre-detail-card">
                                    <p class="dre-detail-card__label">{{ item.label }}</p>
                                    <p class="dre-detail-card__value">{{ item.value }}</p>
                                </div>
                            </div>
                        </article>

                        <article v-if="featureList.length" class="dre-panel">
                            <div class="dre-panel__head">
                                <h3>Key Features</h3>
                            </div>
                            <ul class="dre-feature-list">
                                <li v-for="feature in featureList" :key="feature">{{ feature }}</li>
                            </ul>
                        </article>

                        <article v-if="amenityCards.length" class="dre-panel">
                            <div class="dre-panel__head">
                                <h3>Amenities</h3>
                            </div>
                            <div class="dre-amenities-grid">
                                <div v-for="amenity in amenityCards" :key="amenity.id" class="dre-amenity-card">
                                    <span class="dre-amenity-card__badge" aria-hidden="true">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0L3.293 9.207a1 1 0 011.414-1.414l4.043 4.043 6.543-6.543a1 1 0 011.414 0z" />
                                        </svg>
                                    </span>
                                    <span>{{ amenity.label }}</span>
                                </div>
                            </div>
                        </article>

                        <PropertyDetailMap
                            v-if="hasMapCenter"
                            :lat="Number(property.latitude)"
                            :lng="Number(property.longitude)"
                            :title="property.title"
                            :price-display="mapPopupPriceDisplay"
                            :baths="Number(property.baths || property.bathrooms || 0)"
                            :sqft="Number(property.sqft || 0)"
                            :thumb="galleryImages[0] || DRE_PROPERTY_PLACEHOLDER_IMAGE"
                            :nearby-places="nearbyPlacesForMap"
                            :external-map-url="mapUrl"
                            :placeholder-image="DRE_PROPERTY_PLACEHOLDER_IMAGE"
                        />
                    </div>

                    <aside class="dre-detail-layout__side">
                        <article class="dre-sidebar-card">
                            <div class="dre-sidebar-card__head">
                                <div class="dre-agent-avatar">
                                    <img
                                        v-if="property.agent?.image"
                                        :src="property.agent.image"
                                        :alt="property.agent?.name || 'Agent'"
                                        @error="dreOnPropertyImgError"
                                    >
                                    <span v-else>{{ agentInitials }}</span>
                                </div>
                                <div>
                                    <p class="dre-sidebar-card__eyebrow">Agent</p>
                                    <h3>{{ property.agent?.name || 'Sales Team' }}</h3>
                                    <p class="dre-sidebar-card__role">{{ property.agent?.designation || 'Property Consultant' }}</p>
                                </div>
                            </div>

                            <div class="dre-contact-actions">
                                <a :href="property.agent?.phone || property.phone || '#'" class="dre-contact-actions__primary">Call Now</a>
                                <a :href="property.agent?.whatsapp || property.whatsapp || '#'" target="_blank" rel="noopener noreferrer" class="dre-contact-actions__ghost">WhatsApp</a>
                                <a :href="property.inquireUrl || '#'" class="dre-contact-actions__muted">Email Inquiry</a>
                            </div>
                        </article>

                        <MortgageCalculator :property-price="Number(property.price)" />
                    </aside>
                </section>

                <section v-if="similar.length" class="dre-similar">
                    <div class="dre-panel__head">
                        <h3>Similar Listing</h3>
                        <a href="/properties">View All</a>
                    </div>
                    <div class="dre-similar__grid">
                        <article v-for="p in similar" :key="p.id" class="property-card">
                            <div class="property-card__inner">
                                <div class="property-card__media">
                                    <img :src="similarImage(p)" :alt="p.title || 'Property'" @error="dreOnPropertyImgError">
                                </div>
                                <div class="property-card__body">
                                    <h3 class="property-card__title">{{ p.title || 'Property' }}</h3>
                                    <p class="property-card__location">{{ p.location || 'Dubai, UAE' }}</p>
                                    <a class="property-btn property-btn--primary" :href="similarUrl(p)">View Details</a>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            </template>
        </main>

        <Footer />
        <button type="button" class="dre-chat-fab dre-chat-fab--properties">Chat with us</button>
    </div>
</template>

<style scoped>
.dre-property-detail {
    max-width: 1240px;
    margin: 0 auto;
    padding: 26px 20px 80px;
    color: #10233f;
}

.dre-property-detail__empty {
    padding: 80px 0;
    text-align: center;
    color: #64748b;
}

.dre-gallery {
    display: grid;
    grid-template-columns: 120px minmax(0, 1.8fr) minmax(280px, 1fr);
    gap: 16px;
    margin-top: -54px;
    position: relative;
    z-index: 2;
}

.dre-gallery__rail,
.dre-gallery__stack {
    display: grid;
    gap: 12px;
}

.dre-gallery__thumb,
.dre-gallery__mini,
.dre-gallery__stage {
    border: 1px solid #d8e1ef;
    border-radius: 24px;
    overflow: hidden;
    background: #f8fbff;
    box-shadow: 0 20px 42px rgba(17, 38, 70, 0.08);
}

.dre-gallery__thumb {
    padding: 0;
    background: transparent;
    cursor: pointer;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}

.dre-gallery__thumb.is-active {
    border-color: #3058b8;
    transform: translateY(-2px);
    box-shadow: 0 18px 36px rgba(48, 88, 184, 0.2);
}

.dre-gallery__thumb img,
.dre-gallery__mini img,
.dre-gallery__stage img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.dre-gallery__thumb {
    height: 110px;
}

.dre-gallery__stage {
    min-height: 420px;
}

.dre-gallery__mini {
    min-height: 202px;
}

.dre-summary {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 250px;
    gap: 20px;
    margin-top: 22px;
    margin-bottom: 28px;
    align-items: start;
}

.dre-summary__main,
.dre-price-card {
    background: #fff;
    border: 1px solid #e3eaf5;
    border-radius: 24px;
    box-shadow: 0 18px 40px rgba(13, 35, 67, 0.07);
}

.dre-summary__main {
    padding: 26px 28px;
}

.dre-summary__eyebrow {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.dre-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 32px;
    padding: 0 14px;
    border-radius: 999px;
    background: #edf4ff;
    color: #2447a8;
    font-size: 13px;
    font-weight: 700;
}

.dre-summary__type {
    color: #6a7a93;
    font-size: 14px;
    font-weight: 600;
}

.dre-summary__title {
    margin: 0 0 10px;
    font-size: clamp(28px, 4vw, 40px);
    line-height: 1.1;
    color: #12284c;
}

.dre-summary__location {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 20px;
    color: #6b7b92;
    font-size: 15px;
}

.dre-summary__pin {
    width: 18px;
    height: 18px;
    color: #4c6fc7;
}

.dre-summary__pin svg {
    width: 100%;
    height: 100%;
}

.dre-summary__facts {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}

.dre-summary__fact {
    padding: 16px 14px;
    border-radius: 18px;
    background: #f7faff;
    border: 1px solid #e4ebf5;
}

.dre-summary__fact-label {
    display: block;
    margin-bottom: 6px;
    color: #7a879c;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.dre-summary__fact-value {
    font-size: 16px;
    color: #10233f;
}

.dre-price-card {
    padding: 24px 22px;
    min-height: 100%;
}

.dre-price-card__label,
.dre-price-card__period {
    margin: 0;
    color: #77849a;
    font-size: 13px;
}

.dre-price-card__value {
    margin: 10px 0 6px;
    font-size: 34px;
    line-height: 1.05;
    font-weight: 800;
    color: #1e4eb4;
}

.dre-detail-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 28px;
    align-items: start;
}

.dre-detail-layout__main,
.dre-detail-layout__side {
    display: grid;
    gap: 22px;
}

.dre-panel,
.dre-sidebar-card {
    background: #fff;
    border: 1px solid #e3eaf5;
    border-radius: 26px;
    padding: 24px;
    box-shadow: 0 18px 38px rgba(17, 38, 70, 0.06);
}

.dre-panel__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 18px;
}

.dre-panel__head h3 {
    margin: 0;
    font-size: 20px;
    color: #12284c;
}

.dre-panel__head a {
    color: #2d57b6;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
}

.dre-richtext {
    color: #607088;
    line-height: 1.75;
    font-size: 15px;
}

.dre-richtext :deep(p:first-child) {
    margin-top: 0;
}

.dre-richtext :deep(p:last-child) {
    margin-bottom: 0;
}

.dre-access-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.dre-access-card,
.dre-detail-card,
.dre-amenity-card {
    border: 1px solid #e4ebf5;
    background: #f8fbff;
    border-radius: 18px;
}

.dre-access-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
}

.dre-access-card__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: #eaf1ff;
    color: #2e56b2;
    flex-shrink: 0;
}

.dre-access-card__icon svg,
.dre-access-card__icon img {
    width: 22px;
    height: 22px;
    object-fit: contain;
}

.dre-access-card__name {
    margin: 0 0 3px;
    font-weight: 700;
    color: #12284c;
}

.dre-access-card__meta {
    margin: 0;
    font-size: 13px;
    color: #73829b;
}

.dre-details-grid,
.dre-amenities-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}

.dre-detail-card {
    padding: 18px 16px;
    min-height: 106px;
}

.dre-detail-card__label {
    margin: 0 0 8px;
    color: #74829a;
    font-size: 13px;
    text-transform: capitalize;
}

.dre-detail-card__value {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #10233f;
    line-height: 1.45;
}

.dre-feature-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px 18px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.dre-feature-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    color: #55657d;
}

.dre-feature-list li::before {
    content: '';
    width: 8px;
    height: 8px;
    margin-top: 8px;
    border-radius: 999px;
    background: #2750b0;
    flex-shrink: 0;
}

.dre-amenity-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 14px;
    color: #12284c;
    font-weight: 600;
}

.dre-amenity-card__badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: #eaf1ff;
    color: #2d57b6;
    flex-shrink: 0;
}

.dre-amenity-card__badge svg {
    width: 18px;
    height: 18px;
}

.dre-sidebar-card__head {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 18px;
}

.dre-agent-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 68px;
    height: 68px;
    border-radius: 20px;
    overflow: hidden;
    background: linear-gradient(135deg, #d9e5ff, #f4f8ff);
    color: #2349aa;
    font-weight: 800;
    font-size: 24px;
    flex-shrink: 0;
}

.dre-agent-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.dre-sidebar-card__eyebrow,
.dre-sidebar-card__role {
    margin: 0;
    color: #7c889d;
    font-size: 13px;
}

.dre-sidebar-card h3 {
    margin: 2px 0 4px;
    font-size: 22px;
    color: #12284c;
}

.dre-contact-actions {
    display: grid;
    gap: 10px;
}

.dre-contact-actions a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    border-radius: 14px;
    text-decoration: none;
    font-weight: 700;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.dre-contact-actions a:hover {
    transform: translateY(-1px);
}

.dre-contact-actions__primary {
    background: #2750b0;
    color: #fff;
    box-shadow: 0 14px 24px rgba(39, 80, 176, 0.22);
}

.dre-contact-actions__ghost {
    border: 1px solid #cfe0ff;
    color: #2750b0;
    background: #f8fbff;
}

.dre-contact-actions__muted {
    border: 1px solid #e2e8f2;
    color: #5f6f88;
    background: #fff;
}

.dre-similar {
    margin-top: 34px;
}

.dre-similar__grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
}

@media (max-width: 1200px) {
    .dre-gallery {
        grid-template-columns: 96px minmax(0, 1fr) 280px;
    }

    .dre-summary__facts,
    .dre-details-grid,
    .dre-amenities-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dre-similar__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 1024px) {
    .dre-gallery {
        grid-template-columns: 1fr;
        margin-top: -36px;
    }

    .dre-gallery__rail {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        order: 2;
    }

    .dre-gallery__stack {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dre-summary,
    .dre-detail-layout {
        grid-template-columns: 1fr;
    }

    .dre-access-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767px) {
    .dre-property-detail {
        padding-inline: 14px;
        padding-bottom: 56px;
    }

    .dre-gallery__stage {
        min-height: 280px;
    }

    .dre-gallery__thumb,
    .dre-gallery__mini {
        min-height: 88px;
        height: 88px;
    }

    .dre-gallery__rail,
    .dre-gallery__stack,
    .dre-summary__facts,
    .dre-access-grid,
    .dre-details-grid,
    .dre-amenities-grid,
    .dre-feature-list,
    .dre-similar__grid {
        grid-template-columns: 1fr;
    }

    .dre-summary__main,
    .dre-price-card,
    .dre-panel,
    .dre-sidebar-card {
        padding: 18px;
        border-radius: 20px;
    }

    .dre-summary__title {
        font-size: 28px;
    }

    .dre-price-card__value {
        font-size: 28px;
    }
}
</style>
