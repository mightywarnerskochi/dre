<template>
    <div ref="mapContainer" class="property-detail-map-card__leaflet" role="presentation"></div>
</template>

<script setup>
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { asset } from '@/utils/asset';
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const TILE_URL = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';

const TYPE_MARKER_ICONS = {
    school: asset('public/images/properties-details/icons/fluent_building-32-regular.png'),
    hospital: asset('public/images/properties-details/icons/tabler_hours-24.png'),
    food: asset('public/images/properties-details/icons/icon-park-outline_shopping-mall.png'),
    attraction: asset('public/images/properties-details/icons/hugeicons_location-star-01.png'),
};

function escapeAttr(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

const props = defineProps({
    centerLat: { type: Number, required: true },
    centerLng: { type: Number, required: true },
    propertyTitle: { type: String, default: '' },
    /** Places for the active category only (with latitude & longitude when known). */
    places: {
        type: Array,
        default: () => [],
    },
});

const mapContainer = ref(null);

/** @type {import('leaflet').Map | null} */
let mapInstance = null;
/** @type {import('leaflet').LayerGroup | null} */
let markersLayer = null;
/** @type {ResizeObserver | null} */
let mapResizeObserver = null;

function escapeHtmlText(text) {
    return String(text ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function poiModifier(type) {
    const raw = String(type || '').toLowerCase();
    if (raw === 'school') return 'school';
    if (raw === 'hospital') return 'hospital';
    if (raw === 'restaurant') return 'food';
    if (raw === 'attraction') return 'attraction';
    return 'attraction';
}

function propertyMarkerHtml() {
    return `
        <div class="property-detail-map-card__pin property-detail-map-card__pin--leaflet">
            <span class="property-detail-map-card__pin-dot">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </div>
    `;
}

function nearbyMarkerHtml(type, iconUrl) {
    const mod = poiModifier(type);
    const fallback = TYPE_MARKER_ICONS[mod] || TYPE_MARKER_ICONS.attraction;
    const src = escapeAttr(iconUrl || fallback || '');
    return `
        <span class="property-detail-map-card__poi property-detail-map-card__poi--${mod} property-detail-map-card__poi--leaflet property-detail-map-card__poi--icon-marker">
            <img src="${src}" alt="" width="18" height="18" loading="lazy" decoding="async" />
        </span>
    `;
}

function placeTooltipContent(place, approximate = false) {
    const name = escapeHtmlText(place.name);
    const dist = place.distance !== undefined && place.distance !== null && String(place.distance).trim() !== ''
        ? escapeHtmlText(place.distance)
        : '';
    const approxNote = approximate ? '<br><span class="property-detail-map-tooltip__approx">Approx. position</span>' : '';
    if (!dist) {
        return `${name}${approxNote}`;
    }
    return `${name}<br><span class="property-detail-map-tooltip__meta">${dist}</span>${approxNote}`;
}

function tooltipOptions() {
    return {
        sticky: true,
        direction: 'top',
        opacity: 1,
        className: 'property-detail-map-tooltip',
    };
}

/**
 * CMS rows often have names/types but empty lat/lng. Spread pins in a small ring
 * around the listing so the map stays local until coordinates are saved.
 */
function effectiveLatLng(place, index, total, centerLat, centerLng) {
    const lat = Number(place?.latitude);
    const lng = Number(place?.longitude);
    if (Number.isFinite(lat) && Number.isFinite(lng)) {
        return { lat, lng, approximate: false };
    }

    const n = Math.max(Number(total) || 0, 1);
    const radius = 0.0045;
    const angle = (index / n) * Math.PI * 2;
    const stagger = (index % 5) * 0.00025;

    return {
        lat: centerLat + Math.cos(angle) * (radius + stagger),
        lng: centerLng + Math.sin(angle) * (radius + stagger),
        approximate: true,
    };
}

function collectLatLngs() {
    /** @type {[number, number][]} */
    const out = [[props.centerLat, props.centerLng]];
    const n = props.places.length;
    props.places.forEach((row, index) => {
        const { lat, lng } = effectiveLatLng(row, index, n, props.centerLat, props.centerLng);
        if (Number.isFinite(lat) && Number.isFinite(lng)) {
            out.push([lat, lng]);
        }
    });
    return out;
}

function renderLayers() {
    if (!mapInstance || !markersLayer) {
        return;
    }

    markersLayer.clearLayers();

    const propertyIcon = L.divIcon({
        className: 'property-detail-map-card__leaflet-divicon',
        html: propertyMarkerHtml(),
        iconSize: [44, 44],
        iconAnchor: [22, 40],
    });

    const mainMarker = L.marker([props.centerLat, props.centerLng], { icon: propertyIcon });
    const titleTip = escapeHtmlText(props.propertyTitle || '');
    if (titleTip) {
        mainMarker.bindTooltip(titleTip, tooltipOptions());
    }
    markersLayer.addLayer(mainMarker);

    const nPlaces = props.places.length;

    props.places.forEach((place, index) => {
        const { lat, lng, approximate } = effectiveLatLng(
            place,
            index,
            nPlaces,
            props.centerLat,
            props.centerLng,
        );
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return;
        }
        const icon = L.divIcon({
            className: 'property-detail-map-card__leaflet-divicon',
            html: nearbyMarkerHtml(place?.type, place?.icon),
            iconSize: [28, 28],
            iconAnchor: [14, 14],
        });
        const marker = L.marker([lat, lng], { icon });
        marker.bindTooltip(placeTooltipContent(place, approximate), {
            ...tooltipOptions(),
            interactive: false,
        });
        markersLayer.addLayer(marker);
    });

    const latLngs = collectLatLngs();
    if (latLngs.length <= 1) {
        mapInstance.setView([props.centerLat, props.centerLng], 15, { animate: false });
    } else {
        const bounds = L.latLngBounds(latLngs);
        if (bounds.isValid()) {
            mapInstance.fitBounds(bounds, { padding: [56, 56], maxZoom: 16, animate: false });
        } else {
            mapInstance.setView([props.centerLat, props.centerLng], 15, { animate: false });
        }
    }
}

function initMap() {
    if (!mapContainer.value || mapInstance) {
        return;
    }

    mapInstance = L.map(mapContainer.value, {
        center: [props.centerLat, props.centerLng],
        zoom: 15,
        zoomControl: false,
        attributionControl: false,
    });

    // One control only; top-right so it does not clash with category tabs at the bottom.
    L.control.zoom({ position: 'topright' }).addTo(mapInstance);
    L.tileLayer(TILE_URL, { maxZoom: 20 }).addTo(mapInstance);

    markersLayer = L.layerGroup().addTo(mapInstance);

    mapInstance.whenReady(() => {
        renderLayers();
        nextTick(() => {
            mapInstance?.invalidateSize(true);
            renderLayers();
        });
    });

    if (typeof ResizeObserver !== 'undefined' && mapContainer.value) {
        mapResizeObserver = new ResizeObserver(() => {
            mapInstance?.invalidateSize(true);
        });
        mapResizeObserver.observe(mapContainer.value);
    }
}

function teardownMap() {
    if (mapResizeObserver && mapContainer.value) {
        mapResizeObserver.unobserve(mapContainer.value);
    }
    mapResizeObserver = null;
    if (mapInstance) {
        mapInstance.remove();
        mapInstance = null;
    }
    markersLayer = null;
}

onMounted(() => {
    nextTick(initMap);
});

onBeforeUnmount(teardownMap);

watch(
    () => [props.centerLat, props.centerLng, props.places],
    () => {
        if (!mapInstance || !markersLayer) {
            return;
        }
        renderLayers();
        nextTick(() => mapInstance?.invalidateSize(true));
    },
    { deep: true },
);
</script>

<style scoped>
.property-detail-map-card__leaflet {
    min-height: min(420px, 55vh);
}
</style>
