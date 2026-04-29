<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    lat: { type: Number, required: true },
    lng: { type: Number, required: true },
    title: { type: String, default: '' },
    priceDisplay: { type: String, default: '' },
    baths: { type: Number, default: 0 },
    sqft: { type: Number, default: 0 },
    thumb: { type: String, default: '' },
    nearbyPlaces: { type: Array, default: () => [] },
    externalMapUrl: { type: String, default: '#' },
    placeholderImage: { type: String, default: '/images/dre/property-placeholder.svg' },
});

const mapRoot = ref(null);
let map = null;
let propertyMarker = null;
let poiLayer = null;

const activeCategory = ref('all');

const TYPE_ORDER = ['school', 'hospital', 'restaurant', 'attraction'];

const TYPE_LABELS = {
    school: 'Schools',
    hospital: 'Hospitals',
    restaurant: 'Restaurants',
    attraction: 'Attractions',
    other: 'Other',
};

const hasNearbyList = computed(() => Array.isArray(props.nearbyPlaces) && props.nearbyPlaces.length > 0);

const categoryTabs = computed(() => {
    const tabs = [{ key: 'all', label: 'All', count: coordsPlaces(props.nearbyPlaces).length }];

    TYPE_ORDER.forEach((type) => {
        const count = props.nearbyPlaces.filter(
            (p) => (p.type || 'other').toLowerCase() === type && hasCoords(p),
        ).length;
        tabs.push({
            key: type,
            label: TYPE_LABELS[type] || type,
            count,
        });
    });

    const extras = new Set(
        props.nearbyPlaces
            .map((p) => (p.type || 'other').toLowerCase())
            .filter((t) => t && !TYPE_ORDER.includes(t)),
    );
    extras.forEach((type) => {
        const count = props.nearbyPlaces.filter((p) => (p.type || '').toLowerCase() === type && hasCoords(p)).length;
        tabs.push({
            key: type,
            label: TYPE_LABELS[type] || type.charAt(0).toUpperCase() + type.slice(1),
            count,
        });
    });

    return tabs;
});

function hasCoords(p) {
    const la = Number(p?.latitude);
    const ln = Number(p?.longitude);

    return Number.isFinite(la) && Number.isFinite(ln) && (Math.abs(la) > 1e-6 || Math.abs(ln) > 1e-6);
}

function coordsPlaces(list) {
    return list.filter(hasCoords);
}

const visiblePois = computed(() => {
    const withCoords = coordsPlaces(props.nearbyPlaces);
    if (activeCategory.value === 'all') {
        return withCoords;
    }

    return withCoords.filter((p) => (p.type || 'other').toLowerCase() === activeCategory.value);
});

