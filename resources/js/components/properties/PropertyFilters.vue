<template>
    <div class="properties-listing__search p-0">
        <form @submit.prevent="onSearch" class="search-form search-form--listing">
            <!-- Location Search with Autocomplete -->
            <div class="search-field dropdown search-field--location" id="locationField">
                <svg class="search-field__lead-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                    <path d="M9 1.5C5.7 1.5 3 4.2 3 7.5C3 11.55 8.25 16.125 8.475 16.35C8.625 16.425 8.85 16.5 9 16.5C9.15 16.5 9.375 16.425 9.525 16.35C9.75 16.125 15 11.55 15 7.5C15 4.2 12.3 1.5 9 1.5ZM9 14.775C7.425 13.275 4.5 10.05 4.5 7.5C4.5 5.025 6.525 3 9 3C11.475 3 13.5 5.025 13.5 7.5C13.5 9.975 10.575 13.275 9 14.775ZM9 4.5C7.35 4.5 6 5.85 6 7.5C6 9.15 7.35 10.5 9 10.5C10.65 10.5 12 9.15 12 7.5C12 5.85 10.65 4.5 9 4.5ZM9 9C8.175 9 7.5 8.325 7.5 7.5C7.5 6.675 8.175 6 9 6C9.825 6 10.5 6.675 10.5 7.5C10.5 8.325 9.825 9 9 9Z" fill="#4B5C77" />
                </svg>
                <input 
                    type="text" 
                    class="form-control bg-transparent border-0" 
                    :placeholder="t('listing.locationPlaceholder')"
                    v-model="filters.location"
                    autocomplete="off"
                    @focus="locationDropdownOpen = true"
                    @input="locationDropdownOpen = true"
                    @blur="onLocationBlur"
                >
                
                <!-- Location Suggestions Dropdown (Home Style) -->
                <div v-if="locationDropdownOpen && locationSuggestions.length" class="search-results-dropdown show" id="searchResults">
                    <div class="search-results-title">{{ t('listing.popularLocations') }}</div>
                    <div class="search-results-list custom-scrollbar">
                        <button 
                            v-for="opt in locationSuggestions" 
                            :key="opt.value"
                            type="button"
                            class="search-results-item w-100 text-start"
                            @mousedown.prevent="selectLocation(opt.value)"
                        >
                            <div class="search-results-icon">
                                <svg class="search-field__lead-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"> <path d="M9 1.5C5.7 1.5 3 4.2 3 7.5C3 11.55 8.25 16.125 8.475 16.35C8.625 16.425 8.85 16.5 9 16.5C9.15 16.5 9.375 16.425 9.525 16.35C9.75 16.125 15 11.55 15 7.5C15 4.2 12.3 1.5 9 1.5ZM9 14.775C7.425 13.275 4.5 10.05 4.5 7.5C4.5 5.025 6.525 3 9 3C11.475 3 13.5 5.025 13.5 7.5C13.5 9.975 10.575 13.275 9 14.775ZM9 4.5C7.35 4.5 6 5.85 6 7.5C6 9.15 7.35 10.5 9 10.5C10.65 10.5 12 9.15 12 7.5C12 5.85 10.65 4.5 9 4.5ZM9 9C8.175 9 7.5 8.325 7.5 7.5C7.5 6.675 8.175 6 9 6C9.825 6 10.5 6.675 10.5 7.5C10.5 8.325 9.825 9 9 9Z" fill="#4B5C77"></path> </svg>
                            </div>
                            <div class="search-results-content">
                                <div class="search-results-name" v-html="highlightMatch(locationOptionName(opt), filters.location)"></div>
                                <div class="search-results-city" v-if="locationOptionSubtitle(opt)">{{ locationOptionSubtitle(opt) }}</div>
                            </div>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="location" :value="filters.location">
            </div>

            <!-- Property Type Dropdown -->
            <div class="search-field dropdown-field dropdown search-field--filter" id="propertyTypeField">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M11.25 16.5H2.25V3.75C2.25 1.8885 2.6385 1.5 4.5 1.5H9C10.8615 1.5 11.25 1.8885 11.25 3.75V16.5ZM11.25 16.5V6H13.5C15.3615 6 15.75 6.3885 15.75 8.25V16.5H11.25Z" stroke="#4B5C77" stroke-width="1.125" stroke-linejoin="round"/>
                    <path d="M6 4.5H7.5M6 6.75H7.5M6 9H7.5" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.625 16.5V13.5C8.625 12.7927 8.625 12.4395 8.40525 12.2197C8.1855 12 7.83225 12 7.125 12H6.375C5.66775 12 5.3145 12 5.09475 12.2197C4.875 12.4395 4.875 12.7927 4.875 13.5V16.5" stroke="#4B5C77" stroke-width="1.125" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="dropdown-trigger bg-transparent border-0 w-100 text-start" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
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
                            :class="{ 'property-options--collapsed': !propertyTypesExpanded }"
                        >
                            <button
                            v-for="(opt, idx) in visiblePropertyTypeOptions"
                            :key="'pt-' + opt.value"
                            type="button"
                            :class="{
                                'property-option--extra': idx >= 5 && !propertyTypesExpanded,
                                active: filters.property_type === opt.value,
                                }"
                                @click="selectPropertyType(opt)"
                            >
                                {{ opt.label }}
                            </button>
                        </div>
                    </div>
                    <div v-if="visiblePropertyTypeOptions.length > 5" class="dropdown-footer">
                        <button type="button" class="view-more" @click.stop="propertyTypesExpanded = !propertyTypesExpanded">
                            {{ propertyTypesExpanded ? t('listing.viewLess') : t('listing.viewMore') }}
                        </button>
                    </div>
                </div>
                <input type="hidden" name="property_type" :value="filters.property_type">
            </div>

            <!-- Beds & Baths Dropdown -->
            <div class="search-field dropdown search-field--filter" id="bedsBathsField">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M1.6875 14.625V10.6875C1.68926 10.0913 1.92688 9.52003 2.34846 9.09846C2.77003 8.67688 3.3413 8.43926 3.9375 8.4375H14.0625C14.6587 8.43926 15.23 8.67688 15.6515 9.09846C16.0731 9.52003 16.3107 10.0913 16.3125 10.6875V14.625M13.5 8.4375H3.375V4.78125C3.37611 4.40863 3.52463 4.05159 3.78811 3.78811C4.05159 3.52463 4.40863 3.37611 4.78125 3.375H13.2188C13.5914 3.37611 13.9484 3.52463 14.2119 3.78811C14.4754 4.05159 14.6239 4.40863 14.625 4.78125V8.4375H13.5Z" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M1.6875 14.625V14.3438C1.68815 14.1202 1.77725 13.9059 1.93535 13.7478C2.09344 13.5898 2.30767 13.5006 2.53125 13.5H15.4688C15.6923 13.5006 15.9066 13.5898 16.0647 13.7478C16.2227 13.9059 16.3119 14.1202 16.3125 14.3438V14.625M3.9375 8.4375V7.875C3.93833 7.57689 4.05713 7.29122 4.26793 7.08043C4.47872 6.86963 4.76439 6.75083 5.0625 6.75H7.875C8.17311 6.75083 8.45878 6.86963 8.66957 7.08043C8.88037 7.29122 8.99917 7.57689 9 7.875M9 7.875V8.4375M9 7.875C9.00083 7.57689 9.11963 7.29122 9.33043 7.08043C9.54122 6.86963 9.82689 6.75083 10.125 6.75H12.9375C13.2356 6.75083 13.5213 6.86963 13.7321 7.08043C13.9429 7.29122 14.0617 7.57689 14.0625 7.875V8.4375" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="dropdown-trigger bg-transparent border-0 w-100 text-start" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                    <span class="selected-text">{{ bedsBathsTriggerText }}</span>
                </button>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                </svg>
                <div class="dropdown-menu beds-baths-dropdown border-0 custom-dropdown-menu">
                    <div class="dropdown-section">
                        <div class="dropdown-section-title">{{ t('listing.bedrooms') }}</div>
                        <div class="option-buttons" data-type="bedrooms">
                            <label v-for="val in bedroomOptions" :key="'bed-' + val" class="custom-check-btn">
                                <input type="radio" name="bedrooms" class="btn-check" :value="val" v-model="filters.bedrooms" @change="selectBedroom(val)">
                                {{ val }}
                            </label>
                        </div>
                    </div>
                    <div class="dropdown-section">
                        <div class="dropdown-section-title">{{ t('listing.bathrooms') }}</div>
                        <div class="option-buttons" data-type="bathrooms">
                            <label v-for="val in bathroomOptions" :key="'bath-' + val" class="custom-check-btn">
                                <input type="radio" name="bathrooms" class="btn-check" :value="val" v-model="filters.bathrooms" @change="selectBathroom(val)">
                                {{ val }}
                            </label>
                        </div>
                    </div>
                    <div class="dropdown-footer border-0 pt-0">
                        <button type="button" class="clear-filters border-0 bg-transparent text-primary small" @click="clearBedsBaths">{{ t('listing.clearFilters') }}</button>
                    </div>
                </div>
                <input type="hidden" name="bedrooms" :value="filters.bedrooms">
                <input type="hidden" name="bathrooms" :value="filters.bathrooms">
            </div>

            <!-- Categories Dropdown -->
            <div class="search-field dropdown-field dropdown search-field--filter" id="categoriesField">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M12.75 7.5C13.9926 7.5 15 6.49264 15 5.25C15 4.00736 13.9926 3 12.75 3C11.5074 3 10.5 4.00736 10.5 5.25C10.5 6.49264 11.5074 7.5 12.75 7.5Z" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5.25 15C6.49264 15 7.5 13.9926 7.5 12.75C7.5 11.5074 6.49264 10.5 5.25 10.5C4.00736 10.5 3 11.5074 3 12.75C3 13.9926 4.00736 15 5.25 15Z" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10.5 10.5H15V14.25C15 14.4489 14.921 14.6397 14.7803 14.7803C14.6397 14.921 14.4489 15 14.25 15H11.25C11.0511 15 10.8603 14.921 10.7197 14.7803C10.579 14.6397 10.5 14.4489 10.5 14.25V10.5ZM3 3H7.5V6.75C7.5 6.94891 7.42098 7.13968 7.28033 7.28033C7.13968 7.42098 6.94891 7.5 6.75 7.5H3.75C3.55109 7.5 3.36032 7.42098 3.21967 7.28033C3.07902 7.13968 3 6.94891 3 6.75V3Z" stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <button type="button" class="dropdown-trigger bg-transparent border-0 w-100 text-start" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
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
                            v-for="opt in visibleCategoryOptions"
                            :key="'cat-' + opt.value"
                            type="button"
                            :class="{ active: filters.categories === opt.value }"
                                @click="selectCategory(opt)"
                            >
                                {{ opt.label }}
                            </button>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="categories" :value="filters.categories">
            </div>

            <!-- Price Range Dropdown -->
            <div class="search-field dropdown-field dropdown search-field--filter" id="priceField">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M3.5459 12.1029C2.38715 10.9434 1.8074 10.3644 1.59215 9.61218C1.37615 8.85993 1.56065 8.06118 1.92965 6.46443L2.1419 5.54343C2.45165 4.19943 2.6069 3.52743 3.06665 3.06693C3.5264 2.60643 4.19915 2.45193 5.54315 2.14218L6.46415 1.92918C8.06165 1.56093 8.85965 1.37643 9.6119 1.59168C10.3641 1.80768 10.9431 2.38743 12.1019 3.54618L13.4744 4.91868C15.4926 6.93618 16.4999 7.94418 16.4999 9.19668C16.4999 10.4499 15.4919 11.4579 13.4751 13.4747C11.4576 15.4922 10.4496 16.5002 9.1964 16.5002C7.9439 16.5002 6.93515 15.4922 4.9184 13.4754L3.5459 12.1029Z" stroke="#4B5C77" stroke-width="1.125"/>
                </svg>
                <button type="button" class="dropdown-trigger bg-transparent border-0 w-100 text-start" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                    <span class="selected-text">{{ priceTriggerText }}</span>
                </button>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                </svg>
                <div class="dropdown-menu price-range-dropdown--listing border-0 shadow">
                    <div class="price-range-slider-container">
                        <div ref="priceSliderRef" class="price-range-slider"></div>
                        <div class="price-range-slider-display">
                            <div class="price-range-slider-display__field">
                                <input
                                    type="text"
                                    :value="formatPriceInput(filters.min_price || 0)"
                                    @change="setPriceInput('min', $event)"
                                    @blur="normalizePriceInput('min', $event)"
                                >
                                <span class="currency">AED</span>
                            </div>
                            <span class="slider-sep">-</span>
                            <div class="price-range-slider-display__field">
                                <input
                                    type="text"
                                    :value="formatPriceInput(filters.max_price || PRICE_MAX)"
                                    @change="setPriceInput('max', $event)"
                                    @blur="normalizePriceInput('max', $event)"
                                >
                                <span class="currency">AED</span>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="min_price" :value="filters.min_price">
                <input type="hidden" name="max_price" :value="filters.max_price">
            </div>

            <button v-if="showSearchButton" type="submit" class="search-btn search-btn--listing">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                    <path d="M14.7982 13.7332L12.0157 10.9732C13.0957 9.62648 13.6188 7.91715 13.4773 6.19665C13.3358 4.47615 12.5404 2.87526 11.2548 1.72316C9.9692 0.571055 8.29103 -0.0446921 6.56537 0.00252822C4.8397 0.0497485 3.19772 0.756347 1.97703 1.97703C0.756347 3.19772 0.0497485 4.8397 0.00252822 6.56537C-0.0446921 8.29103 0.571055 9.9692 1.72316 11.2548C2.87526 12.5404 4.47615 13.3358 6.19665 13.4773C7.91715 13.6188 9.62648 13.0957 10.9732 12.0157L13.7332 14.7757C13.8029 14.846 13.8858 14.9018 13.9772 14.9398C14.0686 14.9779 14.1667 14.9975 14.2657 14.9975C14.3647 14.9975 14.4627 14.9779 14.5541 14.9398C14.6455 14.9018 14.7285 14.846 14.7982 14.7757C14.9334 14.6358 15.0089 14.4489 15.0089 14.2544C15.0089 14.0599 14.9334 13.873 14.7982 13.7332ZM6.76568 12.0157C5.72732 12.0157 4.71229 11.7078 3.84893 11.1309C2.98557 10.554 2.31267 9.73408 1.91531 8.77476C1.51795 7.81545 1.41398 6.75985 1.61655 5.74145C1.81912 4.72305 2.31914 3.78759 3.05336 3.05336C3.78759 2.31914 4.72305 1.81912 5.74145 1.61655C6.75985 1.41398 7.81545 1.51795 8.77476 1.91531C9.73408 2.31267 10.554 2.98557 11.1309 3.84893C11.7078 4.71229 12.0157 5.72732 12.0157 6.76568C12.0157 8.15806 11.4626 9.49342 10.478 10.478C9.49342 11.4626 8.15806 12.0157 6.76568 12.0157Z" fill="white" />
                </svg>
                <span class="search-btn__text d-none d-lg-inline">{{ t('listing.search') }}</span>
            </button>
        </form>
    </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({
            property_type: '',
            categories: '',
            bedrooms: '',
            bathrooms: '',
            location: '',
            min_price: '',
            max_price: '',
        })
    },
    showSearchButton: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['update:modelValue', 'search']);

