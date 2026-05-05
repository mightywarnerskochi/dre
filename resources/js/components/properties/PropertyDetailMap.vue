<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    lat: { type: Number, required: true },
    lng: { type: Number, required: true },
    title: { type: String, default: '' },
    priceDisplay: { type: String, default: '' },
    beds: { type: Number, default: 0 },
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

const TYPE_ORDER = ['school', 'hospital', 'restaurant', 'attraction'];

const TYPE_LABELS = {
    school: 'Schools',
    hospital: 'Hospitals',
    restaurant: 'Restaurants',
    attraction: 'Attractions',
};

const hasNearbyList = computed(() => Array.isArray(props.nearbyPlaces) && props.nearbyPlaces.length > 0);

const categoryTabs = computed(() =>
    TYPE_ORDER.map((type) => ({
        key: type,
        label: TYPE_LABELS[type] || type,
        count: props.nearbyPlaces.filter((p) => normalizePoiType(p) === type && hasCoords(p)).length,
    })),
);

const showCategoryFilters = computed(() => hasNearbyList.value);

function hasCoords(p) {
    const la = Number(p?.latitude);
    const ln = Number(p?.longitude);

    return Number.isFinite(la) && Number.isFinite(ln) && (Math.abs(la) > 1e-6 || Math.abs(ln) > 1e-6);
}

function coordsPlaces(list) {
    return (Array.isArray(list) ? list : []).filter(hasCoords);
}

const activeCategory = ref(TYPE_ORDER[0]);

/** Map CMS / legacy values onto tab keys so filters + icons stay in sync. */
function normalizePoiType(p) {
    const raw = String(p?.type ?? '')
        .trim()
        .toLowerCase()
        .replace(/\s+/g, '_');
    const syn = {
        education: 'school',
        schools: 'school',
        medical: 'hospital',
        hospitals: 'hospital',
        clinic: 'hospital',
        pharmacy: 'hospital',
        dining: 'restaurant',
        cafe: 'restaurant',
        coffee: 'restaurant',
        restaurants: 'restaurant',
        food: 'restaurant',
        landmark: 'attraction',
        attractions: 'attraction',
        park: 'attraction',
        recreation: 'attraction',
    };
    if (syn[raw]) {
        return syn[raw];
    }
    if (TYPE_ORDER.includes(raw)) {
        return raw;
    }

    return raw || 'other';
}

const visiblePois = computed(() => {
    const withCoords = coordsPlaces(props.nearbyPlaces);

    return withCoords.filter((p) => normalizePoiType(p) === activeCategory.value);
});

