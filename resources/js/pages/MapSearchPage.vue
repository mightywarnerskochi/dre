<script setup>
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import { computed, onMounted, reactive, ref } from 'vue';
import FollowSocial from '../components/home/FollowSocial.vue';
import SiteFooter from '../components/home/SiteFooter.vue';
import SiteHeader from '../components/home/SiteHeader.vue';
import { dreOnPropertyImgError, DRE_PROPERTY_PLACEHOLDER_IMAGE } from '../utils/propertyImages';

const props = defineProps({
    pageData: {
        type: Object,
        default: () => ({}),
    },
});

const d = props.pageData || {};
const filters = computed(() => d.search?.filters || []);
const filterValues = reactive({});
const properties = ref([]);
const selectedProperty = ref(null);
const selectedCardImage = computed(() => {
    const p = selectedProperty.value;
    if (!p) return DRE_PROPERTY_PLACEHOLDER_IMAGE;
    const u = p.image;
    return typeof u === 'string' && u.trim() !== '' ? u : DRE_PROPERTY_PLACEHOLDER_IMAGE;
});
const loading = ref(false);
const mapEl = ref(null);
const map = ref(null);
const markerLayer = ref(null);
let fetchTimer = null;
let suppressNextMoveFetch = false;
let shouldAutoFitResults = false;

function formatMoney(value, currency = 'AED') {
    const amount = Number(value || 0);
    if (!Number.isFinite(amount)) return `${currency} 0`;
    return `${currency} ${amount.toLocaleString()}`;
}

function initFilterValues() {
    const params = new URLSearchParams(window.location.search);
    filters.value.forEach((filter) => {
        const key = String(filter.key || '');
        const queryParam = String(filter.queryParam || '');
        if (!key) return;
        filterValues[key] = queryParam ? (params.get(queryParam) ?? '') : '';
    });
}

function tileConfig() {
    return {
        url: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
        options: {
            maxZoom: 19,
            subdomains: 'abcd',
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        },
    };
}

function markerIcon(priceLabel, active = false) {
    return L.divIcon({
        className: 'dre-map-price-pin-wrap',
        html: `<button class="dre-map-price-pin ${active ? 'is-active' : ''}" type="button">${priceLabel}</button>`,
        iconSize: [84, 28],
        iconAnchor: [42, 14],
    });
}

function buildRequestParams(withBounds = true) {
    const params = {
        lang: String(d.map?.locale || document.documentElement.lang || 'en'),
    };

    filters.value.forEach((filter) => {
        const key = String(filter.key || '');
        const qp = String(filter.queryParam || '');
        if (!key || !qp) return;
        const value = String(filterValues[key] ?? '').trim();
        if (value !== '') {
            params[qp] = value;
        }
    });

    if (withBounds && map.value) {
        const bounds = map.value.getBounds();
        params.north = bounds.getNorth();
        params.south = bounds.getSouth();
        params.east = bounds.getEast();
        params.west = bounds.getWest();
    }

    return params;
}

function syncUrlFromFilters() {
    const url = new URL(window.location.href);
    filters.value.forEach((filter) => {
        const key = String(filter.key || '');
        const qp = String(filter.queryParam || '');
        if (!key || !qp) return;
        url.searchParams.delete(qp);
        const value = String(filterValues[key] ?? '').trim();
        if (value !== '') {
            url.searchParams.set(qp, value);
        }
    });
    window.history.replaceState({}, '', `${url.pathname}${url.search}`);
}

function drawMarkers(list) {
    if (!markerLayer.value || !map.value) return;
    markerLayer.value.clearLayers();

    const bounds = [];
    list.forEach((item) => {
        const lat = Number(item.latitude);
        const lng = Number(item.longitude);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

        const latLng = L.latLng(lat, lng);
        bounds.push(latLng);
        const marker = L.marker(latLng, {
            icon: markerIcon(item.priceLabel || 'From -', selectedProperty.value?.id === item.id),
        });

        marker.on('click', () => {
            selectedProperty.value = item;
            map.value.setView(latLng, Math.max(map.value.getZoom(), 15), { animate: true });
            drawMarkers(properties.value);
        });

        marker.addTo(markerLayer.value);
    });

    if (bounds.length && !selectedProperty.value && shouldAutoFitResults) {
        suppressNextMoveFetch = true;
        map.value.fitBounds(L.latLngBounds(bounds), { padding: [36, 36], maxZoom: 14 });
        shouldAutoFitResults = false;
    }
}