const { locale, t } = useI18n({ useScope: 'global' });

// Internal filter state
const filters = ref({ ...props.modelValue });

watch(() => props.modelValue, (newVal) => {
    // Only update if external value is truly different to avoid infinite loops
    const hasChanged = JSON.stringify(newVal) !== JSON.stringify(filters.value);
    if (hasChanged) {
        filters.value = { ...newVal };
        normalizeFilters();
        syncPriceSlider();
    }
}, { deep: true });

// Generate a unique ID for radio button names to avoid conflicts if multiple filters exist
const uid = Math.random().toString(36).substring(2, 9);

// Options and Constants
const PRICE_MIN = 0;
const PRICE_MAX = 1000000;
const PRICE_STEP = 5000;
const MAX_LOCATION_SUGGESTIONS = 4;
const bedroomOptions = ['1', '2', '3', '4', '5', '6', '7', '7+'];
const bathroomOptions = ['1', '2', '3', '4', '5', '6', '7', '7+'];

const propertyTypeOptions = ref([]);
const categoryOptions = ref([]);
const locationOptions = ref([]);
const locationDropdownOpen = ref(false);
const propertyTypesExpanded = ref(false);
const priceSliderRef = ref(null);
let priceSliderRetry = null;

// Computed trigger texts
const propertyTypeTriggerText = computed(() => {
    if (!filters.value.property_type) {
        return t('listing.selectPropertyType');
    }

    const o = propertyTypeOptions.value.find((x) => x.value === filters.value.property_type);
    return o ? o.label : t('listing.selectPropertyType');
});

