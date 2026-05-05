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
                <div class="properties-listing__search banner-search p-0 js-listing-search-panel">
                    <form
                        id="dre-our-property-listing-form"
                        action="#"
                        class="search-form search-form--listing"
                        @submit.prevent="onListingSearch"
                    >
                        <div class="search-field search-field--location">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M9 1.5C5.7 1.5 3 4.2 3 7.5C3 11.55 8.25 16.125 8.475 16.35C8.625 16.425 8.85 16.5 9 16.5C9.15 16.5 9.375 16.425 9.525 16.35C9.75 16.125 15 11.55 15 7.5C15 4.2 12.3 1.5 9 1.5ZM9 14.775C7.425 13.275 4.5 10.05 4.5 7.5C4.5 5.025 6.525 3 9 3C11.475 3 13.5 5.025 13.5 7.5C13.5 9.975 10.575 13.275 9 14.775ZM9 4.5C7.35 4.5 6 5.85 6 7.5C6 9.15 7.35 10.5 9 10.5C10.65 10.5 12 9.15 12 7.5C12 5.85 10.65 4.5 9 4.5ZM9 9C8.175 9 7.5 8.325 7.5 7.5C7.5 6.675 8.175 6 9 6C9.825 6 10.5 6.675 10.5 7.5C10.5 8.325 9.825 9 9 9Z" fill="#4B5C77"/>
                              </svg>
                                <input type="text" name="location" :placeholder="t('listing.locationPlaceholder')" autocomplete="address-level2">
                            </div>
                        <div class="search-field dropdown-field dropdown search-field--filter" id="propertyTypeField">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M11.25 16.5H2.25V3.75C2.25 1.8885 2.6385 1.5 4.5 1.5H9C10.8615 1.5 11.25 1.8885 11.25 3.75V16.5ZM11.25 16.5V6H13.5C15.3615 6 15.75 6.3885 15.75 8.25V16.5H11.25Z" stroke="#4B5C77" stroke-width="1.125" stroke-linejoin="round"/>
                                <path d="M6 4.5H7.5M6 6.75H7.5M6 9H7.5" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.625 16.5V13.5C8.625 12.7927 8.625 12.4395 8.40525 12.2197C8.1855 12 7.83225 12 7.125 12H6.375C5.66775 12 5.3145 12 5.09475 12.2197C4.875 12.4395 4.875 12.7927 4.875 13.5V16.5" stroke="#4B5C77" stroke-width="1.125" stroke-linejoin="round"/>
                              </svg>
                            <button type="button" class="dropdown-trigger bg-transparent border-0 w-100 text-start" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="selected-text">{{ propertyTypeTriggerText }}</span>
                            </button>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                            </svg>
                            <div class="dropdown-menu property-dropdown border-0 custom-dropdown-menu">
                                <div class="dropdown-section">
                                    <div class="dropdown-section-title">{{ t('listing.propertyTypeHeading') }}</div>
                                    <div
                                        class="option-buttons"
                                        data-type="property"
                                        :class="{ 'property-options--collapsed': !propertyTypesExpanded }"
                                    >
                                        <button
                                            v-for="(opt, idx) in propertyTypeOptions"
                                            :key="'pt-' + opt.value"
                                            type="button"
                                            :data-value="opt.value"
                                            :class="{
                                                'property-option--extra': idx >= 5 && !propertyTypesExpanded,
                                                active: selectedPropertyType === opt.value,
                                            }"
                                            @click="selectPropertyType(opt)"
                                        >
                                            {{ opt.label }}
                                        </button>
                                    </div>
                                </div>
                                <div v-if="propertyTypeOptions.length > 5" class="dropdown-footer">
                                    <button type="button" class="view-more" @click.prevent="propertyTypesExpanded = !propertyTypesExpanded">
                                        {{ propertyTypesExpanded ? t('listing.viewLess') : t('listing.viewMore') }}
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="property_type" id="hiddenPropertyType" :value="selectedPropertyType">
                        </div>
                        <div class="search-field dropdown search-field--filter" id="bedsBathsField">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M1.6875 14.625V10.6875C1.68926 10.0913 1.92688 9.52003 2.34846 9.09846C2.77003 8.67688 3.3413 8.43926 3.9375 8.4375H14.0625C14.6587 8.43926 15.23 8.67688 15.6515 9.09846C16.0731 9.52003 16.3107 10.0913 16.3125 10.6875V14.625M13.5 8.4375H3.375V4.78125C3.37611 4.40863 3.52463 4.05159 3.78811 3.78811C4.05159 3.52463 4.40863 3.37611 4.78125 3.375H13.2188C13.5914 3.37611 13.9484 3.52463 14.2119 3.78811C14.4754 4.05159 14.6239 4.40863 14.625 4.78125V8.4375H13.5Z" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M1.6875 14.625V14.3438C1.68815 14.1202 1.77725 13.9059 1.93535 13.7478C2.09344 13.5898 2.30767 13.5006 2.53125 13.5H15.4688C15.6923 13.5006 15.9066 13.5898 16.0647 13.7478C16.2227 13.9059 16.3119 14.1202 16.3125 14.3438V14.625M3.9375 8.4375V7.875C3.93833 7.57689 4.05713 7.29122 4.26793 7.08043C4.47872 6.86963 4.76439 6.75083 5.0625 6.75H7.875C8.17311 6.75083 8.45878 6.86963 8.66957 7.08043C8.88037 7.29122 8.99917 7.57689 9 7.875M9 7.875V8.4375M9 7.875C9.00083 7.57689 9.11963 7.29122 9.33043 7.08043C9.54122 6.86963 9.82689 6.75083 10.125 6.75H12.9375C13.2356 6.75083 13.5213 6.86963 13.7321 7.08043C13.9429 7.29122 14.0617 7.57689 14.0625 7.875V8.4375" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
                            <button type="button" class="dropdown-trigger bg-transparent border-0 w-100 text-start" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="selected-text">{{ t('listing.selectBedsBaths') }}</span>
                            </button>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                            </svg>
                            <div class="dropdown-menu beds-baths-dropdown border-0 custom-dropdown-menu">
                                <div class="dropdown-section">
                                    <div class="dropdown-section-title">{{ t('listing.bedrooms') }}</div>
                                    <div class="option-buttons" data-type="bedrooms">
                                        <label class="custom-check-btn"><input type="checkbox" class="btn-check bed-check" value="Studio" id="pl-bed-studio">{{ t('listing.studio') }}</label>
                                        <label class="custom-check-btn"><input type="checkbox" class="btn-check bed-check" value="1" id="pl-bed-1">1</label>
                                        <label class="custom-check-btn"><input type="checkbox" class="btn-check bed-check" value="2" id="pl-bed-2">2</label>
                                        <label class="custom-check-btn"><input type="checkbox" class="btn-check bed-check" value="3" id="pl-bed-3">3</label>
                                    </div>
                                </div>
                                <div class="dropdown-section">
                                    <div class="dropdown-section-title">{{ t('listing.bathrooms') }}</div>
                                    <div class="option-buttons" data-type="bathrooms">
                                        <label class="custom-check-btn"><input type="checkbox" class="btn-check bath-check" value="1" id="pl-bath-1">1</label>
                                        <label class="custom-check-btn"><input type="checkbox" class="btn-check bath-check" value="2" id="pl-bath-2">2</label>
                                        <label class="custom-check-btn"><input type="checkbox" class="btn-check bath-check" value="3" id="pl-bath-3">3</label>
                                    </div>
                                </div>
                                <div class="dropdown-footer">
                                    <button type="button" class="clear-filters">{{ t('listing.clearFilters') }}</button>
                                </div>
                            </div>
                            <input type="hidden" name="bedrooms" id="hiddenBedrooms">
                            <input type="hidden" name="bathrooms" id="hiddenBathrooms">
                        </div>
                        <div class="search-field dropdown-field dropdown search-field--filter" id="categoriesField">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M12.75 7.5C13.9926 7.5 15 6.49264 15 5.25C15 4.00736 13.9926 3 12.75 3C11.5074 3 10.5 4.00736 10.5 5.25C10.5 6.49264 11.5074 7.5 12.75 7.5Z" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5.25 15C6.49264 15 7.5 13.9926 7.5 12.75C7.5 11.5074 6.49264 10.5 5.25 10.5C4.00736 10.5 3 11.5074 3 12.75C3 13.9926 4.00736 15 5.25 15Z" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10.5 10.5H15V14.25C15 14.4489 14.921 14.6397 14.7803 14.7803C14.6397 14.921 14.4489 15 14.25 15H11.25C11.0511 15 10.8603 14.921 10.7197 14.7803C10.579 14.6397 10.5 14.4489 10.5 14.25V10.5ZM3 3H7.5V6.75C7.5 6.94891 7.42098 7.13968 7.28033 7.28033C7.13968 7.42098 6.94891 7.5 6.75 7.5H3.75C3.55109 7.5 3.36032 7.42098 3.21967 7.28033C3.07902 7.13968 3 6.94891 3 6.75V3Z" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                              </svg>
                            <button type="button" class="dropdown-trigger bg-transparent border-0 w-100 text-start" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="selected-text">{{ categoryTriggerText }}</span>
                            </button>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                            </svg>
                            <div class="dropdown-menu border-0 custom-dropdown-menu">
                                <div class="dropdown-section">
                                    <div class="dropdown-section-title">{{ t('listing.categoryHeading') }}</div>
                                    <div class="option-buttons">
                                        <button
                                            v-for="opt in categoryOptions"
                                            :key="'cat-' + opt.value"
                                            type="button"
                                            :data-value="opt.value"
                                            :class="{ active: selectedCategory === opt.value }"
                                            @click="selectCategory(opt)"
                                        >
                                            {{ opt.label }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="categories" id="hiddenCategories" :value="selectedCategory">
                        </div>
                        <div class="search-field dropdown-field dropdown search-field--filter" id="priceField">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M3.5459 12.1029C2.38715 10.9434 1.8074 10.3644 1.59215 9.61218C1.37615 8.85993 1.56065 8.06118 1.92965 6.46443L2.1419 5.54343C2.45165 4.19943 2.6069 3.52743 3.06665 3.06693C3.5264 2.60643 4.19915 2.45193 5.54315 2.14218L6.46415 1.92918C8.06165 1.56093 8.85965 1.37643 9.6119 1.59168C10.3641 1.80768 10.9431 2.38743 12.1019 3.54618L13.4744 4.91868C15.4926 6.93618 16.4999 7.94418 16.4999 9.19668C16.4999 10.4499 15.4919 11.4579 13.4751 13.4747C11.4576 15.4922 10.4496 16.5002 9.1964 16.5002C7.9439 16.5002 6.93515 15.4922 4.9184 13.4754L3.5459 12.1029Z" stroke="#4B5C77" stroke-width="1.125"/>
                                <path d="M11.5443 11.5443C11.983 11.1041 12.0422 10.4516 11.6762 10.0848C11.3102 9.71806 10.657 9.77806 10.2175 10.2176C9.77875 10.6571 9.1255 10.7163 8.7595 10.3503C8.3935 9.98431 8.45275 9.33106 8.89225 8.89231M8.89225 8.89231L8.62675 8.62681M8.89225 8.89231C9.1405 8.64331 9.457 8.51731 9.75175 8.51956M11.809 11.8091L11.5435 11.5436C11.2435 11.8443 10.8422 11.9673 10.5017 11.8968" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round"/>
                                <path d="M7.51792 7.72082C8.1037 7.13503 8.1037 6.18528 7.51792 5.5995C6.93213 5.01371 5.98238 5.01371 5.3966 5.5995C4.81081 6.18528 4.81081 7.13503 5.3966 7.72082C5.98238 8.3066 6.93213 8.3066 7.51792 7.72082Z" stroke="#4B5C77" stroke-width="1.125"/>
                              </svg>
                            <button type="button" class="dropdown-trigger bg-transparent border-0 w-100 text-start" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="selected-text">{{ t('listing.pricePlaceholder') }}</span>
                            </button>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                            </svg>
                            <div class="dropdown-menu price-range-dropdown price-range-dropdown--listing border-0">
                                <div class="price-range-dropdown__grid">
                                    <div class="price-range-dropdown__cell">
                                        <span class="price-range-dropdown__heading">{{ t('listing.minPrice') }}</span>
                                        <div class="dropdown price-range-pill-dd">
                                            <button type="button" class="price-range-pill-dd__toggle dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="true" aria-expanded="false">
                                                <span class="js-price-min-label">{{ t('listing.noMin') }}</span>
                                                <svg width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                    <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                                                </svg>
                                            </button>
                                            <ul class="dropdown-menu price-range-pill-dd__menu">
                                                <li><button type="button" class="dropdown-item js-price-min-opt" data-value="">{{ t('listing.noMin') }}</button></li>
                                                <li><button type="button" class="dropdown-item js-price-min-opt" data-value="25000">25,000</button></li>
                                                <li><button type="button" class="dropdown-item js-price-min-opt" data-value="50000">50,000</button></li>
                                                <li><button type="button" class="dropdown-item js-price-min-opt" data-value="100000">100,000</button></li>
                                                <li><button type="button" class="dropdown-item js-price-min-opt" data-value="250000">250,000</button></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="price-range-dropdown__cell">
                                        <span class="price-range-dropdown__heading">{{ t('listing.maxPrice') }}</span>
                                        <div class="dropdown price-range-pill-dd">
                                            <button type="button" class="price-range-pill-dd__toggle dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="true" aria-expanded="false">
                                                <span class="js-price-max-label">{{ t('listing.noMax') }}</span>
                                                <svg width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                    <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                                                </svg>
                                            </button>
                                            <ul class="dropdown-menu price-range-pill-dd__menu">
                                                <li><button type="button" class="dropdown-item js-price-max-opt" data-value="">{{ t('listing.noMax') }}</button></li>
                                                <li><button type="button" class="dropdown-item js-price-max-opt" data-value="100000">100,000</button></li>
                                                <li><button type="button" class="dropdown-item js-price-max-opt" data-value="250000">250,000</button></li>
                                                <li><button type="button" class="dropdown-item js-price-max-opt" data-value="500000">500,000</button></li>
                                                <li><button type="button" class="dropdown-item js-price-max-opt" data-value="1000000">1,000,000</button></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="min_price" id="hiddenMinPrice" value="">
                            <input type="hidden" name="max_price" id="hiddenMaxPrice" value="">
                        </div>
                        <button type="submit" class="search-btn search-btn--listing" :aria-label="t('listing.searchAria')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M14.7982 13.7332L12.0157 10.9732C13.0957 9.62648 13.6188 7.91715 13.4773 6.19665C13.3358 4.47615 12.5404 2.87526 11.2548 1.72316C9.9692 0.571055 8.29103 -0.0446921 6.56537 0.00252822C4.8397 0.0497485 3.19772 0.756347 1.97703 1.97703C0.756347 3.19772 0.0497485 4.8397 0.00252822 6.56537C-0.0446921 8.29103 0.571055 9.9692 1.72316 11.2548C2.87526 12.5404 4.47615 13.3358 6.19665 13.4773C7.91715 13.6188 9.62648 13.0957 10.9732 12.0157L13.7332 14.7757C13.8029 14.846 13.8858 14.9018 13.9772 14.9398C14.0686 14.9779 14.1667 14.9975 14.2657 14.9975C14.3647 14.9975 14.4627 14.9779 14.5541 14.9398C14.6455 14.9018 14.7285 14.846 14.7982 14.7757C14.9334 14.6358 15.0089 14.4489 15.0089 14.2544C15.0089 14.0599 14.9334 13.873 14.7982 13.7332ZM6.76568 12.0157C5.72732 12.0157 4.71229 11.7078 3.84893 11.1309C2.98557 10.554 2.31267 9.73408 1.91531 8.77476C1.51795 7.81545 1.41398 6.75985 1.61655 5.74145C1.81912 4.72305 2.31914 3.78759 3.05336 3.05336C3.78759 2.31914 4.72305 1.81912 5.74145 1.61655C6.75985 1.41398 7.81545 1.51795 8.77476 1.91531C9.73408 2.31267 10.554 2.98557 11.1309 3.84893C11.7078 4.71229 12.0157 5.72732 12.0157 6.76568C12.0157 8.15806 11.4626 9.49342 10.478 10.478C9.49342 11.4626 8.15806 12.0157 6.76568 12.0157Z" fill="white" />
                            </svg>
                            <span class="search-btn__text">{{ t('listing.search') }}</span>
                        </button>
                    </form>
                </div>
                </div>
            </div>
        </div>
        <div class="container-ctn">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="properties-listing__head">
                    <h2>{{ t('listing.exploreTitle') }}</h2>
                </div>
                <div class="listing-toolbar">
                    <RouterLink :to="{ name: 'map' }" class="listing-toolbar__btn text-decoration-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M20 10C20 16.5 12 22 12 22C12 22 4 16.5 4 10C4 7.87827 4.84285 5.84344 6.34315 4.34315C7.84344 2.84285 9.87827 2 12 2C14.1217 2 16.1566 2.84285 17.6569 4.34315C19.1571 5.84344 20 7.87827 20 10Z" stroke="#4B5C77" stroke-width="2"/>
                            <path d="M15 10C15 10.7956 14.6839 11.5587 14.1213 12.1213C13.5587 12.6839 12.7956 13 12 13C11.2044 13 10.4413 12.6839 9.87868 12.1213C9.31607 11.5587 9 10.7956 9 10C9 9.20435 9.31607 8.44129 9.87868 7.87868C10.4413 7.31607 11.2044 7 12 7C12.7956 7 13.5587 7.31607 14.1213 7.87868C14.6839 8.44129 15 9.20435 15 10Z" stroke="#4B5C77" stroke-width="2"/>
                          </svg>
                        {{ t('listing.map') }}
                    </RouterLink>
                    <div class="listing-toolbar__views" role="group" :aria-label="t('listing.layoutAria')">
                        <button type="button" class="listing-toolbar__view-btn" data-listing-view="list" :aria-label="t('listing.listViewAria')" aria-pressed="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M8 9C7.71667 9 7.47934 8.904 7.288 8.712C7.09667 8.52 7.00067 8.28267 7 8C6.99934 7.71733 7.09534 7.48 7.288 7.288C7.48067 7.096 7.718 7 8 7H20C20.2833 7 20.521 7.096 20.713 7.288C20.905 7.48 21.0007 7.71733 21 8C20.9993 8.28267 20.9033 8.52033 20.712 8.713C20.5207 8.90567 20.2833 9.00133 20 9H8ZM8 13C7.71667 13 7.47934 12.904 7.288 12.712C7.09667 12.52 7.00067 12.2827 7 12C6.99934 11.7173 7.09534 11.48 7.288 11.288C7.48067 11.096 7.718 11 8 11H20C20.2833 11 20.521 11.096 20.713 11.288C20.905 11.48 21.0007 11.7173 21 12C20.9993 12.2827 20.9033 12.5203 20.712 12.713C20.5207 12.9057 20.2833 13.0013 20 13H8ZM8 17C7.71667 17 7.47934 16.904 7.288 16.712C7.09667 16.52 7.00067 16.2827 7 16C6.99934 15.7173 7.09534 15.48 7.288 15.288C7.48067 15.096 7.718 15 8 15H20C20.2833 15 20.521 15.096 20.713 15.288C20.905 15.48 21.0007 15.7173 21 16C20.9993 16.2827 20.9033 16.5203 20.712 16.713C20.5207 16.9057 20.2833 17.0013 20 17H8ZM4 9C3.71667 9 3.47934 8.904 3.288 8.712C3.09667 8.52 3.00067 8.28267 3 8C2.99934 7.71733 3.09534 7.48 3.288 7.288C3.48067 7.096 3.718 7 4 7C4.282 7 4.51967 7.096 4.713 7.288C4.90634 7.48 5.002 7.71733 5 8C4.998 8.28267 4.902 8.52033 4.712 8.713C4.522 8.90567 4.28467 9.00133 4 9ZM4 13C3.71667 13 3.47934 12.904 3.288 12.712C3.09667 12.52 3.00067 12.2827 3 12C2.99934 11.7173 3.09534 11.48 3.288 11.288C3.48067 11.096 3.718 11 4 11C4.282 11 4.51967 11.096 4.713 11.288C4.90634 11.48 5.002 11.7173 5 12C4.998 12.2827 4.902 12.5203 4.712 12.713C4.522 12.9057 4.28467 13.0013 4 13ZM4 17C3.71667 17 3.47934 16.904 3.288 16.712C3.09667 16.52 3.00067 16.2827 3 16C2.99934 15.7173 3.09534 15.48 3.288 15.288C3.48067 15.096 3.718 15 4 15C4.282 15 4.51967 15.096 4.713 15.288C4.90634 15.48 5.002 15.7173 5 16C4.998 16.2827 4.902 16.5203 4.712 16.713C4.522 16.9057 4.28467 17.0013 4 17Z" fill="#4B5C77"/>
                              </svg>
                        </button>
                        <button type="button" class="listing-toolbar__view-btn is-active" data-listing-view="grid" :aria-label="t('listing.gridViewAria')" aria-pressed="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M8.885 10.5H5.615C5.155 10.5 4.771 10.346 4.463 10.038C4.155 9.73 4.00067 9.34567 4 8.885V5.615C4 5.155 4.15433 4.771 4.463 4.463C4.77167 4.155 5.156 4.00067 5.616 4H8.885C9.345 4 9.72933 4.15433 10.038 4.463C10.3467 4.77167 10.5007 5.156 10.5 5.616V8.885C10.5 9.345 10.346 9.72933 10.038 10.038C9.73 10.3467 9.34567 10.5007 8.885 10.5ZM5.615 9.5H8.885C9.06433 9.5 9.21167 9.44233 9.327 9.327C9.44233 9.21167 9.5 9.06433 9.5 8.885V5.615C9.5 5.43567 9.44233 5.28833 9.327 5.173C9.21167 5.05767 9.06433 5 8.885 5H5.615C5.43567 5 5.28833 5.05767 5.173 5.173C5.05767 5.28833 5 5.436 5 5.616V8.885C5 9.06433 5.05767 9.21167 5.173 9.327C5.28833 9.44233 5.436 9.5 5.616 9.5M8.885 20H5.615C5.155 20 4.771 19.846 4.463 19.538C4.155 19.23 4.00067 18.8453 4 18.384V15.116C4 14.6553 4.15433 14.271 4.463 13.963C4.77167 13.655 5.156 13.5007 5.616 13.5H8.885C9.345 13.5 9.72933 13.6543 10.038 13.963C10.3467 14.2717 10.5007 14.656 10.5 15.116V18.385C10.5 18.845 10.346 19.2293 10.038 19.538C9.73 19.8467 9.34567 20.0007 8.885 20ZM5.615 19H8.885C9.06433 19 9.21167 18.9423 9.327 18.827C9.44233 18.7117 9.5 18.5643 9.5 18.385V15.115C9.5 14.9357 9.44233 14.7883 9.327 14.673C9.21167 14.5577 9.06433 14.5 8.885 14.5H5.615C5.43567 14.5 5.28833 14.5577 5.173 14.673C5.05767 14.7883 5 14.936 5 15.116V18.385C5 18.5643 5.05767 18.7117 5.173 18.827C5.28833 18.9423 5.436 19 5.616 19M18.385 10.5H15.115C14.655 10.5 14.271 10.346 13.963 10.038C13.655 9.73 13.5007 9.34567 13.5 8.885V5.615C13.5 5.155 13.6543 4.771 13.963 4.463C14.2717 4.155 14.656 4.00067 15.116 4H18.385C18.845 4 19.2293 4.15433 19.538 4.463C19.8467 4.77167 20.0007 5.156 20 5.616V8.885C20 9.345 19.846 9.72933 19.538 10.038C19.23 10.3467 18.8463 10.5007 18.385 10.5ZM15.115 9.5H18.385C18.5643 9.5 18.7117 9.44233 18.827 9.327C18.9423 9.21167 19 9.06433 19 8.885V5.615C19 5.43567 18.9423 5.28833 18.827 5.173C18.7117 5.05767 18.5643 5 18.385 5H15.115C14.9357 5 14.7883 5.05767 14.673 5.173C14.5577 5.28833 14.5 5.436 14.5 5.616V8.885C14.5 9.06433 14.5577 9.21167 14.673 9.327C14.7883 9.44233 14.936 9.5 15.116 9.5M18.385 20H15.115C14.655 20 14.271 19.846 13.963 19.538C13.655 19.23 13.5007 18.8453 13.5 18.384V15.116C13.5 14.6553 13.6543 14.271 13.963 13.963C14.2717 13.655 14.656 13.5007 15.116 13.5H18.385C18.845 13.5 19.2293 13.6543 19.538 13.963C19.8467 14.2717 20.0007 14.656 20 15.116V18.385C20 18.845 19.846 19.2293 19.538 19.538C19.23 19.8467 18.8463 20.0007 18.385 20ZM15.115 19H18.385C18.5643 19 18.7117 18.9423 18.827 18.827C18.9423 18.7117 19 18.5643 19 18.385V15.115C19 14.9357 18.9423 14.7883 18.827 14.673C18.7117 14.5577 18.5643 14.5 18.385 14.5H15.115C14.9357 14.5 14.7883 14.5577 14.673 14.673C14.5577 14.7883 14.5 14.936 14.5 15.116V18.385C14.5 18.5643 14.5577 18.7117 14.673 18.827C14.7883 18.9423 14.936 19 15.116 19" fill="#4B5C77"/>
                              </svg>
                        </button>
                    </div>
                </div>
           
            </div>
            <OurPropertyListingGrid ref="listingGridRef" />
        </div>
    </section>



</template>

<script setup>
import { computed, onMounted, provide, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { asset } from '@/utils/asset';
import OurPropertyListingGrid from '@/components/OurPropertyListingGrid.vue';

const { locale, t } = useI18n({ useScope: 'global' });

const listingGridRef = ref(null);
const propertyTypeOptions = ref([]);
const categoryOptions = ref([]);
const selectedPropertyType = ref('');
const selectedCategory = ref('');
const propertyTypesExpanded = ref(false);

const dreVueListingFilters = computed(() => ({
    property_type: selectedPropertyType.value,
    categories: selectedCategory.value,
}));
provide('dreVueListingFilters', dreVueListingFilters);

const propertyTypeTriggerText = computed(() => {
    const o = propertyTypeOptions.value.find((x) => x.value === selectedPropertyType.value);
    return o ? o.label : t('listing.selectPropertyType');
});

const categoryTriggerText = computed(() => {
    const o = categoryOptions.value.find((x) => x.value === selectedCategory.value);
    return o ? o.label : t('listing.categoriesPlaceholder');
});

function selectPropertyType(opt) {
    selectedPropertyType.value = selectedPropertyType.value === opt.value ? '' : opt.value;
}

function selectCategory(opt) {
    selectedCategory.value = selectedCategory.value === opt.value ? '' : opt.value;
}

async function loadFilterOptions() {
    try {
        const { data } = await window.axios.get('/api/properties/filter-options', {
            params: { lang: locale.value || 'en' },
        });
        propertyTypeOptions.value = Array.isArray(data.property_types) ? data.property_types : [];
        categoryOptions.value = Array.isArray(data.categories) ? data.categories : [];
    } catch (e) {
        console.error(e);
        propertyTypeOptions.value = [];
        categoryOptions.value = [];
    }
    if (selectedPropertyType.value && !propertyTypeOptions.value.some((x) => x.value === selectedPropertyType.value)) {
        selectedPropertyType.value = '';
    }
    if (selectedCategory.value && !categoryOptions.value.some((x) => x.value === selectedCategory.value)) {
        selectedCategory.value = '';
    }
}

async function onListingSearch() {
    await listingGridRef.value?.loadFirstPage?.();
}

onMounted(loadFilterOptions);
watch(locale, loadFilterOptions);
</script>
