<template>
    <section class="banner banner--page banner--page--listing position-relative" :aria-label="t('listing.pageHeaderAria')">
        <div class="banner--page__bg">
            <picture>
                <img :src="asset('public/images/inner-banner.jpg')" alt="Map banner" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">{{ t('map.heroTitle') }}</h1>
                <ol class="breadcrumb-minimal" :aria-label="t('listing.breadcrumbAria')">
                    <li>
                        <a href="/" :aria-label="t('listing.homeAria')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">{{ t('map.breadcrumbCurrent') }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="properties-listing">
        <div class="listing-search-strip">
            <div class="container-ctn">
                <div class="js-listing-search-home">
                    <PropertyFilters v-model="activeFilters" @search="onSearch" />
                </div>
            </div>
        </div>

        <div class="map-search-container position-relative">
            <div v-if="mapLoading" class="map-overlay-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div id="map-search-main" ref="mapEl" class="map-search-main-view"></div>
            
            <ListingMobileExtras />
        </div>
    </section>
</template>

<script setup>
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { asset } from '@/utils/asset';
import { getPublicSiteBoot } from '@/utils/publicSite';
import { siteTelHref, siteWhatsappHref } from '@/utils/siteContact';
import PropertyFilters from '@/components/properties/PropertyFilters.vue';
import ListingMobileExtras from '@/components/ListingMobileExtras.vue';

const { locale, t } = useI18n({ useScope: 'global' });

const mapEl = ref(null);
const mapLoading = ref(true);
const rawMarkers = ref([]);

const DUBAI_CENTER = [25.2048, 55.2708];
const DUBAI_ZOOM = 11;

const LEGACY_CITY_LOOKUP = {
    '2': 'Dubai',
    dubai: 'Dubai',
    '3': 'Abu Dhabi',
    abudhabi: 'Abu Dhabi',
    '4': 'Sharjah',
    sharjah: 'Sharjah',
};

function firstParam(params, keys) {
    for (const key of keys) {
        const allValues = params.getAll(key).map((value) => String(value || '').trim()).filter(Boolean);
        if (allValues.length > 0) {
            return allValues[0];
        }
    }
    return '';
}

function normalizeCount(value) {
    const raw = String(value || '').trim();
    if (raw === '') return '';
    if (/^studio$/i.test(raw)) return 'Studio';
    if (raw.endsWith('+')) return raw;

    const numeric = Number.parseInt(raw, 10);
    return Number.isFinite(numeric) && numeric >= 0 ? String(numeric) : '';
}

function resolveLegacyLocation(params) {
    const cityParam = firstParam(params, ['c', 'city']);
    if (!cityParam) return '';
    const normalized = cityParam.toLowerCase().replace(/\s+/g, '');
    return LEGACY_CITY_LOOKUP[normalized] || cityParam;
}

function parseInitialFilters() {
    const params = new URLSearchParams(window.location.search || '');
    const location = firstParam(params, ['location', 'q', 'query']) || resolveLegacyLocation(params);

    return {
        property_type: firstParam(params, ['property_type', 'type']),
        categories: firstParam(params, ['categories', 'category']),
        bedrooms: normalizeCount(firstParam(params, ['bedrooms', 'beds', 'bdr[]', 'bdr'])),
        bathrooms: normalizeCount(firstParam(params, ['bathrooms', 'baths', 'btr[]', 'btr'])),
        location,
        min_price: firstParam(params, ['min_price', 'price_min']),
        max_price: firstParam(params, ['max_price', 'price_max']),
    };
}

// Consolidate filter state (works in both SPA and non-router entries)
const activeFilters = ref(parseInitialFilters());

let mapInstance = null;
let markersLayer = null;
let viewportFetchTimer = null;
let suppressViewportFetch = false;

const GROUP_DECIMALS = 4;

function normalizeForApiCount(value) {
    const raw = String(value || '').trim();
    if (raw === '') return '';
    if (/^studio$/i.test(raw)) return '0';
    const numeric = Number.parseInt(raw, 10);
    return Number.isFinite(numeric) && numeric >= 0 ? String(numeric) : '';
}

function markerApiParams({ includeBounds = true } = {}) {
    const params = { lang: locale.value || 'en' };

    if (activeFilters.value.location) params.location = activeFilters.value.location;
    if (activeFilters.value.property_type) params.type = activeFilters.value.property_type;
    if (activeFilters.value.categories) params.category = activeFilters.value.categories;
    if (activeFilters.value.min_price) params.min_price = activeFilters.value.min_price;
    if (activeFilters.value.max_price) params.max_price = activeFilters.value.max_price;

    const beds = normalizeForApiCount(activeFilters.value.bedrooms);
    const baths = normalizeForApiCount(activeFilters.value.bathrooms);
    if (beds) params.beds = beds;
    if (baths) params.baths = baths;

    if (includeBounds && mapInstance) {
        const bounds = mapInstance.getBounds();
        params.lat_min = bounds.getSouth();
        params.lat_max = bounds.getNorth();
        params.lng_min = bounds.getWest();
        params.lng_max = bounds.getEast();
    }

    return params;
}

async function fetchMarkers({ fitResults = false, includeBounds = true } = {}) {
    mapLoading.value = true;
    try {
        const { data } = await window.axios.get('/api/properties/map-markers', {
            params: markerApiParams({ includeBounds })
        });
        rawMarkers.value = Array.isArray(data?.markers)
            ? data.markers
            : (Array.isArray(data) ? data : []);
        renderMarkers({ fitResults });
    } catch (e) {
        console.error('Failed to fetch map markers', e);
        rawMarkers.value = [];
    } finally {
        mapLoading.value = false;
    }
}

function renderMarkers({ fitResults = false } = {}) {
    if (!mapInstance || !markersLayer) return;
    markersLayer.clearLayers();

    const normalizedMarkers = rawMarkers.value
        .map((marker) => {
            const lat = Number(marker.latitude ?? marker.lat);
            const lng = Number(marker.longitude ?? marker.lng);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                return null;
            }
            return { ...marker, __lat: lat, __lng: lng };
        })
        .filter(Boolean);

    const groups = {};
    normalizedMarkers.forEach((marker) => {
        const lat = marker.__lat.toFixed(GROUP_DECIMALS);
        const lng = marker.__lng.toFixed(GROUP_DECIMALS);
        const key = `${lat},${lng}`;
        if (!groups[key]) groups[key] = [];
        groups[key].push(marker);
    });

    Object.entries(groups).forEach(([key, items]) => {
        const [lat, lng] = key.split(',').map(Number);
        const count = items.length;
        
        const icon = L.divIcon({
            className: 'custom-map-marker-wrap',
            html: `
                <div class="custom-map-marker ${count > 1 ? 'is-cluster' : ''}">
                    <span class="marker-price">${count > 1 ? count : formatPriceMarker(items[0].price)}</span>
                </div>
            `,
            iconSize: [60, 30],
            iconAnchor: [30, 30]
        });

        const marker = L.marker([lat, lng], { icon });
        marker.on('click', () => {
            openClusterPopup(marker, items);
        });
        markersLayer.addLayer(marker);
    });

    if (normalizedMarkers.length > 0 && fitResults) {
        const group = new L.featureGroup(markersLayer.getLayers());
        suppressViewportFetch = true;
        mapInstance.once('moveend', () => {
            suppressViewportFetch = false;
        });
        mapInstance.fitBounds(group.getBounds().pad(0.1));
    } else if (normalizedMarkers.length === 0 && !activeFilters.value.location) {
        mapInstance.setView(DUBAI_CENTER, DUBAI_ZOOM);
    }
}