const visiblePropertyTypeOptions = computed(() => {
    return propertyTypeOptions.value.filter((option) => String(option.value ?? '').trim() !== '');
});

const visibleCategoryOptions = computed(() => {
    return categoryOptions.value.filter((option) => String(option.value ?? '').trim() !== '');
});

const bedsBathsTriggerText = computed(() => {
    const beds = filters.value.bedrooms;
    const baths = filters.value.bathrooms;
    if (!beds && !baths) return t('listing.selectBedsBaths');
    const parts = [];
    if (beds && beds !== 'Studio') parts.push(`${beds} Bed`);
    if (baths) parts.push(`${baths} Bath`);
    return parts.join(' | ');
});

const categoryTriggerText = computed(() => {
    const o = categoryOptions.value.find((x) => x.value === filters.value.categories);
    return o ? o.label : t('listing.categoriesPlaceholder');
});

const priceTriggerText = computed(() => {
    const min = Number(filters.value.min_price);
    const max = Number(filters.value.max_price);
    const minActive = min > PRICE_MIN;
    const maxActive = max < PRICE_MAX && max > 0;
    
    if (minActive && maxActive) return `${formatPriceInput(min)} - ${formatPriceInput(max)} AED`;
    if (minActive) return `From ${formatPriceInput(min)} AED`;
    if (maxActive) return `Up to ${formatPriceInput(max)} AED`;
    return t('listing.pricePlaceholder');
});

