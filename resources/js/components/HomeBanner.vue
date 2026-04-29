<template>
<section class="banner position-relative">
        <div class="banner-slider">
            <div class="banner-slide">
                <picture>
                    <img :src="asset('public/images/banner.jpg')" alt="Banner 1">
                </picture>
            </div>
            <div class="banner-slide">
                <picture>
                    <img :src="asset('public/images/banner-2.jpg')" alt="Banner 2">
                </picture>
            </div>
            <div class="banner-slide">
                <picture>
                    <img :src="asset('public/images/banner.jpg')" alt="Banner 3">
                </picture>
            </div>
        </div>

        <div class="banner-content">
            <div class="container-ctn">
                <div class="banner-content-inner">
                    <h1>
                        <span>Discover Your</span>
                        Perfect Living Spot
                    </h1>
                    <RouterLink :to="{ name: 'properties' }" class="banner-btn">Explore Rentals</RouterLink>

                    <div class="banner-search">
                        <form action="#" class="search-form">
                            <div class="banner-search__pill">
                                <div class="search-field search-field--location">
                                    <svg class="search-field__lead-icon" xmlns="http://www.w3.org/2000/svg" width="18"
                                        height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                        <path
                                            d="M9 1.5C5.7 1.5 3 4.2 3 7.5C3 11.55 8.25 16.125 8.475 16.35C8.625 16.425 8.85 16.5 9 16.5C9.15 16.5 9.375 16.425 9.525 16.35C9.75 16.125 15 11.55 15 7.5C15 4.2 12.3 1.5 9 1.5ZM9 14.775C7.425 13.275 4.5 10.05 4.5 7.5C4.5 5.025 6.525 3 9 3C11.475 3 13.5 5.025 13.5 7.5C13.5 9.975 10.575 13.275 9 14.775ZM9 4.5C7.35 4.5 6 5.85 6 7.5C6 9.15 7.35 10.5 9 10.5C10.65 10.5 12 9.15 12 7.5C12 5.85 10.65 4.5 9 4.5ZM9 9C8.175 9 7.5 8.325 7.5 7.5C7.5 6.675 8.175 6 9 6C9.825 6 10.5 6.675 10.5 7.5C10.5 8.325 9.825 9 9 9Z"
                                            fill="#4B5C77" />
                                    </svg>
                                    <input type="text" name="location" placeholder="Location, community or building"
                                        autocomplete="address-level2">
                                </div>
                                <button type="submit" class="search-btn search-btn--hero" aria-label="Search properties">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 15 15"
                                        fill="none" aria-hidden="true">
                                        <path
                                            d="M14.7982 13.7332L12.0157 10.9732C13.0957 9.62648 13.6188 7.91715 13.4773 6.19665C13.3358 4.47615 12.5404 2.87526 11.2548 1.72316C9.9692 0.571055 8.29103 -0.0446921 6.56537 0.00252822C4.8397 0.0497485 3.19772 0.756347 1.97703 1.97703C0.756347 3.19772 0.0497485 4.8397 0.00252822 6.56537C-0.0446921 8.29103 0.571055 9.9692 1.72316 11.2548C2.87526 12.5404 4.47615 13.3358 6.19665 13.4773C7.91715 13.6188 9.62648 13.0957 10.9732 12.0157L13.7332 14.7757C13.8029 14.846 13.8858 14.9018 13.9772 14.9398C14.0686 14.9779 14.1667 14.9975 14.2657 14.9975C14.3647 14.9975 14.4627 14.9779 14.5541 14.9398C14.6455 14.9018 14.7285 14.846 14.7982 14.7757C14.9334 14.6358 15.0089 14.4489 15.0089 14.2544C15.0089 14.0599 14.9334 13.873 14.7982 13.7332ZM6.76568 12.0157C5.72732 12.0157 4.71229 11.7078 3.84893 11.1309C2.98557 10.554 2.31267 9.73408 1.91531 8.77476C1.51795 7.81545 1.41398 6.75985 1.61655 5.74145C1.81912 4.72305 2.31914 3.78759 3.05336 3.05336C3.78759 2.31914 4.72305 1.81912 5.74145 1.61655C6.75985 1.41398 7.81545 1.51795 8.77476 1.91531C9.73408 2.31267 10.554 2.98557 11.1309 3.84893C11.7078 4.71229 12.0157 5.72732 12.0157 6.76568C12.0157 8.15806 11.4626 9.49342 10.478 10.478C9.49342 11.4626 8.15806 12.0157 6.76568 12.0157Z"
                                            fill="white" />
                                    </svg>
                                    <span class="search-btn__text d-none d-lg-inline">Search</span>
                                </button>
                            </div>
                            <div class="search-field dropdown-field dropdown search-field--filter" id="propertyTypeField">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                    fill="none">
                                    <path
                                        d="M11.25 16.5H2.25V3.75C2.25 1.8885 2.6385 1.5 4.5 1.5H9C10.8615 1.5 11.25 1.8885 11.25 3.75V16.5ZM11.25 16.5V6H13.5C15.3615 6 15.75 6.3885 15.75 8.25V16.5H11.25Z"
                                        stroke="#4B5C77" stroke-width="1.125" stroke-linejoin="round" />
                                    <path d="M6 4.5H7.5M6 6.75H7.5M6 9H7.5" stroke="#4B5C77" stroke-width="1.125"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M8.625 16.5V13.5C8.625 12.7927 8.625 12.4395 8.40525 12.2197C8.1855 12 7.83225 12 7.125 12H6.375C5.66775 12 5.3145 12 5.09475 12.2197C4.875 12.4395 4.875 12.7927 4.875 13.5V16.5"
                                        stroke="#4B5C77" stroke-width="1.125" stroke-linejoin="round" />
                                </svg>
                                <button type="button" class="dropdown-trigger bg-transparent border-0 w-100 text-start"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="selected-text">Select Property Type</span>
                                </button>
                           
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                                    </svg>
    
                                <div class="dropdown-menu property-dropdown border-0  custom-dropdown-menu">
                                    <div class="dropdown-section">
                                        <div class="dropdown-section-title">Property type</div>
                                        <div class="option-buttons" data-type="property">
                                            <button type="button" data-value="Property type">Property type</button>
                                            <button type="button" data-value="Apartment">Apartment</button>
                                            <button type="button" data-value="Villa">Villa</button>
                                            <button type="button" data-value="Townhouse">Townhouse</button>
                                            <button type="button" data-value="Penthouse">Penthouse</button>
                                            <button type="button" data-value="Compound">Compound</button>
                                            <button type="button" data-value="Full Floor">Full Floor</button>
                                            <button type="button" data-value="Half Floor">Half Floor</button>
                                            <button type="button" data-value="Whole Building">Whole Building</button>
                                            <button type="button" data-value="Bulk Rent Unit">Bulk Rent Unit</button>
                                            <button type="button" data-value="Bungalow">Bungalow</button>
                                            <button type="button" data-value="Hotel & Hotel Apartment">Hotel & Hotel
                                                Apartment</button>
                                        </div>
                                    </div>
                                    <div class="dropdown-footer">
                                        <button type="button" class="view-more">View more</button>
                                    </div>
                                </div>
                                <input type="hidden" name="property_type" id="hiddenPropertyType">
                            </div>

                            <div class="search-field dropdown search-field--filter" id="bedsBathsField">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                    fill="none">
                                    <path
                                        d="M1.6875 14.625V10.6875C1.68926 10.0913 1.92688 9.52003 2.34846 9.09846C2.77003 8.67688 3.3413 8.43926 3.9375 8.4375H14.0625C14.6587 8.43926 15.23 8.67688 15.6515 9.09846C16.0731 9.52003 16.3107 10.0913 16.3125 10.6875V14.625M13.5 8.4375H3.375V4.78125C3.37611 4.40863 3.52463 4.05159 3.78811 3.78811C4.05159 3.52463 4.40863 3.37611 4.78125 3.375H13.2188C13.5914 3.37611 13.9484 3.52463 14.2119 3.78811C14.4754 4.05159 14.6239 4.40863 14.625 4.78125V8.4375H13.5Z"
                                        stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M1.6875 14.625V14.3438C1.68815 14.1202 1.77725 13.9059 1.93535 13.7478C2.09344 13.5898 2.30767 13.5006 2.53125 13.5H15.4688C15.6923 13.5006 15.9066 13.5898 16.0647 13.7478C16.2227 13.9059 16.3119 14.1202 16.3125 14.3438V14.625M3.9375 8.4375V7.875C3.93833 7.57689 4.05713 7.29122 4.26793 7.08043C4.47872 6.86963 4.76439 6.75083 5.0625 6.75H7.875C8.17311 6.75083 8.45878 6.86963 8.66957 7.08043C8.88037 7.29122 8.99917 7.57689 9 7.875M9 7.875V8.4375M9 7.875C9.00083 7.57689 9.11963 7.29122 9.33043 7.08043C9.54122 6.86963 9.82689 6.75083 10.125 6.75H12.9375C13.2356 6.75083 13.5213 6.86963 13.7321 7.08043C13.9429 7.29122 14.0617 7.57689 14.0625 7.875V8.4375"
                                        stroke="#4B5C77" stroke-width="1.125" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <button type="button" class="dropdown-trigger bg-transparent border-0 w-100 text-start"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="selected-text">Select Beds & Baths</span>
                                </button>
                             
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.1595 5.95463C15.9485 5.74373 15.6624 5.62525 15.3641 5.62525C15.0658 5.62525 14.7797 5.74373 14.5688 5.95463L9.00001 11.5234L3.43126 5.95463C3.21908 5.7497 2.9349 5.63631 2.63993 5.63887C2.34496 5.64144 2.06279 5.75975 1.85421 5.96834C1.64563 6.17692 1.52731 6.45908 1.52475 6.75406C1.52219 7.04903 1.63558 7.3332 1.84051 7.54538L8.20463 13.9095C8.4156 14.1204 8.7017 14.2389 9.00001 14.2389C9.29832 14.2389 9.58441 14.1204 9.79538 13.9095L16.1595 7.54538C16.3704 7.33441 16.4889 7.04832 16.4889 6.75001C16.4889 6.4517 16.3704 6.1656 16.1595 5.95463Z" fill="#4B5C77"/>
                                    </svg>
    
                                <div class="dropdown-menu beds-baths-dropdown border-0  custom-dropdown-menu">
                                    <div class="dropdown-section">
                                        <div class="dropdown-section-title">Bedrooms</div>
                                        <div class="option-buttons" data-type="bedrooms">
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bed-check" value="Studio"
                                                    id="bed-studio">Studio</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bed-check" value="1" id="bed-1">1</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bed-check" value="2" id="bed-2">2</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bed-check" value="3" id="bed-3">3</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bed-check" value="4" id="bed-4">4</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bed-check" value="5" id="bed-5">5</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bed-check" value="6" id="bed-6">6</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bed-check" value="7" id="bed-7">7</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bed-check" value="7+" id="bed-7plus">7+</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-section">
                                        <div class="dropdown-section-title">Bathrooms</div>
                                        <div class="option-buttons" data-type="bathrooms">
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bath-check" value="1" id="bath-1">1</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bath-check" value="2" id="bath-2">2</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bath-check" value="3" id="bath-3">3</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bath-check" value="4" id="bath-4">4</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bath-check" value="5" id="bath-5">5</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bath-check" value="6" id="bath-6">6</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bath-check" value="7" id="bath-7">7</label>
                                            <label class="custom-check-btn"><input type="checkbox"
                                                    class="btn-check bath-check" value="7+" id="bath-7plus">7+</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-footer">
                                        <button type="button" class="clear-filters">Clear Filter</button>
                                    </div>
                                </div>
                                <input type="hidden" name="bedrooms" id="hiddenBedrooms">
                                <input type="hidden" name="bathrooms" id="hiddenBathrooms">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { RouterLink } from 'vue-router';
import { asset } from '@/utils/asset';
</script>
