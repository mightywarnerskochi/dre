<template>
    <section v-if="neighborhoodsData.displayHome !== false" ref="neighborhoodSectionEl" class="neighborhoods commonPadding-120">
        <div class="container-ctn">
            <div class="head text-center">
                <h2>{{ neighborhoodsData.title || 'Explore Neighborhoods' }}</h2>
                <p>{{ neighborhoodsData.description }}</p>
            </div>
        </div>
        <div v-if="neighborhoodItems.length" class="container-center">
            <div class="neighborhoods__tabs-wrapper">
                <ul class="nav nav-pills neighborhoods__tabs" id="myTab" role="tablist">
                    <li
                        v-for="item in neighborhoodItems"
                        :key="item.id"
                        class="nav-item"
                        role="presentation"
                    >
                        <button
                            class="nav-link"
                            :id="`neighborhood-tab-${item.id}`"
                            type="button"
                            role="tab"
                            :aria-controls="`neighborhood-map-${item.id}`"
                            :aria-selected="activeNeighborhoodId === item.id"
                            :class="{ active: activeNeighborhoodId === item.id }"
                            @click="activateNeighborhood(item.id)"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M7 2V22" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7.25 3C7.25 3 14 6.5 16 11C18 15.5 16.5 22 16.5 22" stroke="#4B5C77" stroke-width="2" />
                                <path d="M2 22H22" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M5 7.5H16" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7 11H11" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7 14.5H13" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7 18H13.5" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            {{ item.label }}
                        </button>
                    </li>
                </ul>
                <div class="tab-content neighborhoods__tab-content neighborhoods__tab-content--map" id="myTabContent">
                    <div
                        class="neighborhoods__map-container"
                        :id="activeNeighborhoodId ? `neighborhood-map-${activeNeighborhoodId}` : 'neighborhood-map'"
                        :class="{ 'is-hovered': isNeighborhoodMapHovered }"
                        @mouseenter="onNeighborhoodMapHover"
                        @mouseleave="isNeighborhoodMapHovered = false"
                    >
                        <div ref="neighborhoodMapEl" class="neighborhoods__map-leaflet"></div>
                        <div v-if="isNeighborhoodLoading" class="neighborhoods__map-loading">Loading map markers...</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { getHomeNeighborhoodsData } from '@/utils/publicContent';

const { locale } = useI18n();
const neighborhoodsData = computed(() => getHomeNeighborhoodsData(locale.value));
const neighborhoodItems = computed(() => neighborhoodsData.value.items || []);

const neighborhoodMapEl = ref(null);
const neighborhoodSectionEl = ref(null);
const neighborhoodMap = ref(null);
const neighborhoodMarkerLayer = ref(null);
const activeNeighborhoodId = ref(null);
const isNeighborhoodLoading = ref(false);
const isNeighborhoodMapHovered = ref(false);
const hasNeighborhoodMarkersLoaded = ref(false);
const isNeighborhoodSectionVisible = ref(false);
let neighborhoodViewObserver = null;
let mapMoveFetchTimer = null;

function initNeighborhoodMap() {
    if (!neighborhoodMapEl.value || neighborhoodMap.value) return;

    neighborhoodMap.value = L.map(neighborhoodMapEl.value, {
        zoomControl: true,
        scrollWheelZoom: false,
        attributionControl: false,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd',
    }).addTo(neighborhoodMap.value);

    neighborhoodMarkerLayer.value = L.layerGroup().addTo(neighborhoodMap.value);

    const seed = neighborhoodItems.value.find((item) => item.id === activeNeighborhoodId.value) || neighborhoodItems.value[0];
    if (seed && Number.isFinite(seed.latitude) && Number.isFinite(seed.longitude)) {
        neighborhoodMap.value.setView([seed.latitude, seed.longitude], 11);
    } else {
        neighborhoodMap.value.setView([25.2048, 55.2708], 10);
    }
}