// Location Suggestions
const cleanLocationOptions = computed(() => {
    const seen = new Set();

    return locationOptions.value.filter((opt) => {
        const type = locationOptionType(opt).toLowerCase();
        const name = locationOptionName(opt).trim();
        const key = name.toLowerCase();

        if (!['city', 'community'].includes(type) || !name || seen.has(key)) {
            return false;
        }

        seen.add(key);
        return true;
    });
});

const locationSuggestions = computed(() => {
    const search = (filters.value.location || '').trim().toLowerCase();
    const options = cleanLocationOptions.value.length ? cleanLocationOptions.value : locationOptions.value;

    if (!search) return options.slice(0, MAX_LOCATION_SUGGESTIONS);

    return options.filter(opt => {
        const name = locationOptionName(opt).toLowerCase();
        const subtitle = locationOptionSubtitle(opt).toLowerCase();
        return name.includes(search) || subtitle.includes(search);
    }).slice(0, MAX_LOCATION_SUGGESTIONS);
});

function locationOptionMeta(option) {
    const raw = option?.label;
    if (raw && typeof raw === 'object') {
        const localized = raw[locale.value] || raw.en || raw;
        return localized && typeof localized === 'object' ? localized : { label: localized };
    }
    return {
        label: raw || option?.value || '',
        subtitle: option?.subtitle || option?.city || '',
    };
}

