<template>
    <div class="offcanvas offcanvas-bottom listing-search-offcanvas" tabindex="-1" id="listingSearchOffcanvas" aria-labelledby="listingSearchOffcanvasLabel">
        <div class="offcanvas-header listing-search-offcanvas__header">
            <h2 class="offcanvas-title h5 mb-0" id="listingSearchOffcanvasLabel">{{ t('listing.searchAria') }}</h2>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" :aria-label="t('nav.close')"></button>
        </div>
        <div class="offcanvas-body listing-search-offcanvas__body js-listing-search-offcanvas-mount"></div>
    </div>

    <div class="offcanvas offcanvas-bottom listing-sort-offcanvas" tabindex="-1" id="listingSortOffcanvas" aria-labelledby="listingSortOffcanvasLabel">
        <div class="offcanvas-header listing-sort-offcanvas__header">
            <h2 class="offcanvas-title h5 mb-0" id="listingSortOffcanvasLabel">Sort by</h2>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" :aria-label="t('nav.close')"></button>
        </div>
        <div class="offcanvas-body listing-sort-offcanvas__body">
            <div class="listing-sort-offcanvas__options" role="list">
                <button
                    v-for="option in sortOptions"
                    :key="option.value"
                    type="button"
                    class="listing-sort-offcanvas__opt"
                    :class="{ 'is-active': activeSort === option.value }"
                    data-bs-dismiss="offcanvas"
                    @click="selectSort(option.value)"
                >
                    {{ option.label }}
                </button>
            </div>
        </div>
    </div>

    <nav class="listing-mobile-bar" :aria-label="t('listing.mobileToolsAria')">
        <button type="button" class="listing-mobile-bar__action" data-bs-toggle="offcanvas" data-bs-target="#listingSearchOffcanvas" aria-controls="listingSearchOffcanvas">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                <path d="M14.7982 13.7332L12.0157 10.9732C13.0957 9.62648 13.6188 7.91715 13.4773 6.19665C13.3358 4.47615 12.5404 2.87526 11.2548 1.72316C9.9692 0.571055 8.29103 -0.0446921 6.56537 0.00252822C4.8397 0.0497485 3.19772 0.756347 1.97703 1.97703C0.756347 3.19772 0.0497485 4.8397 0.00252822 6.56537C-0.0446921 8.29103 0.571055 9.9692 1.72316 11.2548C2.87526 12.5404 4.47615 13.3358 6.19665 13.4773C7.91715 13.6188 9.62648 13.0957 10.9732 12.0157L13.7332 14.7757C13.8029 14.846 13.8858 14.9018 13.9772 14.9398C14.0686 14.9779 14.1667 14.9975 14.2657 14.9975C14.3647 14.9975 14.4627 14.9779 14.5541 14.9398C14.6455 14.9018 14.7285 14.846 14.7982 14.7757C14.9334 14.6358 15.0089 14.4489 15.0089 14.2544C15.0089 14.0599 14.9334 13.873 14.7982 13.7332ZM6.76568 12.0157C5.72732 12.0157 4.71229 11.7078 3.84893 11.1309C2.98557 10.554 2.31267 9.73408 1.91531 8.77476C1.51795 7.81545 1.41398 6.75985 1.61655 5.74145C1.81912 4.72305 2.31914 3.78759 3.05336 3.05336C3.78759 2.31914 4.72305 1.81912 5.74145 1.61655C6.75985 1.41398 7.81545 1.51795 8.77476 1.91531C9.73408 2.31267 10.554 2.98557 11.1309 3.84893C11.7078 4.71229 12.0157 5.72732 12.0157 6.76568C12.0157 8.15806 11.4626 9.49342 10.478 10.478C9.49342 11.4626 8.15806 12.0157 6.76568 12.0157Z" fill="currentColor" />
            </svg>
        </button>

        <RouterLink :to="{ name: 'map' }" class="listing-mobile-bar__action listing-mobile-bar__action--link">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M20 10C20 16.5 12 22 12 22C12 22 4 16.5 4 10C4 7.87827 4.84285 5.84344 6.34315 4.34315C7.84344 2.84285 9.87827 2 12 2C14.1217 2 16.1566 2.84285 17.6569 4.34315C19.1571 5.84344 20 7.87827 20 10Z" stroke="currentColor" stroke-width="2" />
                <path d="M15 10C15 10.7956 14.6839 11.5587 14.1213 12.1213C13.5587 12.6839 12.7956 13 12 13C11.2044 13 10.4413 12.6839 9.87868 12.1213C9.31607 11.5587 9 10.7956 9 10C9 9.20435 9.31607 8.44129 9.87868 7.87868C10.4413 7.31607 11.2044 7 12 7C12.7956 7 13.5587 7.31607 14.1213 7.87868C14.6839 8.44129 15 9.20435 15 10Z" stroke="currentColor" stroke-width="2" />
            </svg>
            <span>{{ t('listing.map') }}</span>
        </RouterLink>

        <div class="listing-mobile-bar__views" role="group" :aria-label="t('listing.layoutAria')">
            <button type="button" class="listing-mobile-bar__view-btn" data-listing-view="list" :class="{ 'is-active': activeView === 'list' }" :aria-label="t('listing.listViewAria')" :aria-pressed="activeView === 'list' ? 'true' : 'false'" @click="setListingView('list')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M8 9C7.71667 9 7.47934 8.904 7.288 8.712C7.09667 8.52 7.00067 8.28267 7 8C6.99934 7.71733 7.09534 7.48 7.288 7.288C7.48067 7.096 7.718 7 8 7H20C20.2833 7 20.521 7.096 20.713 7.288C20.905 7.48 21.0007 7.71733 21 8C20.9993 8.28267 20.9033 8.52033 20.712 8.713C20.5207 8.90567 20.2833 9.00133 20 9H8ZM8 13C7.71667 13 7.47934 12.904 7.288 12.712C7.09667 12.52 7.00067 12.2827 7 12C6.99934 11.7173 7.09534 11.48 7.288 11.288C7.48067 11.096 7.718 11 8 11H20C20.2833 11 20.521 11.096 20.713 11.288C20.905 11.48 21.0007 11.7173 21 12C20.9993 12.2827 20.9033 12.5203 20.712 12.713C20.5207 12.9057 20.2833 13.0013 20 13H8ZM8 17C7.71667 17 7.47934 16.904 7.288 16.712C7.09667 16.52 7.00067 16.2827 7 16C6.99934 15.7173 7.09534 15.48 7.288 15.288C7.48067 15.096 7.718 15 8 15H20C20.2833 15 20.521 15.096 20.713 15.288C20.905 15.48 21.0007 15.7173 21 16C20.9993 16.2827 20.9033 16.5203 20.712 16.713C20.5207 16.9057 20.2833 17.0013 20 17H8ZM4 9C3.71667 9 3.47934 8.904 3.288 8.712C3.09667 8.52 3.00067 8.28267 3 8C2.99934 7.71733 3.09534 7.48 3.288 7.288C3.48067 7.096 3.718 7 4 7C4.282 7 4.51967 7.096 4.713 7.288C4.90634 7.48 5.002 7.71733 5 8C4.998 8.28267 4.902 8.52033 4.712 8.713C4.522 8.90567 4.28467 9.00133 4 9ZM4 13C3.71667 13 3.47934 12.904 3.288 12.712C3.09667 12.52 3.00067 12.2827 3 12C2.99934 11.7173 3.09534 11.48 3.288 11.288C3.48067 11.096 3.718 11 4 11C4.282 11 4.51967 11.096 4.713 11.288C4.90634 11.48 5.002 11.7173 5 12C4.998 12.2827 4.902 12.5203 4.712 12.713C4.522 12.9057 4.28467 13.0013 4 13ZM4 17C3.71667 17 3.47934 16.904 3.288 16.712C3.09667 16.52 3.00067 16.2827 3 16C2.99934 15.7173 3.09534 15.48 3.288 15.288C3.48067 15.096 3.718 15 4 15C4.282 15 4.51967 15.096 4.713 15.288C4.90634 15.48 5.002 15.7173 5 16C4.998 16.2827 4.902 16.5203 4.712 16.713C4.522 16.9057 4.28467 17.0013 4 17Z" fill="currentColor" />
                </svg>
            </button>
            <button type="button" class="listing-mobile-bar__view-btn" data-listing-view="grid" :class="{ 'is-active': activeView === 'grid' }" :aria-label="t('listing.gridViewAria')" :aria-pressed="activeView === 'grid' ? 'true' : 'false'" @click="setListingView('grid')">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M8.885 10.5H5.615C5.155 10.5 4.771 10.346 4.463 10.038C4.155 9.73 4.00067 9.34567 4 8.885V5.615C4 5.155 4.15433 4.771 4.463 4.463C4.77167 4.155 5.156 4.00067 5.616 4H8.885C9.345 4 9.72933 4.15433 10.038 4.463C10.3467 4.77167 10.5007 5.156 10.5 5.616V8.885C10.5 9.345 10.346 9.72933 10.038 10.038C9.73 10.3467 9.34567 10.5007 8.885 10.5Z" fill="currentColor" />
                    <path d="M18.385 10.5H15.115C14.655 10.5 14.271 10.346 13.963 10.038C13.655 9.73 13.5007 9.34567 13.5 8.885V5.615C13.5 5.155 13.6543 4.771 13.963 4.463C14.2717 4.155 14.656 4.00067 15.116 4H18.385C18.845 4 19.2293 4.15433 19.538 4.463C19.8467 4.77167 20.0007 5.156 20 5.616V8.885C20 9.345 19.846 9.72933 19.538 10.038C19.23 10.3467 18.8463 10.5007 18.385 10.5Z" fill="currentColor" />
                    <path d="M8.885 20H5.615C5.155 20 4.771 19.846 4.463 19.538C4.155 19.23 4.00067 18.8453 4 18.384V15.116C4 14.6553 4.15433 14.271 4.463 13.963C4.77167 13.655 5.156 13.5007 5.616 13.5H8.885C9.345 13.5 9.72933 13.6543 10.038 13.963C10.3467 14.2717 10.5007 14.656 10.5 15.116V18.385C10.5 18.845 10.346 19.2293 10.038 19.538C9.73 19.8467 9.34567 20.0007 8.885 20Z" fill="currentColor" />
                    <path d="M18.385 20H15.115C14.655 20 14.271 19.846 13.963 19.538C13.655 19.23 13.5007 18.8453 13.5 18.384V15.116C13.5 14.6553 13.6543 14.271 13.963 13.963C14.2717 13.655 14.656 13.5007 15.116 13.5H18.385C18.845 13.5 19.2293 13.6543 19.538 13.963C19.8467 14.2717 20.0007 14.656 20 15.116V18.385C20 18.845 19.846 19.2293 19.538 19.538C19.23 19.8467 18.8463 20.0007 18.385 20Z" fill="currentColor" />
                </svg>
            </button>
        </div>

        <button type="button" class="listing-mobile-bar__action" data-bs-toggle="offcanvas" data-bs-target="#listingSortOffcanvas" aria-controls="listingSortOffcanvas">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 6H20M8 12H16M10 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                <path d="M6 4V8M18 10V14M12 16V20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            <span>Sort</span>
        </button>
    </nav>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';