async function loadNeighborhoodMarkers(id, options = {}) {
    const useBounds = options.useBounds !== false;
    const endpointBase = neighborhoodsData.value.endpointBase;
    if (!id || !endpointBase || !neighborhoodMap.value || !neighborhoodMarkerLayer.value) return;

    isNeighborhoodLoading.value = true;
    try {
        const url = new URL(`${endpointBase}/${id}/properties`, window.location.origin);
        url.searchParams.set('lang', locale.value || 'en');
        url.searchParams.set('lite', '1');
        if (useBounds && neighborhoodMap.value) {
            const bounds = neighborhoodMap.value.getBounds();
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
        const properties = Array.isArray(payload?.properties) ? payload.properties : [];

        neighborhoodMarkerLayer.value.clearLayers();

        properties.forEach((property) => {
            const lat = Number(property.latitude);
            const lng = Number(property.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

            const point = L.latLng(lat, lng);
            L.marker(point, {
                icon: L.divIcon({
                    className: 'dre-home-map-pin',
                    html: '<span></span>',
                    iconSize: [16, 16],
                    iconAnchor: [8, 8],
                }),
            }).addTo(neighborhoodMarkerLayer.value);
        });

        hasNeighborhoodMarkersLoaded.value = true;
    } finally {
        isNeighborhoodLoading.value = false;
    }
}

function activateNeighborhood(id) {
    if (activeNeighborhoodId.value === id) return;
    activeNeighborhoodId.value = id;
    if (hasNeighborhoodMarkersLoaded.value) {
        const selected = neighborhoodItems.value.find((item) => item.id === id);
        if (selected && neighborhoodMap.value) {
            neighborhoodMap.value.setView([selected.latitude, selected.longitude], neighborhoodMap.value.getZoom());
        }
        loadNeighborhoodMarkers(id, { useBounds: false });
    }
}

function onNeighborhoodMapHover() {
    isNeighborhoodMapHovered.value = true;
    if (isNeighborhoodSectionVisible.value && !hasNeighborhoodMarkersLoaded.value && activeNeighborhoodId.value) {
        loadNeighborhoodMarkers(activeNeighborhoodId.value, { useBounds: false });
    }
}

watch(
    neighborhoodItems,
    (items) => {
        if (!items.length) {
            activeNeighborhoodId.value = null;
            return;
        }
        if (!items.some((item) => item.id === activeNeighborhoodId.value)) {
            activeNeighborhoodId.value = items[0].id;
        }
        if (neighborhoodMap.value && activeNeighborhoodId.value && hasNeighborhoodMarkersLoaded.value) {
            loadNeighborhoodMarkers(activeNeighborhoodId.value, { useBounds: false });
        }
    },
    { immediate: true },
);

watch(locale, () => {
    if (activeNeighborhoodId.value && hasNeighborhoodMarkersLoaded.value) {
        loadNeighborhoodMarkers(activeNeighborhoodId.value, { useBounds: false });
    }
});

onMounted(() => {
    initNeighborhoodMap();

    if (neighborhoodMap.value) {
        neighborhoodMap.value.on('moveend zoomend', () => {
            if (!hasNeighborhoodMarkersLoaded.value || !activeNeighborhoodId.value) return;
            if (mapMoveFetchTimer) {
                clearTimeout(mapMoveFetchTimer);
            }
            mapMoveFetchTimer = setTimeout(() => {
                loadNeighborhoodMarkers(activeNeighborhoodId.value, { useBounds: true });
            }, 250);
        });
    }

    if ('IntersectionObserver' in window && neighborhoodSectionEl.value) {
        neighborhoodViewObserver = new IntersectionObserver((entries) => {
            const entry = entries[0];
            isNeighborhoodSectionVisible.value = !!entry?.isIntersecting;
        }, { threshold: 0.2 });
        neighborhoodViewObserver.observe(neighborhoodSectionEl.value);
    } else {
        isNeighborhoodSectionVisible.value = true;
    }

    if (activeNeighborhoodId.value && isNeighborhoodSectionVisible.value && isNeighborhoodMapHovered.value) {
        loadNeighborhoodMarkers(activeNeighborhoodId.value, { useBounds: false });
    }
});

onUnmounted(() => {
    if (mapMoveFetchTimer) {
        clearTimeout(mapMoveFetchTimer);
        mapMoveFetchTimer = null;
    }
    if (neighborhoodViewObserver) {
        neighborhoodViewObserver.disconnect();
        neighborhoodViewObserver = null;
    }
    if (neighborhoodMap.value) {
        neighborhoodMap.value.remove();
        neighborhoodMap.value = null;
    }
});
</script>

<style scoped>
.neighborhoods__tab-content--map {
    position: relative;
}

.neighborhoods__map-leaflet {
    width: 100%;
    min-height: 430px;
    border-radius: 12px;
}

@media (max-width: 767px) {
    .neighborhoods__map-leaflet {
        min-height: 300px;
    }
}

.neighborhoods__map-loading {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #e4e8ef;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 12px;
    color: #4b5c77;
}

:deep(.leaflet-control-attribution) {
    font-size: 10px;
    opacity: 0.65;
}

:deep(.dre-home-map-pin) {
    width: 16px;
    height: 16px;
}

:deep(.dre-home-map-pin span) {
    display: block;
    width: 16px;
    height: 16px;
    border-radius: 50% 50% 50% 0;
    transform: rotate(-45deg);
    background: #2a559c;
    border: 1px solid rgba(255, 255, 255, 0.9);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
    position: relative;
}

:deep(.dre-home-map-pin span::after) {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #ffffff;
    position: absolute;
    top: 4px;
    left: 4px;
}

.neighborhoods__map-container.is-hovered :deep(.dre-home-map-pin span) {
    animation: map-pin-blink 1s ease-in-out infinite;
}

@keyframes map-pin-blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.45; }
}
</style>