function locationOptionName(option) {
    return String(locationOptionMeta(option).label || option?.value || '');
}

function locationOptionSubtitle(option) {
    const meta = locationOptionMeta(option);
    return String(meta.subtitle || meta.city || option?.subtitle || option?.city || '');
}

function locationOptionType(option) {
    const meta = locationOptionMeta(option);
    return String(meta.type || option?.type || '');
}

function selectLocation(val) {
    const opt = locationOptions.value.find(o => o.value === val);
    filters.value.location = opt ? locationOptionName(opt) : val;
    locationDropdownOpen.value = false;
    emitChange();
}

function onLocationBlur() {
    // Delay to allow mousedown on suggestions
    setTimeout(() => {
        locationDropdownOpen.value = false;
    }, 200);
}

function highlightMatch(text, search) {
    if (!search || !text) return text;
    const regex = new RegExp(`(${search})`, 'gi');
    return text.replace(regex, '<strong>$1</strong>');
}

// Filter Actions
function selectPropertyType(opt) {
    filters.value.property_type = filters.value.property_type === opt.value ? '' : opt.value;
    emitChange();
    closeDropdown('propertyTypeField');
}

function selectCategory(opt) {
    filters.value.categories = filters.value.categories === opt.value ? '' : opt.value;
    emitChange();
    closeDropdown('categoriesField');
}

function selectBedroom(value) {
    filters.value.bedrooms = value;
    emitChange();
    closeDropdown('bedsBathsField');
}

function selectBathroom(value) {
    filters.value.bathrooms = value;
    emitChange();
    closeDropdown('bedsBathsField');
}

function clearBedsBaths() {
    filters.value.bedrooms = '';
    filters.value.bathrooms = '';
    emitChange();
}

function closeDropdown(fieldId) {
    if (typeof window === 'undefined') return;

    const trigger = document.querySelector(`#${fieldId} [data-bs-toggle="dropdown"]`);
    const dropdown = window.bootstrap?.Dropdown?.getOrCreateInstance(trigger);
    dropdown?.hide();
}

function closeSearchOffcanvas() {
    if (typeof window === 'undefined') return;

    const panel = document.getElementById('listingSearchOffcanvas');
    if (!panel?.classList.contains('show')) return;

    const offcanvas = window.bootstrap?.Offcanvas?.getOrCreateInstance(panel);
    offcanvas?.hide();
}

