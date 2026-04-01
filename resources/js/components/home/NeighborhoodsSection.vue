<script setup>
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import { nextTick, onMounted, ref } from 'vue';

const props = defineProps({
    neighborhoods: { type: Object, required: true },
});

const items = props.neighborhoods.items ?? [];
const activeId = ref(items[0]?.id ?? null);
const loading = ref(false);
const errorText = ref('');
const mapEl = ref(null);
const map = ref(null);
const markerLayer = ref(null);
const tileLayer = ref(null);
let moveFetchTimer = null;
let suppressNextMoveFetch = false;

function currentLocale() {
    return String(
        props.neighborhoods.locale
            || document.documentElement.getAttribute('lang')
            || 'en',
    ).toLowerCase();
}

function getTileConfig() {
    return {
        url: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
        options: {
            maxZoom: 19,
            subdomains: 'abcd',
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        },
    };
}

function createPriceIcon(label) {
    return L.divIcon({
        className: 'dre-map-price-marker-wrap',
        html: `<div class="dre-map-price-marker">${label}</div>`,
        iconSize: [72, 26],
        iconAnchor: [36, 13],
    });
}

async function loadNeighborhoodProperties(id, options = {}) {
    if (!id || !props.neighborhoods.endpointBase) return;
    const useBounds = Boolean(options.useBounds);
    const applyView = options.applyView !== false;

    loading.value = true;
    errorText.value = '';
    try {
        const url = new URL(`${props.neighborhoods.endpointBase}/${id}/properties`, window.location.origin);
        url.searchParams.set('lang', currentLocale());

        if (useBounds && map.value) {
            const bounds = map.value.getBounds();
            url.searchParams.set('north', String(bounds.getNorth()));
            url.searchParams.set('south', String(bounds.getSouth()));
            url.searchParams.set('east', String(bounds.getEast()));
            url.searchParams.set('west', String(bounds.getWest()));
        }

        const response = await fetch(url.toString(), {
            headers: { Accept: 'application/json' },
        });
        if (!response.ok) {
            throw new Error(`Failed with ${response.status}`);
        }

        const payload = await response.json();
        const center = payload?.neighborhood ?? null;
        const properties = payload?.properties ?? [];

        if (!map.value) return;
        markerLayer.value?.clearLayers();

        const bounds = [];
        if (applyView && center?.latitude && center?.longitude) {
            const cLatLng = L.latLng(Number(center.latitude), Number(center.longitude));
            bounds.push(cLatLng);
            suppressNextMoveFetch = true;
            map.value.setView(cLatLng, 12, { animate: true });
        }

        properties.forEach((property) => {
            const lat = Number(property.latitude);
            const lng = Number(property.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

            const latLng = L.latLng(lat, lng);
            bounds.push(latLng);
            L.marker(latLng, {
                icon: createPriceIcon(property.priceLabel || 'From -'),
            })
                .bindPopup(
                    `<div><strong>${property.title || 'Property'}</strong><br>${property.location || ''}</div>`,
                )
                .addTo(markerLayer.value);
        });

        if (applyView && bounds.length > 1) {
            suppressNextMoveFetch = true;
            map.value.fitBounds(L.latLngBounds(bounds), { padding: [24, 24], maxZoom: 13 });
        }
    } catch (error) {
        errorText.value = 'Unable to load properties for this neighborhood right now.';
    } finally {
        loading.value = false;
    }
}

async function activateNeighborhood(id) {
    if (activeId.value === id) return;
    activeId.value = id;
    await loadNeighborhoodProperties(id, { applyView: true });
}

onMounted(async () => {
    if (!mapEl.value) return;

    map.value = L.map(mapEl.value, {
        zoomControl: true,
        scrollWheelZoom: false,
    });
    markerLayer.value = L.layerGroup().addTo(map.value);

    const tileConfig = getTileConfig();
    tileLayer.value = L.tileLayer(tileConfig.url, tileConfig.options).addTo(map.value);

    const first = items[0];
    if (first?.latitude && first?.longitude) {
        map.value.setView([Number(first.latitude), Number(first.longitude)], 11);
    } else {
        map.value.setView([25.2048, 55.2708], 10);
    }

    await nextTick();
    if (activeId.value) {
        await loadNeighborhoodProperties(activeId.value, { applyView: true });
    }

    map.value.on('moveend zoomend', () => {
        if (!activeId.value) return;
        if (suppressNextMoveFetch) {
            suppressNextMoveFetch = false;
            return;
        }
        if (moveFetchTimer) {
            clearTimeout(moveFetchTimer);
        }
        moveFetchTimer = setTimeout(() => {
            loadNeighborhoodProperties(activeId.value, { useBounds: true, applyView: false });
        }, 350);
    });
});
</script>

<template>
    <section id="neighborhoods" class="dre-section dre-section--white">
        <div class="dre-container">
            <div class="dre-section__head">
                <h2 class="dre-section__title">{{ neighborhoods.title }}</h2>
                <p v-if="neighborhoods.description" class="dre-section__desc">
                    {{ neighborhoods.description }}
                </p>
            </div>

            <div class="dre-neighborhood__toggle-row">
                <div class="dre-pill-toggle" role="group" aria-label="Neighborhood">
                    <button
                        v-for="n in items"
                        :key="n.id"
                        type="button"
                        class="dre-pill-toggle__btn"
                        :class="{ 'is-active': activeId === n.id }"
                        @click="activateNeighborhood(n.id)"
                    >
                        {{ n.label }}
                    </button>
                </div>
            </div>

            <div class="dre-map-panel">
                <div class="dre-map-panel__canvas">
                    <div ref="mapEl" class="dre-map-panel__leaflet" />
                    <div v-if="loading" class="dre-map-panel__loading">Loading properties...</div>
                    <div v-if="errorText" class="dre-map-panel__error">{{ errorText }}</div>
                </div>
            </div>
        </div>
    </section>
</template>
