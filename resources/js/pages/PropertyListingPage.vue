<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import DrePageHero from '../components/layout/DrePageHero.vue';
import PropertyFilters from '../components/properties/PropertyFilters.vue';
import Footer from '../components/Footer.vue';
import Header from '../components/Header.vue';
import { dreOnPropertyImgError, DRE_PROPERTY_PLACEHOLDER_IMAGE } from '../utils/propertyImages';

const props = defineProps({
    pageData: { type: Object, default: () => ({}) },
});

const { locale } = useI18n({ useScope: 'global' });
const d = props.pageData;
const properties = ref([]);
const pagination = ref({});
const loading = ref(false);

const filterValues = reactive({
    page: 1,
    location: '',
    property_type: '',
    bedrooms: '',
    price: '',
});

const listingCrumbs = [
    { label: 'Home', href: '/' },
    { label: 'Our Properties' },
];

const heroImage = computed(() => d.hero?.backgroundImage || '/images/dre/hero-dubai.jpg');

function buildQueryParams(extra = {}) {
    const filters = d.search?.filters || [];
    const q = { page: filterValues.page || 1, lang: locale.value || 'en', ...extra };
    for (const f of filters) {
        const val = filterValues[f.key];
        if (val === '' || val === null || val === undefined) {
            continue;
        }
        const qp = f.queryParam || f.key;
        q[qp] = val;
    }
    return q;
}

async function fetchProperties(extra = {}) {
    loading.value = true;
    try {
        const response = await window.axios.get('/api/properties', {
            params: buildQueryParams(extra),
        });
        properties.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            total: response.data.total,
        };
    } catch (error) {
        console.error('Error fetching properties:', error);
    } finally {
        loading.value = false;
    }
}

function handleSearch(newFilters) {
    Object.assign(filterValues, newFilters);
    filterValues.page = 1;
    fetchProperties();
}

function goToPage(page) {
    filterValues.page = page;
    fetchProperties();
    window.scrollTo({ top: 280, behavior: 'smooth' });
}

const pageNumbers = computed(() => {
    const last = pagination.value.last_page || 1;
    const cur = pagination.value.current_page || 1;
    if (last <= 15) {
        return Array.from({ length: last }, (_, i) => i + 1);
    }
    const delta = 2;
    const start = Math.max(1, cur - delta);
    const end = Math.min(last, cur + delta);
    const nums = [];
    if (start > 1) {
        nums.push(1);
        if (start > 2) nums.push('…');
    }
    for (let i = start; i <= end; i++) nums.push(i);
    if (end < last) {
        if (end < last - 1) nums.push('…');
        nums.push(last);
    }
    return nums;
});

function pageButtonClick(i) {
    if (typeof i !== 'number') return;
    goToPage(i);
}

onMounted(() => {
    fetchProperties();
});

watch(locale, () => {
    filterValues.page = 1;
    fetchProperties();
});

function propertyImage(property) {
    const list = Array.isArray(property?.images) ? property.images : [];
    const first = list.find((item) => typeof item === 'string' && item.trim() !== '');
    return first || property?.image || DRE_PROPERTY_PLACEHOLDER_IMAGE;
}

function propertyUrl(property) {
    if (typeof property?.url === 'string' && property.url.trim() !== '') return property.url;
    if (property?.slug) return `/property-details/${property.slug}`;
    if (property?.id) return `/property-details/${property.id}`;
    return '#';
}

function propertyCardDataPropertyUrl(property) {
    const u = propertyUrl(property);
    return u && u !== '#' ? u : null;
}

function formatPrice(value) {
    const number = Number(value || 0);
    return Number.isFinite(number) ? number.toLocaleString(locale.value === 'ar' ? 'ar-AE' : 'en-AE') : '0';
}
</script>

<template>
    <div class="dre-page dre-properties-figma-page">
        <Header />

        <DrePageHero
            v-if="d.hero"
            :title="d.hero.title || 'Our Properties'"
            :background-image="heroImage"
            align="center"
            :crumbs="listingCrumbs"
            :show-carousel-arrows="true"
        />

        <PropertyFilters
            v-if="d.search?.filters?.length"
            :filters="d.search.filters"
            :initial-values="filterValues"
            @search="handleSearch"
        />

        <main class="dre-properties-main">
            <div class="dre-listing-toolbar">
                <h2 class="dre-listing-toolbar__title">Explore Our Properties</h2>
                <div class="dre-listing-toolbar__views" role="group" aria-label="View mode">
                    <a
                        href="/map-search"
                        class="dre-listing-toolbar__view-btn dre-listing-toolbar__map-link"
                        title="Map view"
                        aria-label="Open map search"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                            <circle cx="12" cy="9" r="2.2" />
                        </svg>
                    </a>
                    <button type="button" class="dre-listing-toolbar__view-btn" title="List view" aria-label="List view">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 6h16M4 12h16M4 18h10" stroke-linecap="round" />
                        </svg>
                    </button>
                    <button type="button" class="dre-listing-toolbar__view-btn is-active" title="Grid view" aria-label="Grid view">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                    </button>
                </div>
            </div>

            <div v-if="loading" class="dre-loading-spinner">
                <div class="dre-loading-spinner__ring" role="status" aria-label="Loading" />
            </div>

            <div v-else-if="properties.length === 0" class="dre-loading-spinner">
                <p style="margin: 0; color: var(--dre-text-muted); font-size: 1.05rem">
                    No properties found matching your criteria.
                </p>
            </div>

            <div v-else class="dre-property-grid-figma">
                <article
                    v-for="p in properties"
                    :key="p.id"
                    class="property-card"
                    :data-property-url="propertyCardDataPropertyUrl(p)"
                >
                    <div class="property-card__ghost" aria-hidden="true"></div>
                    <div class="property-card__inner">
                        <div class="property-card__media">
                            <span v-if="p.isFeatured || p.is_featured" class="property-card__badge">Featured</span>
                            <img :src="propertyImage(p)" :alt="p.title || 'Property'" loading="lazy" @error="dreOnPropertyImgError">
                        </div>
                        <div class="property-card__body">
                            <h3 class="property-card__title">{{ p.title || 'Property' }}</h3>
                            <p class="property-card__location">{{ p.location || 'Dubai, UAE' }}</p>
                            <div class="property-card__price">{{ formatPrice(p.price) }} AED</div>
                            <div class="property-card__actions">
                                <a class="property-btn property-btn--primary" :href="propertyUrl(p)">View Details</a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <div v-if="pagination.last_page > 1" class="dre-pagination-figma">
                <template v-for="(i, idx) in pageNumbers" :key="'p-' + idx">
                    <span v-if="i === '…'" class="dre-pagination-figma__ellipsis">…</span>
                    <button
                        v-else
                        type="button"
                        class="dre-pagination-figma__btn"
                        :class="{ 'is-current': i === pagination.current_page }"
                        @click="pageButtonClick(i)"
                    >
                        {{ i }}
                    </button>
                </template>
            </div>
        </main>

        <Footer />
        <button type="button" class="dre-chat-fab dre-chat-fab--properties">Chat with us</button>
    </div>
</template>