function normalizeFilters() {
    if (filters.value.bedrooms === 'Studio') {
        filters.value.bedrooms = '';
    }
}

// Price Slider Logic
function normalizePriceNumber(value, fallback) {
    const raw = String(value ?? '').replace(/,/g, '').trim();
    if (raw === '') return fallback;
    const n = Number(raw);
    if (!Number.isFinite(n)) return fallback;
    return Math.min(PRICE_MAX, Math.max(PRICE_MIN, Math.round(n / PRICE_STEP) * PRICE_STEP));
}

function formatPriceInput(value) {
    const n = Number(value);
    return Number.isFinite(n) ? n.toLocaleString('en-US') : '';
}

function syncPriceSlider() {
    const slider = priceSliderRef.value;
    if (!slider?.noUiSlider) return;
    slider.noUiSlider.set([filters.value.min_price || PRICE_MIN, filters.value.max_price || PRICE_MAX]);
}

function setPriceRange(min, max, syncSlider = false) {
    let nextMin = normalizePriceNumber(min, PRICE_MIN);
    let nextMax = normalizePriceNumber(max, PRICE_MAX);
    if (nextMin > nextMax) {
        if (syncSlider) nextMin = Math.min(nextMin, nextMax);
        else [nextMin, nextMax] = [nextMax, nextMin];
    }
    filters.value.min_price = nextMin > PRICE_MIN ? String(nextMin) : '';
    filters.value.max_price = nextMax < PRICE_MAX ? String(nextMax) : '';
    if (syncSlider) syncPriceSlider();
    emitChange();
}

function setPriceInput(kind, event) {
    const raw = event?.target?.value ?? '';
    const value = normalizePriceNumber(raw, kind === 'min' ? PRICE_MIN : PRICE_MAX);
    if (kind === 'min') setPriceRange(value, filters.value.max_price || PRICE_MAX, true);
    else setPriceRange(filters.value.min_price || PRICE_MIN, value, true);
}

function normalizePriceInput(kind, event) {
    setPriceInput(kind, event);
    if (event?.target) {
        event.target.value = formatPriceInput(kind === 'min' ? (filters.value.min_price || PRICE_MIN) : (filters.value.max_price || PRICE_MAX));
    }
}

function initPriceSlider() {
    const slider = priceSliderRef.value;
    if (!slider) return;
    if (typeof window.noUiSlider === 'undefined') {
        priceSliderRetry = window.setTimeout(initPriceSlider, 100);
        return;
    }
    if (slider.noUiSlider) slider.noUiSlider.destroy();
    
    window.noUiSlider.create(slider, {
        start: [Number(filters.value.min_price) || PRICE_MIN, Number(filters.value.max_price) || PRICE_MAX],
        connect: true,
        range: { min: PRICE_MIN, max: PRICE_MAX },
        step: PRICE_STEP,
        format: {
            to: (v) => Math.round(v),
            from: (v) => Number(v),
        },
    });
    slider.noUiSlider.on('change', (values) => {
        setPriceRange(values[0], values[1], false);
    });
}

// Data Loading
async function loadOptions() {
    try {
        const { data } = await window.axios.get('/api/properties/filter-options', {
            params: { lang: locale.value || 'en' },
        });
        propertyTypeOptions.value = data.property_types || [];
        categoryOptions.value = data.categories || [];
        locationOptions.value = (data.locations || []).filter(o => o.value !== '');
    } catch (e) {
        console.error('Failed to load filter options', e);
    }
}

function emitChange() {
    normalizeFilters();
    emit('update:modelValue', { ...filters.value });
}

function onSearch() {
    emit('search', { ...filters.value });
    closeSearchOffcanvas();
}

// Lifecycle and Watchers
onMounted(async () => {
    normalizeFilters();
    loadOptions();
    await nextTick();
    initPriceSlider();
});

watch(locale, loadOptions);

onBeforeUnmount(() => {
    if (priceSliderRetry) window.clearTimeout(priceSliderRetry);
    if (priceSliderRef.value?.noUiSlider) priceSliderRef.value.noUiSlider.destroy();
});
</script>

<style scoped>
.properties-listing__search {
    position: relative;
    z-index: 20;
    width: 100%;
}

.search-form--listing {
    background: transparent;
    box-shadow: none;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    gap: 10px;
    width: 100%;
    padding: 0;
    border-radius: 0;
}

