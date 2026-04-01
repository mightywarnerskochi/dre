<script setup>
import { onMounted, reactive } from 'vue';

const props = defineProps({
    filters: { type: Array, default: () => [] },
    initialValues: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['search']);

const filterValues = reactive({ ...props.initialValues });

function fieldIcon(filterKey) {
    if (filterKey === 'location') return 'location';
    if (filterKey === 'property_type') return 'building';
    if (filterKey === 'bedrooms' || filterKey === 'bathrooms' || filterKey === 'bed_and_baths') return 'bed';
    return 'location';
}

function submitSearch() {
    emit('search', { ...filterValues });
}

onMounted(() => {
    // Initial sync from props if any
});
</script>

<template>
    <section class="dre-filter-overlap">
        <div class="dre-container">
            <form class="dre-search-bar dre-search-bar--listing" @submit.prevent="submitSearch">
            <div
                v-for="filter in filters"
                :key="filter.key"
                class="dre-search-bar__field"
            >
                <label class="dre-sr-only" :for="`dre-filter-${filter.key}`">{{ filter.label }}</label>
                <span class="dre-search-bar__icon" aria-hidden="true">
                    <!-- Same icons as HeroSection -->
                    <svg v-if="fieldIcon(filter.key) === 'location'" class="dre-icon-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z" /><circle cx="12" cy="11" r="2.5" stroke-width="1.8" /></svg>
                    <svg v-else-if="fieldIcon(filter.key) === 'building'" class="dre-icon-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 21h16" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21V7h10v14" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 10h.01M14 10h.01M10 14h.01M14 14h.01" /></svg>
                    <svg v-else class="dre-icon-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12h18" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 12V9a2 2 0 114 0v3M13 12V9a2 2 0 114 0v3" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12v6h14v-6" /></svg>
                </span>
                <select
                    v-if="filter.uiType === 'dropdown' || (filter.options && filter.options.length)"
                    :id="`dre-filter-${filter.key}`"
                    v-model="filterValues[filter.key]"
                    @change="submitSearch"
                >
                    <option v-for="opt in filter.options || []" :key="opt.value" :value="opt.value">
                        {{ opt.label }}
                    </option>
                </select>
                <input
                    v-else
                    :id="`dre-filter-${filter.key}`"
                    v-model="filterValues[filter.key]"
                    :type="filter.uiType === 'integer' ? 'number' : 'text'"
                    :placeholder="filter.label"
                    @input="submitSearch"
                >
            </div>
            <button type="submit" class="dre-btn dre-btn--primary dre-search-bar__submit dre-search-bar__submit--icon">
                <svg class="dre-icon-sm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                </svg>
                Search
            </button>
            </form>
        </div>
    </section>
</template>

<style scoped>
.dre-search-bar--listing {
    box-shadow: var(--dre-shadow-search);
    background: white;
    border-radius: var(--dre-radius-xl);
}

.dre-search-bar__submit--icon {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
}
</style>