function mapPopupSiteCtaHtml() {
    const site = getPublicSiteBoot();
    const tel = siteTelHref(site);
    const wa = siteWhatsappHref(site);
    if (!tel && !wa) return '';
    const call = tel
        ? `<a href="${tel}" class="map-popup-site-cta__btn map-popup-site-cta__btn--call">${t('home.rentals.card.callNow')}</a>`
        : '';
    const whatsapp = wa
        ? `<a href="${wa}" class="map-popup-site-cta__btn map-popup-site-cta__btn--wa" target="_blank" rel="noopener noreferrer">${t('home.rentals.card.whatsapp')}</a>`
        : '';
    return `<div class="map-popup-site-cta" dir="${isArabicLocale() ? 'rtl' : 'ltr'}">${call}${whatsapp}</div>`;
}

function openClusterPopup(marker, items) {
    const isMultiItem = items.length > 1;
    const popupMaxWidth = Math.max(300, Math.min(isMultiItem ? 420 : 360, window.innerWidth - 32));
    const popupDir = isArabicLocale() ? 'rtl' : 'ltr';
    const currencyLabel = popupCurrencyLabel();
    const content = `
        <div class="map-popup-list custom-scrollbar" dir="${popupDir}">
            ${items.map(item => `
                <div class="map-popup-item">
                    <a href="${item.url || (item.slug ? `/property-details/${item.slug}` : '#')}" class="d-flex text-decoration-none text-dark">
                        <img src="${item.thumbnail || item.featured_image || ''}" alt="Property image" class="popup-thumb me-2 rounded">
                        <div class="popup-info">
                            <div class="popup-title fw-bold small">${item.title}</div>
                            <div class="popup-price text-primary fw-bold small">${formatPriceMarker(item.price)} ${currencyLabel}</div>
                            <div class="popup-meta small text-muted">${popupMetaLine(item)}</div>
                        </div>
                    </a>
                </div>
            `).join('')}
            ${mapPopupSiteCtaHtml()}
        </div>
    `;
    marker.bindPopup(content, {
        maxWidth: popupMaxWidth,
        minWidth: Math.min(isMultiItem ? 320 : 300, popupMaxWidth),
        className: 'custom-leaflet-popup'
    }).openPopup();
}