@media (min-width: 1199px) {
    .search-form--listing {
        flex-wrap: nowrap;
    }
}

.search-field {
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    margin: 0;
    min-height: 50px;
    padding: 0 16px;
    background: #fff;
    border-radius: 100px;
    border: 1px solid #cfd5de;
    flex: 1 1 140px;
}

@media (max-width: 1198px) {
    .search-field {
        width: 100%;
        flex: 1 1 100%;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.09);
    }
}

@media (min-width: 1199px) {
    .search-field--location { flex: 1.35 1 220px; }
    #propertyTypeField { flex: 1 1 170px; }
    #bedsBathsField { flex: 1 1 170px; }
    #categoriesField { flex: 0.85 1 140px; }
    #priceField { flex: 0.85 1 130px; }
}

.search-field input,
.search-field .selected-text {
    width: 100%;
    font-weight: 400;
    color: #5a6578;
    line-height: 1.25;
    font-size: clamp(14px, 0.95vw, 16px);
    border: none;
    background: transparent;
}

.search-field input:focus {
    outline: none;
}

.search-field svg {
    color: #6b7280;
    flex-shrink: 0;
}

.dropdown-trigger {
    padding: 0;
    color: inherit;
    font: inherit;
}

.custom-dropdown-menu {
    padding: 12px;
    border-radius: 12px;
    box-shadow: 0 14px 40px rgba(15, 23, 42, 0.14) !important;
    border: 1px solid #e8eaed;
    margin-top: 16px !important;
    width: auto;
    min-width: 0;
    max-width: min(100vw - 24px, 520px);
    background: #fff;
}

.dropdown-section {
    margin-bottom: 12px;
}

.dropdown-section-title {
    font-size: 18px;
    font-weight: 400;
    color: #1f2937;
    margin: 8px 0 12px;
}

#propertyTypeField .option-buttons.property-options--collapsed .property-option--extra {
    display: none;
}

.option-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.option-buttons button,
.option-buttons label.custom-check-btn {
    position: relative;
    padding: 8px 16px;
    border-radius: 50px;
    border: 1px solid #e0e0e0;
    background: #fff;
    font-size: 14px;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.2s ease;
    line-height: normal;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0;
}

.option-buttons button:hover,
.option-buttons label.custom-check-btn:hover {
    border-color: #5c6bc0;
    color: #3949ab;
}

.option-buttons button.active,
.option-buttons label.custom-check-btn:has(input.btn-check:checked) {
    background: #e8eaf6;
    border-color: #5c6bc0;
    color: #3949ab;
    font-weight: 600;
}

.option-buttons label.custom-check-btn input.btn-check {
    position: absolute;
    clip: rect(0, 0, 0, 0);
    pointer-events: none;
}

#propertyTypeField .custom-dropdown-menu,
#categoriesField .custom-dropdown-menu,
#priceField .custom-dropdown-menu,
.beds-baths-dropdown {
    width: 100%;
    max-width: 100%;
    min-width: 100%;
}

#propertyTypeField .dropdown-menu,
#bedsBathsField .dropdown-menu,
#categoriesField .dropdown-menu,
#priceField .dropdown-menu {
    top: 100% !important;
    left: 0 !important;
    right: auto !important;
    transform: none !important;
    margin-top: 0 !important;
}

#propertyTypeField .custom-dropdown-menu {
    width: 100%;
    max-width: 100%;
    min-width: 100%;
    margin-top: 0 !important;
    padding: 18px 12px 16px;
}

#propertyTypeField .dropdown-section-title {
    margin: 0 0 12px;
    font-size: 18px;
}

#propertyTypeField .option-buttons {
    gap: 8px;
}

#propertyTypeField .option-buttons button {
    min-width: 0;
    padding: 7px 16px;
}

#categoriesField .custom-dropdown-menu {
    width: 100%;
    max-width: 100%;
    min-width: 100%;
    top: 100% !important;
    margin-top: 0 !important;
    padding: 18px 12px 16px;
}

#categoriesField .dropdown-section-title {
    margin: 0 0 12px;
    font-size: 18px;
}

#categoriesField .option-buttons {
    gap: 8px;
}

#categoriesField .option-buttons button {
    min-width: 0;
    padding: 7px 16px;
}

.dropdown-footer {
    border-top: 1px solid #e0e0e0;
    padding-top: 12px;
    display: flex;
    justify-content: flex-start;
    margin-top: 10px;
}