function esc(s) {
    return String(s ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function poiStyle(type) {
    const t = String(type || '').toLowerCase();
    if (t === 'restaurant') {
        return { bg: '#f4a261', label: 'R' };
    }
    if (t === 'hospital') {
        return { bg: '#e76f51', label: 'H' };
    }
    if (t === 'school') {
        return { bg: '#457b9d', label: 'S' };
    }
    if (t === 'attraction') {
        return { bg: '#2a559c', label: 'A' };
    }

    return { bg: '#868e96', label: '•' };
}

function propertyPopupHtml() {
    const img = esc(props.thumb || props.placeholderImage);
    const ph = esc(props.placeholderImage);
    const title = esc(props.title);
    const price = esc(props.priceDisplay);
    const metaBits = [];
    if (props.baths) {
        metaBits.push(`${props.baths} BA`);
    }
    if (props.sqft) {
        metaBits.push(`${Number(props.sqft).toLocaleString('en-AE')} ft²`);
    }
    const meta = esc(metaBits.join('  ·  '));

    return `
<div class="dre-map-infobox">
  <button type="button" class="dre-map-infobox__close" aria-label="Close">&times;</button>
  <div class="dre-map-infobox__row">
    <div class="dre-map-infobox__thumb"><img src="${img}" alt="" loading="lazy" onerror="this.onerror=null;this.src='${ph}'"></div>
    <div class="dre-map-infobox__body">
      <div class="dre-map-infobox__title">${title}</div>
      <div class="dre-map-infobox__price">${price}</div>
      <div class="dre-map-infobox__meta">${meta}</div>
    </div>
  </div>
</div>`.trim();
}

function poiPopupHtml(place) {
    const name = esc(place.name);
    const addr = esc(place.address || '');
    let dist = '';
    if (place.distance !== null && place.distance !== undefined && place.distance !== '') {
        dist = isNaN(Number(place.distance))
            ? esc(String(place.distance))
            : esc(`${place.distance} km`);
    }

    return `
<div class="dre-map-poi-popup">
  <strong>${name}</strong>
  ${addr ? `<div class="dre-map-poi-popup__addr">${addr}</div>` : ''}
  ${dist ? `<div class="dre-map-poi-popup__dist">${dist}</div>` : ''}
</div>`.trim();
}

const propertyDivIcon = L.divIcon({
    className: 'dre-map-property-marker-wrap',
    html: `<div class="dre-map-property-marker" aria-hidden="true">
    <svg viewBox="0 0 32 42" width="32" height="42" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M16 42s14-12.2 14-22a14 14 0 10-28 0c0 9.8 14 22 14 22z" fill="#c92a2a"/>
      <path d="M9 18L16 11l7 7v9H9v-9z" fill="#fff"/>
      <path d="M13 22h6v5h-6v-5z" fill="#c92a2a"/>
    </svg>
  </div>`,
    iconSize: [32, 42],
    iconAnchor: [16, 42],
    popupAnchor: [0, -36],
});

function onMapContainerClick(e) {
    if (e.target.closest('.dre-map-infobox__close')) {
        e.preventDefault();
        e.stopPropagation();
        map?.closePopup();
    }
}

function renderPois() {
    if (!map || !poiLayer) return;
    poiLayer.clearLayers();

    visiblePois.value.forEach((p) => {
        const st = poiStyle(p.type);
        const icon = L.divIcon({
            className: 'dre-poi-marker-wrap',
            html: `<div class="dre-poi-marker" style="background:${st.bg}"><span>${esc(st.label)}</span></div>`,
            iconSize: [34, 34],
            iconAnchor: [17, 17],
        });
        const m = L.marker([p.latitude, p.longitude], { icon });
        m.bindPopup(poiPopupHtml(p), {
            className: 'dre-map-leaflet-popup dre-map-leaflet-popup--poi',
            maxWidth: 260,
        });
        m.addTo(poiLayer);
    });
}

function fitMap() {
    if (!map) return;
    const pts = [[props.lat, props.lng]];
    visiblePois.value.forEach((p) => pts.push([p.latitude, p.longitude]));
    if (pts.length === 1) {
        map.setView([props.lat, props.lng], 14);

        return;
    }
    const b = L.latLngBounds(pts);
    map.fitBounds(b, { padding: [48, 48], maxZoom: 15 });
}

function initMap() {
    if (!mapRoot.value || map) return;

    map = L.map(mapRoot.value, {
        zoomControl: true,
        scrollWheelZoom: true,
        attributionControl: true,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20,
    }).addTo(map);

    propertyMarker = L.marker([props.lat, props.lng], {
        icon: propertyDivIcon,
        zIndexOffset: 1000,
    }).addTo(map);

    propertyMarker.bindPopup(propertyPopupHtml(), {
        className: 'dre-map-leaflet-popup dre-map-leaflet-popup--property',
        closeButton: false,
        autoClose: false,
        closeOnClick: false,
        maxWidth: 320,
    });

    map.getContainer().addEventListener('click', onMapContainerClick);

    propertyMarker.openPopup();

    poiLayer = L.layerGroup().addTo(map);
    renderPois();
    fitMap();
}

function destroyMap() {
    if (map) {
        map.getContainer()?.removeEventListener('click', onMapContainerClick);
        map.remove();
        map = null;
        propertyMarker = null;
        poiLayer = null;
    }
}

onMounted(() => {
    initMap();
});

onBeforeUnmount(() => {
    destroyMap();
});

watch(
    () => [props.lat, props.lng, props.title, props.priceDisplay, props.thumb, props.baths, props.sqft],
    () => {
        if (!map || !propertyMarker) return;
        propertyMarker.setLatLng([props.lat, props.lng]);
        propertyMarker.setPopupContent(propertyPopupHtml());
        propertyMarker.openPopup();
        fitMap();
    },
);

watch(
    () => props.nearbyPlaces,
    () => {
        renderPois();
        fitMap();
    },
    { deep: true },
);

watch(activeCategory, () => {
    renderPois();
    fitMap();
});

function setCategory(key) {
    activeCategory.value = key;
}
</script>

<template>
    <div class="dre-detail-map">
        <div class="dre-detail-map__head">
            <h3 class="dre-detail-map__title">Map</h3>
            <a
                v-if="externalMapUrl && externalMapUrl !== '#'"
                class="dre-detail-map__open"
                :href="externalMapUrl"
                target="_blank"
                rel="noopener noreferrer"
            >Open in Google Maps</a>
        </div>

        <div class="dre-detail-map__surface">
            <div ref="mapRoot" class="dre-detail-map__canvas" role="application" aria-label="Property location map" />

            <div v-if="hasNearbyList" class="dre-detail-map__filters" role="tablist" aria-label="Nearby categories">
                <button
                    v-for="tab in categoryTabs"
                    :key="tab.key"
                    type="button"
                    role="tab"
                    class="dre-detail-map__filter"
                    :class="{ 'is-active': activeCategory === tab.key }"
                    :aria-selected="activeCategory === tab.key"
                    @click="setCategory(tab.key)"
                >
                    {{ tab.label }}
                    <span v-if="tab.key !== 'all' && tab.count" class="dre-detail-map__filter-count">{{ tab.count }}</span>
                </button>
            </div>
        </div>

        <p v-if="hasNearbyList && !coordsPlaces(nearbyPlaces).length" class="dre-detail-map__hint">
            Add latitude and longitude to nearby places in the CMS to show pins on the map.
        </p>
    </div>
</template>

<style scoped>
.dre-detail-map {
    border: 1px solid #e3eaf5;
    border-radius: 26px;
    padding: 22px 22px 18px;
    background: #fff;
    box-shadow: 0 18px 38px rgba(17, 38, 70, 0.06);
}

.dre-detail-map__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 14px;
}

.dre-detail-map__title {
    margin: 0;
    font-size: 20px;
    color: #12284c;
}

.dre-detail-map__open {
    font-size: 14px;
    font-weight: 700;
    color: #2d57b6;
    text-decoration: none;
}

.dre-detail-map__open:hover {
    text-decoration: underline;
}

.dre-detail-map__surface {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid #e4ebf5;
    background: #f1f5fb;
}

.dre-detail-map__canvas {
    height: min(420px, 55vh);
    width: 100%;
    z-index: 1;
}

.dre-detail-map__filters {
    position: absolute;
    left: 50%;
    bottom: 16px;
    transform: translateX(-50%);
    z-index: 400;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 6px;
    padding: 8px 10px;
    background: rgba(255, 255, 255, 0.96);
    border-radius: 999px;
    box-shadow: 0 8px 28px rgba(15, 35, 70, 0.12);
    border: 1px solid #e8edf5;
    max-width: calc(100% - 24px);
}

.dre-detail-map__filter {
    border: none;
    background: transparent;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
    color: #10233f;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background 0.15s ease, color 0.15s ease;
}

.dre-detail-map__filter:hover {
    background: #f1f5fb;
}

.dre-detail-map__filter.is-active {
    background: #2a559c;
    color: #fff;
}

.dre-detail-map__filter-count {
    font-size: 11px;
    opacity: 0.85;
}

.dre-detail-map__hint {
    margin: 12px 0 0;
    font-size: 13px;
    color: #73829b;
}

:deep(.dre-map-property-marker-wrap),
:deep(.dre-poi-marker-wrap) {
    background: transparent !important;
    border: none !important;
}

:deep(.dre-map-property-marker) {
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

:deep(.dre-poi-marker) {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
    border: 2px solid #fff;
}

:deep(.dre-map-leaflet-popup .leaflet-popup-content-wrapper) {
    border-radius: 16px;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(15, 35, 70, 0.18);
}

:deep(.dre-map-leaflet-popup .leaflet-popup-content) {
    margin: 0;
    min-width: 0;
}

:deep(.dre-map-leaflet-popup--property .leaflet-popup-content) {
    padding: 0;
}

:deep(.dre-map-leaflet-popup--poi .leaflet-popup-content) {
    padding: 12px 14px;
    font-size: 14px;
    line-height: 1.45;
    color: #10233f;
}
</style>

<style>
.dre-map-infobox {
    position: relative;
    padding: 14px 16px;
    min-width: 240px;
    max-width: 300px;
}

.dre-map-infobox__close {
    position: absolute;
    top: 8px;
    right: 10px;
    border: none;
    background: transparent;
    font-size: 22px;
    line-height: 1;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    z-index: 2;
}

.dre-map-infobox__close:hover {
    color: #334155;
}

.dre-map-infobox__row {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding-right: 18px;
}

.dre-map-infobox__thumb {
    width: 72px;
    height: 72px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    background: #eef2f7;
}

.dre-map-infobox__thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.dre-map-infobox__title {
    font-weight: 800;
    font-size: 15px;
    color: #10233f;
    line-height: 1.25;
    margin-bottom: 4px;
}

.dre-map-infobox__price {
    font-weight: 700;
    font-size: 15px;
    color: #2a559c;
    margin-bottom: 4px;
}

.dre-map-infobox__meta {
    font-size: 12px;
    color: #64748b;
}

.dre-map-poi-popup__addr {
    margin-top: 6px;
    font-size: 13px;
    color: #64748b;
}

.dre-map-poi-popup__dist {
    margin-top: 4px;
    font-size: 12px;
    color: #2a559c;
    font-weight: 600;
}
</style>