function esc(s) {
    return String(s ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/** CMS `type` → BEM modifier on `.property-detail-map-card__poi` (restaurant → `--food`). */
function poiModifier(type) {
    const t = String(type || '').toLowerCase();
    if (t === 'restaurant') return 'food';

    return ['school', 'hospital', 'attraction'].includes(t) ? t : 'attraction';
}

/** White glyphs inside coloured POI circles (same logic as static map card design). */
function poiGlyphSvg(mod) {
    if (mod === 'school') {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3 1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>';
    }
    if (mod === 'hospital') {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>';
    }
    if (mod === 'food') {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a2 2 0 01-2 2H6a2 2 0 01-2-2V8z"/><path d="M6 1v3"/></svg>';
    }
    return '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M12 2l2.4 7.4H22l-6 4.6 2.3 7L12 17.8 5.7 21 8 14 2 9.4h7.6L12 2z"/></svg>';
}

const HOUSE_SVG = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>`;

function propertySummaryPopupHtml() {
    const img = esc(props.thumb || props.placeholderImage);
    const ph = esc(props.placeholderImage);
    const title = esc(props.title);
    const price = esc(props.priceDisplay);
    const metaBits = [];
    if (Number(props.beds) > 0) {
        metaBits.push(`${props.beds} BD`);
    }
    if (Number.isFinite(Number(props.baths))) {
        metaBits.push(`${props.baths} BA`);
    }
    if (props.sqft) {
        metaBits.push(`${Number(props.sqft).toLocaleString('en-AE')} ft²`);
    }
    const meta = esc(metaBits.join('  '));

    return `
<div class="property-detail-map-card__popup-inner">
  <button type="button" class="property-detail-map-card__popup-close dre-vue-map-popup-close" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
  <img class="property-detail-map-card__popup-thumb" src="${img}" alt="" width="64" height="64" loading="lazy" onerror="this.onerror=null;this.src='${ph}'">
  <div class="property-detail-map-card__popup-body">
    <p class="property-detail-map-card__popup-title">${title}</p>
    <p class="property-detail-map-card__popup-price">${price}</p>
    <p class="property-detail-map-card__popup-meta">${meta}</p>
  </div>
</div>`.trim();
}

function poiPopupHtml(place) {
    const name = esc(place.name);
    const addr = esc(place.address || '');
    let dist = '';
    if (place.distance !== null && place.distance !== undefined && place.distance !== '') {
        dist = Number.isNaN(Number(place.distance))
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

const PIN_DOT_PX = 40;

/** Leaflet + string `html` can reuse one DOM node across markers — use fresh Elements per icon. */
function createPropertyDivIcon() {
    const pin = document.createElement('div');
    pin.className = 'property-detail-map-card__pin property-detail-map-card__pin--leaflet';
    pin.setAttribute('aria-hidden', 'true');
    const dot = document.createElement('span');
    dot.className = 'property-detail-map-card__pin-dot';
    dot.innerHTML = HOUSE_SVG;
    pin.appendChild(dot);

    return L.divIcon({
        className: 'dre-vue-map-marker-host',
        html: pin,
        iconSize: [PIN_DOT_PX, PIN_DOT_PX],
        iconAnchor: [PIN_DOT_PX / 2, PIN_DOT_PX / 2],
        popupAnchor: [0, -PIN_DOT_PX / 2 - 6],
    });
}

function createPoiDivIconForPlace(place) {
    const mod = poiModifier(activeCategory.value);
    const glyphHtml = poiGlyphSvg(mod);
    const label = (place.name || '').trim().slice(0, 28) || mod;
    const ariaLabel = place.name || (mod === 'food' ? TYPE_LABELS.restaurant : TYPE_LABELS[mod]) || 'Location';

    const wrap = document.createElement('div');
    wrap.className = `property-detail-map-card__poi property-detail-map-card__poi--${mod} property-detail-map-card__poi--leaflet`;
    wrap.setAttribute('data-poi-label', label);
    wrap.setAttribute('role', 'img');
    wrap.setAttribute('aria-label', ariaLabel);

    const glyph = document.createElement('span');
    glyph.className = 'property-detail-map-card__poi-glyph';
    glyph.innerHTML = glyphHtml;
    wrap.appendChild(glyph);

    return L.divIcon({
        className: 'dre-vue-map-marker-host',
        html: wrap,
        iconSize: [26, 26],
        iconAnchor: [13, 13],
    });
}

function onMapContainerClick(e) {
    if (e.target.closest('.dre-vue-map-popup-close')) {
        e.preventDefault();
        e.stopPropagation();
        map?.closePopup();
    }
}

function renderPois() {
    if (!map || !poiLayer) return;
    poiLayer.clearLayers();

    visiblePois.value.forEach((p) => {
        const m = L.marker([p.latitude, p.longitude], { icon: createPoiDivIconForPlace(p) });
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
        attributionControl: false,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd',
        maxZoom: 20,
    }).addTo(map);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd',
        maxZoom: 20,
    }).addTo(map);

    propertyMarker = L.marker([props.lat, props.lng], {
        icon: createPropertyDivIcon(),
        zIndexOffset: 1000,
    }).addTo(map);

    propertyMarker.bindPopup(propertySummaryPopupHtml(), {
        className: 'dre-map-leaflet-popup dre-map-leaflet-popup--property-card',
        closeButton: false,
        autoClose: false,
        closeOnClick: false,
        maxWidth: 340,
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
    () => [props.lat, props.lng, props.title, props.priceDisplay, props.thumb, props.beds, props.baths, props.sqft],
    () => {
        if (!map || !propertyMarker) return;
        propertyMarker.setLatLng([props.lat, props.lng]);
        propertyMarker.setPopupContent(propertySummaryPopupHtml());
        propertyMarker.openPopup();
        fitMap();
    },
);

watch(
    () => props.nearbyPlaces,
    (list) => {
        const keysWithPins = TYPE_ORDER.filter((type) =>
            coordsPlaces(Array.isArray(list) ? list : []).some((p) => normalizePoiType(p) === type),
        );
        if (!keysWithPins.includes(activeCategory.value)) {
            activeCategory.value = keysWithPins[0] || TYPE_ORDER[0];
        }
        renderPois();
        fitMap();
    },
    { deep: true, immediate: true },
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
    <div class="property-detail-map-card position-relative">
        <div class="property-detail-map-card__head">
            <p class="property-detail-map-card__title">Map</p>
        </div>

        <div class="property-detail-map-card__map-panels" aria-live="polite">
            <div
                id="dre-vue-property-map-panel"
                class="property-detail-map-card__map-panel"
                role="tabpanel"
                :aria-labelledby="`dre-vue-map-tab-${activeCategory}`"
            >
                <div class="property-detail-map-card__frame">
                    <div ref="mapRoot" class="property-detail-map-card__leaflet" role="application" aria-label="Property location map" />
                </div>
            </div>
        </div>

        <div v-if="showCategoryFilters" class="property-detail-map-card__tabs-outer">
            <div class="property-detail-map-card__tabs" role="tablist" aria-label="Nearby places">
                <button
                    v-for="tab in categoryTabs"
                    :key="tab.key"
                    type="button"
                    class="property-detail-map-card__tab"
                    :class="{ 'is-active': activeCategory === tab.key && tab.count > 0 }"
                    :disabled="tab.count === 0"
                    role="tab"
                    :id="`dre-vue-map-tab-${tab.key}`"
                    aria-controls="dre-vue-property-map-panel"
                    :aria-selected="activeCategory === tab.key && tab.count > 0"
                    :tabindex="activeCategory === tab.key ? 0 : -1"
                    @click="setCategory(tab.key)"
                >
                    {{ tab.label }}
                </button>
            </div>
        </div>

        <p v-if="hasNearbyList && !coordsPlaces(nearbyPlaces).length" class="property-detail-map-card__hint">
            Add latitude and longitude to nearby places in the CMS to show pins on the map.
        </p>
    </div>
</template>

<style scoped>
.property-detail-map-card__hint {
    margin: 0;
    padding: 10px 18px 16px;
    font-size: 13px;
    color: #73829b;
}

.property-detail-map-card__external-map {
    font-size: 14px;
    font-weight: 600;
    color: #2a559c;
    text-decoration: none;
    white-space: nowrap;
}

.property-detail-map-card__external-map:hover {
    text-decoration: underline;
}

:deep(.property-detail-map-card__leaflet) {
    width: 100%;
    min-height: min(420px, 55vh);
    z-index: 0;
}

:deep(.property-detail-map-card__leaflet .leaflet-container) {
    width: 100%;
    height: min(420px, 55vh);
    font-family: inherit;
}

:deep(.dre-vue-map-marker-host) {
    background: transparent !important;
    border: none !important;
}

:deep(.dre-map-leaflet-popup--property-card .leaflet-popup-content-wrapper) {
    padding: 0;
    background: transparent;
    border: none;
    box-shadow: none;
}

:deep(.dre-map-leaflet-popup--property-card .leaflet-popup-tip-container) {
    display: none;
}

:deep(.dre-map-leaflet-popup--property-card .leaflet-popup-content) {
    margin: 0;
    width: auto !important;
}

:deep(.dre-map-leaflet-popup--poi .leaflet-popup-content-wrapper) {
    border-radius: 12px;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 10px 28px rgba(15, 35, 70, 0.16);
}

:deep(.dre-map-leaflet-popup--poi .leaflet-popup-content) {
    margin: 0;
    padding: 12px 14px;
    font-size: 14px;
    line-height: 1.45;
    color: #10233f;
}

.property-detail-map-card__popup-inner {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 36px 12px 12px;
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e8ecf0;
    box-shadow: 0 8px 28px rgba(10, 20, 40, 0.12);
    min-width: 240px;
    max-width: 300px;
}

.property-detail-map-card__popup-inner .property-detail-map-card__popup-close {
    position: absolute;
    top: 6px;
    right: 6px;
    border: none;
    background: transparent;
    font-size: 22px;
    line-height: 1;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    z-index: 2;
    border-radius: 6px;
}

.property-detail-map-card__popup-inner .property-detail-map-card__popup-close:hover {
    color: #334155;
    background: #f4f6f9;
}
</style>

<style>
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