const { t } = useI18n({ useScope: 'global' });

const props = defineProps({
    sortOptions: {
        type: Array,
        default: () => [
            { value: 'newest', label: 'Newest first' },
            { value: 'price_asc', label: 'Price: low to high' },
            { value: 'price_desc', label: 'Price: high to low' },
            { value: 'area_desc', label: 'Largest area' },
        ],
    },
    activeSort: {
        type: String,
        default: 'newest',
    },
});

const emit = defineEmits(['sort-change']);

const sortOptions = computed(() => props.sortOptions);
const activeSort = computed(() => props.activeSort);
const activeView = ref('grid');

let listingSearchOffcanvas = null;
let listingSearchHome = null;
let listingSearchPanel = null;
let listingSearchOffcanvasMount = null;

function setListingView(view) {
    activeView.value = view;
    document.querySelector('.properties-grid')?.classList.toggle('properties-grid--list', view === 'list');
    document.querySelectorAll('[data-listing-view]').forEach((btn) => {
        const on = btn.getAttribute('data-listing-view') === view;
        btn.classList.toggle('is-active', on);
        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
}

function selectSort(value) {
    emit('sort-change', value);
}

function initDropdowns() {
    if (typeof window.initBannerSearchDropdowns === 'function') {
        window.initBannerSearchDropdowns();
    }
}

function moveSearchToOffcanvas() {
    if (listingSearchPanel && listingSearchOffcanvasMount) {
        listingSearchOffcanvasMount.appendChild(listingSearchPanel);
    }
}

function restoreSearchToPage() {
    if (listingSearchPanel && listingSearchHome) {
        listingSearchHome.appendChild(listingSearchPanel);
    }
    initDropdowns();
}

onMounted(async () => {
    await nextTick();
    listingSearchOffcanvas = document.getElementById('listingSearchOffcanvas');
    listingSearchHome = document.querySelector('.js-listing-search-home');
    listingSearchPanel = document.querySelector('.js-listing-search-panel');
    listingSearchOffcanvasMount = document.querySelector('.js-listing-search-offcanvas-mount');

    listingSearchOffcanvas?.addEventListener('show.bs.offcanvas', moveSearchToOffcanvas);
    listingSearchOffcanvas?.addEventListener('shown.bs.offcanvas', initDropdowns);
    listingSearchOffcanvas?.addEventListener('hidden.bs.offcanvas', restoreSearchToPage);
    setListingView(activeView.value);
});

onBeforeUnmount(() => {
    listingSearchOffcanvas?.removeEventListener('show.bs.offcanvas', moveSearchToOffcanvas);
    listingSearchOffcanvas?.removeEventListener('shown.bs.offcanvas', initDropdowns);
    listingSearchOffcanvas?.removeEventListener('hidden.bs.offcanvas', restoreSearchToPage);
    restoreSearchToPage();
});
</script>