function formatPriceMarker(price) {
    if (!price) return '';
    const n = Number(price);
    if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M';
    if (n >= 1000) return (n / 1000).toFixed(0) + 'K';
    return n.toString();
}

function isArabicLocale() {
    return String(locale.value || '').toLowerCase().startsWith('ar');
}

function popupCurrencyLabel() {
    return isArabicLocale() ? 'د.إ' : 'AED';
}

function popupMetaLine(item) {
    const beds = Number(item.bedrooms ?? item.beds ?? 0) || 0;
    const baths = Number(item.bathrooms ?? item.baths ?? 0) || 0;

    const bedLabel = beds === 1 ? t('map.bedShort') : t('map.bedsShort');
    const bathLabel = baths === 1 ? t('map.bathShort') : t('map.bathsShort');

    return `${beds} ${bedLabel} | ${baths} ${bathLabel}`;
}

function onSearch(submittedFilters = null) {
    if (submittedFilters && typeof submittedFilters === 'object') {
        activeFilters.value = {
            ...activeFilters.value,
            ...submittedFilters,
        };
    }
    fetchMarkers({ fitResults: true, includeBounds: false });
}

function onViewportChanged() {
    if (suppressViewportFetch) return;
    if (viewportFetchTimer) {
        window.clearTimeout(viewportFetchTimer);
    }
    viewportFetchTimer = window.setTimeout(() => {
        fetchMarkers();
    }, 220);
}

onMounted(() => {
    mapInstance = L.map(mapEl.value, {
        center: DUBAI_CENTER,
        zoom: DUBAI_ZOOM,
        zoomControl: false,
        attributionControl: false
    });

    L.control.zoom({ position: 'bottomright' }).addTo(mapInstance);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png').addTo(mapInstance);

    markersLayer = L.layerGroup().addTo(mapInstance);
    mapInstance.on('moveend', onViewportChanged);
    fetchMarkers();
});

onBeforeUnmount(() => {
    if (viewportFetchTimer) {
        window.clearTimeout(viewportFetchTimer);
        viewportFetchTimer = null;
    }
    if (mapInstance) {
        mapInstance.off('moveend', onViewportChanged);
        mapInstance.remove();
    }
});

watch(locale, fetchMarkers);
</script>

<style>
.map-search-main-view {
    height: 700px;
    width: 100%;
    z-index: 1;
}
.custom-map-marker {
    background: white;
    border: 2px solid #0d6efd;
    border-radius: 20px;
    padding: 2px 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    transition: all 0.2s;
}
.custom-map-marker:hover {
    transform: scale(1.1);
    z-index: 1000;
}
.custom-map-marker.is-cluster {
    background: #0d6efd;
    color: white;
    font-weight: bold;
}
.marker-price {
    font-size: 12px;
    font-weight: 600;
}
.map-popup-list {
    width: min(360px, calc(100vw - 56px));
    max-height: none;
    overflow: visible;
    padding-right: 0;
}
.map-popup-item {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}
.map-popup-site-cta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}
.map-popup-site-cta__btn {
    flex: 1;
    min-width: 7rem;
    text-align: center;
    font-size: 0.8125rem;
    font-weight: 500;
    padding: 6px 10px;
    border-radius: 8px;
    border: 1px solid #ced4da;
    background: #fff;
    color: #0d6efd;
    text-decoration: none;
}
.map-popup-site-cta__btn:hover {
    background: #f8f9fa;
    color: #0a58ca;
}
.map-popup-item > a {
    display: flex;
    gap: 12px;
    align-items: center;
}
.popup-thumb {
    width: 78px;
    height: 58px;
    object-fit: cover;
    flex-shrink: 0;
}
.popup-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.map-popup-list[dir="rtl"] .popup-info {
    text-align: right;
}
.popup-title {
    white-space: normal;
    word-break: break-word;
    line-height: 1.25;
}
.map-overlay-loading {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255,255,255,0.7);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.custom-leaflet-popup .leaflet-popup-content-wrapper {
    border-radius: 12px;
    padding: 5px;
}
.custom-leaflet-popup .leaflet-popup-content {
    margin: 10px;
}
</style>