.dropdown-footer button {
    background: none;
    border: none;
    color: #3f51b5;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    padding: 0;
}

.dropdown-footer button:hover {
    text-decoration: underline;
}

.dropdown-footer .clear-filters {
    background: #f3f4f6;
    color: #3f51b5;
    padding: 8px 16px;
    border-radius: 8px;
}

.dropdown-footer .clear-filters:hover {
    background: #e8eaf6;
    text-decoration: none;
}

#bedsBathsField .beds-baths-dropdown {
    width: 100%;
    max-width: 100%;
    min-width: 100%;
    top: 100% !important;
    margin-top: 0 !important;
    padding: 18px 12px 16px;
}

#bedsBathsField .dropdown-section {
    margin-bottom: 14px;
}

#bedsBathsField .dropdown-section-title {
    margin: 0 0 10px;
    font-size: 18px;
}

#bedsBathsField .option-buttons {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 8px;
    overflow: visible;
    padding-bottom: 0;
}

#bedsBathsField .option-buttons label.custom-check-btn,
#bedsBathsField .option-buttons button {
    min-width: 0;
    width: 100%;
    height: 35px;
    padding: 0 10px;
    flex: none;
}

#bedsBathsField .dropdown-footer {
    margin-top: 12px;
    padding-top: 14px;
}

#bedsBathsField .dropdown-footer .clear-filters {
    background: #f3f4f6;
    color: #2a559c;
    padding: 7px 16px;
    border-radius: 6px;
    font-size: 13px;
}

/* Location Suggestions (Matching Home Design) */
.search-results-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    min-width: 100%;
    max-width: 100%;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 14px 40px rgba(15, 23, 42, 0.15);
    margin-top: 0;
    padding: 16px 8px;
    z-index: 1000;
}

.search-results-title {
    font-size: 13px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 12px;
    padding-left: 12px;
}

.search-results-list {
    max-height: 248px;
    overflow-y: auto;
}

.search-results-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    border: none;
    background: transparent;
    transition: all 0.2s ease;
    width: 100%;
}

.search-results-item:hover {
    background: #f1f5f9;
}

.search-results-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    border-radius: 8px;
    color: #64748b;
    flex-shrink: 0;
}

.search-results-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
    text-align: left;
}

.search-results-name {
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.2;
}

.search-results-city {
    font-size: 13px;
    color: #64748b;
    line-height: 1.2;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Price Slider Styles */
.price-range-dropdown--listing {
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 16px 48px rgba(15, 23, 42, 0.14) !important;
    border: 1px solid #e8eaed;
    background: #fff;
    margin-top: 0 !important;
    min-width: 100%;
}

#priceField .price-range-dropdown--listing {
    width: 100%;
    max-width: 100%;
    min-width: 100%;
    top: 100% !important;
    margin-top: 0 !important;
}

.price-range-slider {
    height: 6px;
    border: none;
    background: #e5e7eb;
    margin: 20px 12px 45px;
    border-radius: 10px;
}

:deep(.noUi-connect) {
    background: #2a559c;
}

:deep(.noUi-handle) {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #fff;
    border: 3px solid #1f2937;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    right: -11px;
    top: -9px;
    cursor: pointer;
}

.price-range-slider-display {
    background: #f8fafc;
    padding: 14px 10px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    gap: 10px;
    border: 1px solid #f1f5f9;
}

.price-range-slider-display__field {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.price-range-slider-display__field input {
    border: none;
    background: rgba(42, 85, 156, 0.08);
    width: 100%;
    text-align: center;
    color: #1f2937;
    padding: 8px 4px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
}

.price-range-slider-display__field .currency {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
    margin-top: 2px;
}

.search-btn--listing {
    color: #fff;
    border: none;
    border-radius: 100px;
    background: #2a559c;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: background 0.25s ease;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    padding: 20px 27px;
}

.search-btn--listing:hover {
    background: #152a4a;
}

@media (max-width: 1198px) {
    .properties-listing__search,
    .search-form--listing {
        overflow: visible;
    }

    .search-btn--listing {
        width: 100%;
        padding: 16px;
    }

    .search-results-dropdown {
        width: 100%;
        max-width: 100%;
        left: 0;
    }

    #propertyTypeField .custom-dropdown-menu,
    #categoriesField .custom-dropdown-menu,
    #priceField .custom-dropdown-menu,
    .beds-baths-dropdown {
        min-width: 0;
        width: 100%;
        max-width: 100%;
    }
}
</style>
