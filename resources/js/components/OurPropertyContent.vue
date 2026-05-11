<template>
    <section class="banner banner--page banner--page--listing position-relative" :aria-label="t('listing.pageHeaderAria')">
        <div class="banner--page__bg">
            <picture>
                <img :src="asset('public/images/inner-banner.jpg')" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">{{ t('listing.heroTitle') }}</h1>
                <ol class="breadcrumb-minimal" :aria-label="t('listing.breadcrumbAria')">
                    <li>
                        <RouterLink :to="{ name: 'home' }" :aria-label="t('listing.homeAria')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </RouterLink>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">{{ t('listing.breadcrumbCurrent') }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="properties-listing commonPadding-pb-120">
        <div class="listing-search-strip">
            <div class="container-ctn">
                <div class="js-listing-search-home">
                    <div class="js-listing-search-panel">
                    <PropertyFilters v-model="activeFilters" @search="onListingSearch" />
                    </div>
                </div>
            </div>
        </div>

        <div class="container-ctn pt-5">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div class="properties-listing__head">
                    <h2 class="mb-0">{{ t('listing.exploreTitle') }}</h2>
                </div>
                <div class="listing-toolbar">
                    <a :href="mapHref" class="listing-toolbar__btn text-decoration-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M20 10C20 16.5 12 22 12 22C12 22 4 16.5 4 10C4 7.87827 4.84285 5.84344 6.34315 4.34315C7.84344 2.84285 9.87827 2 12 2C14.1217 2 16.1566 2.84285 17.6569 4.34315C19.1571 5.84344 20 7.87827 20 10Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M15 10C15 10.7956 14.6839 11.5587 14.1213 12.1213C13.5587 12.6839 12.7956 13 12 13C11.2044 13 10.4413 12.6839 9.87868 12.1213C9.31607 11.5587 9 10.7956 9 10C9 9.20435 9.31607 8.44129 9.87868 7.87868C10.4413 7.31607 11.2044 7 12 7C12.7956 7 13.5587 7.31607 14.1213 7.87868C14.6839 8.44129 15 9.20435 15 10Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        {{ t('listing.map') }}
                    </a>

                    <div class="dropdown">
                        <button
                            type="button"
                            class="listing-toolbar__btn listing-toolbar__btn--sort dropdown-toggle d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="4" y1="21" x2="4" y2="14"></line>
                                <line x1="4" y1="10" x2="4" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12" y2="3"></line>
                                <line x1="20" y1="21" x2="20" y2="16"></line>
                                <line x1="20" y1="12" x2="20" y2="3"></line>
                                <line x1="1" y1="14" x2="7" y2="14"></line>
                                <line x1="9" y1="8" x2="15" y2="8"></line>
                                <line x1="17" y1="16" x2="23" y2="16"></line>
                            </svg>
                            {{ activeSortLabel }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end listing-toolbar__sort-menu">
                            <li v-for="opt in sortOptions" :key="opt.value">
                                <button
                                    type="button"
                                    class="listing-toolbar__sort-item"
                                    :class="{ active: selectedSort === opt.value }"
                                    @click="selectSort(opt.value)"
                                >
                                    {{ opt.label }}
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="listing-toolbar__views" role="group" :aria-label="t('listing.layoutAria')">
                        <button 
                            type="button" 
                            class="listing-toolbar__view-btn" 
                            :class="{ 'is-active': currentView === 'list' }"
                            @click="currentView = 'list'"
                            :aria-label="t('listing.listViewAria')" 
                            :aria-pressed="currentView === 'list'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M8 9C7.71667 9 7.47934 8.904 7.288 8.712C7.09667 8.52 7.00067 8.28267 7 8C6.99934 7.71733 7.09534 7.48 7.288 7.288C7.48067 7.096 7.718 7 8 7H20C20.2833 7 20.521 7.096 20.713 7.288C20.905 7.48 21.0007 7.71733 21 8C20.9993 8.28267 20.9033 8.52033 20.712 8.713C20.5207 8.90567 20.2833 9.00133 20 9H8ZM8 13C7.71667 13 7.47934 12.904 7.288 12.712C7.09667 12.52 7.00067 12.2827 7 12C6.99934 11.7173 7.09534 11.48 7.288 11.288C7.48067 11.096 7.718 11 8 11H20C20.2833 11 20.521 11.096 20.713 11.288C20.905 11.48 21.0007 11.7173 21 12C20.9993 12.2827 20.9033 12.5203 20.712 12.713C20.5207 12.9057 20.2833 13.0013 20 13H8ZM8 17C7.71667 17 7.47934 16.904 7.288 16.712C7.09667 16.52 7.00067 16.2827 7 16C6.99934 15.7173 7.09534 15.48 7.288 15.288C7.48067 15.096 7.718 15 8 15H20C20.2833 15 20.521 15.096 20.713 15.288C20.905 15.48 21.0007 15.7173 21 16C20.9993 16.2827 20.9033 16.5203 20.712 16.713C20.5207 16.9057 20.2833 17.0013 20 17H8ZM4 9C3.71667 9 3.47934 8.904 3.288 8.712C3.09667 8.52 3.00067 8.28267 3 8C2.99934 7.71733 3.09534 7.48 3.288 7.288C3.48067 7.096 3.718 7 4 7C4.282 7 4.51967 7.096 4.713 7.288C4.90634 7.48 5.002 7.71733 5 8C4.998 8.28267 4.902 8.52033 4.712 8.713C4.522 8.90567 4.28467 9.00133 4 9ZM4 13C3.71667 13 3.47934 12.904 3.288 12.712C3.09667 12.52 3.00067 12.2827 3 12C2.99934 11.7173 3.09534 11.48 3.288 11.288C3.48067 11.096 3.718 11 4 11C4.282 11 4.51967 11.096 4.713 11.288C4.90634 11.48 5.002 11.7173 5 12C4.998 12.2827 4.902 12.5203 4.712 12.713C4.522 12.9057 4.28467 13.0013 4 13ZM4 17C3.71667 17 3.47934 16.904 3.288 16.712C3.09667 16.52 3.00067 16.2827 3 16C2.99934 15.7173 3.09534 15.48 3.288 15.288C3.48067 15.096 3.718 15 4 15C4.282 15 4.51967 15.096 4.713 15.288C4.90634 15.48 5.002 15.7173 5 16C4.998 16.2827 4.902 16.5203 4.712 16.713C4.522 16.9057 4.28467 17.0013 4 17Z" fill="currentColor"/>
                            </svg>
                        </button>
                        <button 
                            type="button" 
                            class="listing-toolbar__view-btn" 
                            :class="{ 'is-active': currentView === 'grid' }"
                            @click="currentView = 'grid'"
                            :aria-label="t('listing.gridViewAria')" 
                            :aria-pressed="currentView === 'grid'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M8.885 10.5H5.615C5.155 10.5 4.771 10.346 4.463 10.038C4.155 9.73 4.00067 9.34567 4 8.885V5.615C4 5.155 4.15433 4.771 4.463 4.463C4.77167 4.155 5.156 4.00067 5.616 4H8.885C9.345 4 9.72933 4.15433 10.038 4.463C10.3467 4.77167 10.5007 5.156 10.5 5.616V8.885C10.5 9.345 10.346 9.72933 10.038 10.038C9.73 10.3467 9.34567 10.5007 8.885 10.5ZM5.615 9.5H8.885C9.06433 9.5 9.21167 9.44233 9.327 9.327C9.44233 9.21167 9.5 9.06433 9.5 8.885V5.615C9.5 5.43567 9.44233 5.28833 9.327 5.173C9.21167 5.05767 9.06433 5 8.885 5H5.615C5.43567 5 5.28833 5.05767 5.173 5.173C5.05767 5.28833 5 5.436 5 5.616V8.885C5 9.06433 5.05767 9.21167 5.173 9.327C5.28833 9.44233 5.436 9.5 5.616 9.5M8.885 20H5.615C5.155 20 4.771 19.846 4.463 19.538C4.155 19.23 4.00067 18.8453 4 18.384V15.116C4 14.6553 4.15433 14.271 4.463 13.963C4.77167 13.655 5.156 13.5007 5.616 13.5H8.885C9.345 13.5 9.72933 13.6543 10.038 13.963C10.3467 14.2717 10.5007 14.656 10.5 15.116V18.385C10.5 18.845 10.346 19.2293 10.038 19.538C9.73 19.8467 9.34567 20.0007 8.885 20ZM5.615 19H8.885C9.06433 19 9.21167 18.9423 9.327 18.827C9.44233 18.7117 9.5 18.5643 9.5 18.385V15.115C9.5 14.9357 9.44233 14.7883 9.327 14.673C9.21167 14.5577 9.06433 14.5 8.885 14.5H5.615C5.43567 14.5 5.28833 14.5577 5.173 14.673C5.05767 14.7883 5 14.936 5 15.116V18.385C5 18.5643 5.05767 18.7117 5.173 18.827C5.28833 18.9423 5.436 19 5.616 19M18.385 10.5H15.115C14.655 10.5 14.271 10.346 13.963 10.038C13.655 9.73 13.5007 9.34567 13.5 8.885V5.615C13.5 5.155 13.6543 4.771 13.963 4.463C14.2717 4.155 14.656 4.00067 15.116 4H18.385C18.845 4 19.2293 4.15433 19.538 4.463C19.8467 4.77167 20.0007 5.156 20 5.616V8.885C20 9.345 19.846 9.72933 19.538 10.038C19.23 10.3467 18.8463 10.5007 18.385 10.5ZM15.115 9.5H18.385C18.5643 9.5 18.7117 9.44233 18.827 9.327C18.9423 9.21167 19 9.06433 19 8.885V5.615C19 5.43567 18.9423 5.28833 18.827 5.173C18.7117 5.05767 18.5643 5 18.385 5H15.115C14.9357 5 14.7883 5.05767 14.673 5.173C14.5577 5.28833 14.5 5.436 14.5 5.616V8.885C14.5 9.06433 14.5577 9.21167 14.673 9.327C14.7883 9.44233 14.936 9.5 15.116 9.5M18.385 20H15.115C14.655 20 14.271 19.846 13.963 19.538C13.655 19.23 13.5007 18.8453 13.5 18.384V15.116C13.5 14.6553 13.6543 14.271 13.963 13.963C14.2717 13.655 14.656 13.5007 15.116 13.5H18.385C18.845 13.5 19.2293 13.6543 19.538 13.963C19.8467 14.2717 20.0007 14.656 20 15.116V18.385C20 18.845 19.846 19.2293 19.538 19.538C19.23 19.8467 18.8463 20.0007 18.385 20ZM15.115 19H18.385C18.5643 19 18.7117 18.9423 18.827 18.827C18.9423 18.7117 19 18.5643 19 18.385V15.115C19 14.9357 18.9423 14.7883 18.827 14.673C18.7117 14.5577 18.5643 14.5 18.385 14.5H15.115C14.9357 14.5 14.7883 14.5577 14.673 14.673C14.5577 14.7883 14.5 14.936 14.5 15.116V18.385C14.5 18.5643 14.5577 18.7117 14.673 18.827C14.7883 18.9423 14.936 19 15.116 19" fill="currentColor"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <OurPropertyListingGrid ref="listingGridRef" />
        </div>
        <ListingMobileExtras
            :sort-options="sortOptions"
            :active-sort="selectedSort"
            :map-href="mapHref"
            @sort-change="selectSort"
        />
    </section>
</template>

<script setup>
import { computed, provide, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { RouterLink, useRoute } from 'vue-router';
import PropertyFilters from '@/components/properties/PropertyFilters.vue';
import ListingMobileExtras from '@/components/ListingMobileExtras.vue';
import OurPropertyListingGrid from '@/components/OurPropertyListingGrid.vue';
import { asset } from '@/utils/asset';

const { t } = useI18n({ useScope: 'global' });
const listingGridRef = ref(null);
const route = useRoute();

const currentView = ref('grid');

// Consolidated active filters
const activeFilters = ref({
    listing_type: route.query.listing_type || '',
    property_type: route.query.property_type || route.query.type || '',
    categories: route.query.categories || route.query.category || '',
    bedrooms: route.query.bedrooms || route.query.beds || '',
    bathrooms: route.query.bathrooms || route.query.baths || '',
    location: route.query.location || '',
    min_price: route.query.min_price || '',
    max_price: route.query.max_price || '',
});

const selectedSort = ref(route.query.sort || 'newest');

const sortOptions = [
    { value: 'newest', label: 'Newest first' },
    { value: 'price_asc', label: 'Price: low to high' },
    { value: 'price_desc', label: 'Price: high to low' },
    { value: 'area_desc', label: 'Largest area' },
];

const dreVueListingFilters = computed(() => ({
    ...activeFilters.value,
    sort: selectedSort.value,
    view: currentView.value,
}));

const mapHref = computed(() => {
    const params = new URLSearchParams();

    Object.entries(dreVueListingFilters.value).forEach(([key, value]) => {
        const normalized = String(value ?? '').trim();
        if (normalized !== '') {
            params.set(key, normalized);
        }
    });

    const query = params.toString();
    return query ? `/map?${query}` : '/map';
});

// Provide filters to children components (ListingGrid)
provide('dreVueListingFilters', dreVueListingFilters);
provide('currentView', currentView);

const activeSortLabel = computed(() => {
    return sortOptions.find((option) => option.value === selectedSort.value)?.label || 'Sort';
});

async function selectSort(value) {
    selectedSort.value = value || 'newest';
    await listingGridRef.value?.loadFirstPage?.();
}

async function onListingSearch() {
    await listingGridRef.value?.loadFirstPage?.();
}
</script>

<style scoped>
.listing-toolbar {
    display: flex;
    align-items: center;
    gap: 12px;
}

.listing-toolbar__btn {
    border: 1px solid #b9c9df;
    border-radius: 999px;
    background: #fff;
    color: #2f4f83;
    padding: 10px 18px;
    font-size: 16px;
    font-weight: 500;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.listing-toolbar__btn--sort {
    background: #d3ddec;
}

.listing-toolbar__btn--sort::after {
    margin-left: 4px;
}

.listing-toolbar__sort-menu {
    min-width: 180px;
    margin-top: 8px !important;
    padding: 6px 0;
    border-radius: 8px;
    border: 1px solid #d3dce8;
    box-shadow: 0 10px 30px rgba(47, 63, 88, 0.12);
}

.listing-toolbar__sort-item {
    width: 100%;
    text-align: left;
    background: transparent;
    border: 0;
    color: #2f4f83;
    font-size: 16px;
    font-weight: 500;
    padding: 10px 14px;
}

.listing-toolbar__sort-item:hover {
    background: #f1f5fb;
}

.listing-toolbar__sort-item.active {
    background: transparent;
    color: #1f3f74;
    font-weight: 600;
}

@media (max-width: 767.98px) {
    .container-ctn.pt-5 {
        padding-top: 28px !important;
        padding-bottom: 96px;
    }

    .container-ctn.pt-5 > .d-flex {
        display: block !important;
        margin-bottom: 16px !important;
    }

    .listing-toolbar {
        display: none;
    }

    .properties-listing__head h2 {
        font-size: 18px;
        line-height: 1.25;
    }
}
</style>