async function loadProperties({ withBounds = true } = {}) {
    if (!d.map?.endpoint) return;

    loading.value = true;
    try {
        const response = await window.axios.get(d.map.endpoint, {
            params: buildRequestParams(withBounds),
        });
        properties.value = response.data?.properties || [];
        if (
            selectedProperty.value
            && !properties.value.find((item) => item.id === selectedProperty.value.id)
        ) {
            selectedProperty.value = null;
        }
        drawMarkers(properties.value);
    } finally {
        loading.value = false;
    }
}

async function onSubmitFilters() {
    selectedProperty.value = null;
    shouldAutoFitResults = true;
    syncUrlFromFilters();
    await loadProperties({ withBounds: true });
}

onMounted(async () => {
    initFilterValues();

    map.value = L.map(mapEl.value, {
        zoomControl: true,
        scrollWheelZoom: true,
    });
    markerLayer.value = L.layerGroup().addTo(map.value);

    const t = tileConfig();
    L.tileLayer(t.url, t.options).addTo(map.value);

    const center = d.map?.defaultCenter || { lat: 25.2048, lng: 55.2708, zoom: 11 };
    map.value.setView([Number(center.lat), Number(center.lng)], Number(center.zoom || 11));

    map.value.on('moveend zoomend', () => {
        if (suppressNextMoveFetch) {
            suppressNextMoveFetch = false;
            return;
        }
        if (fetchTimer) clearTimeout(fetchTimer);
        fetchTimer = setTimeout(() => {
            loadProperties({ withBounds: true });
        }, 350);
    });

    map.value.on('click', async () => {
        selectedProperty.value = null;
        shouldAutoFitResults = true;
        await loadProperties({ withBounds: true });
    });

    await loadProperties({ withBounds: true });
});
</script>

<template>
    <div class="dre-page dre-map-page">
        <SiteHeader
            v-if="d.site && d.header"
            variant="solid"
            :site="d.site"
            :header="d.header"
        />

        <section class="dre-map-hero">
            <div class="dre-map-hero__inner dre-container">
                <h1>{{ d.hero?.title || 'Our Properties' }}</h1>
                <p>{{ d.hero?.breadcrumb || 'Our Properties' }}</p>
            </div>
        </section>

        <section class="dre-map-controls">
            <div class="dre-container">
                <form class="dre-map-filters" @submit.prevent="onSubmitFilters">
                    <div v-for="filter in filters" :key="filter.key" class="dre-map-filters__field">
                        <select
                            v-if="filter.uiType === 'dropdown' || (filter.options && filter.options.length)"
                            v-model="filterValues[filter.key]"
                        >
                            <option v-for="opt in filter.options || []" :key="String(opt.value) + opt.label" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                        <input
                            v-else
                            v-model="filterValues[filter.key]"
                            :placeholder="filter.label"
                            :type="filter.uiType === 'integer' ? 'number' : 'text'"
                        >
                    </div>
                    <button type="submit" class="dre-btn dre-btn--primary">Search</button>
                </form>
            </div>
        </section>

        <section class="dre-map-stage">
            <div class="dre-map-stage__map" ref="mapEl" />
            <div v-if="loading" class="dre-map-stage__loading">Loading properties...</div>
            <article v-if="selectedProperty" class="dre-map-card">
                <img
                    :src="selectedCardImage"
                    :alt="selectedProperty.title"
                    @error="dreOnPropertyImgError"
                >
                <div class="dre-map-card__body">
                    <h3>{{ selectedProperty.title }}</h3>
                    <p>{{ selectedProperty.location }}</p>
                    <p class="dre-map-card__price">
                        {{ formatMoney(selectedProperty.price, selectedProperty.currency) }}
                    </p>
                    <p class="dre-map-card__meta">
                        {{ selectedProperty.bedrooms }} Bed • {{ selectedProperty.bathrooms }} Bath
                    </p>
                </div>
            </article>
        </section>

        <FollowSocial v-if="d.social" :social="d.social" />
        <SiteFooter v-if="d.footer" :footer="d.footer" />
    </div>
</template>
