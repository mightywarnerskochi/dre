<?php
$page_title = 'DRE';
$page_key = 'home';
$meta_description = 'List your property, earn more, and let us manage everything. Premium vacation home management in Dubai.';
$style_version = '14';
require __DIR__ . '/includes/header.php';
?>
<section class="banner position-relative">
        <div class="banner-slider">
            <div class="banner-slide">
                <picture>
                    <img src="public/images/banner.jpg" alt="Banner 1">
                </picture>
            </div>
            <div class="banner-slide">
                <picture>
                    <img src="public/images/banner-2.jpg" alt="Banner 2">
                </picture>
            </div>
            <div class="banner-slide">
                <picture>
                    <img src="public/images/banner.jpg" alt="Banner 3">
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
                    <a href="" class="banner-btn">Explore Rentals</a>

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

    <section class="rental-properties commonPadding" id="rental-properties">
        <div class="container-ctn">
            <div class="row">
                <div class="col-12">
                    <div class="head text-center">
                        <h2>Rental Properties</h2>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has
                            been the industry's standard dummy text ever since the 1500s, when an unknown.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid p-0">
            <div class="property-slider-wrap">
                <div class="property-slider">
                    <article class="property-card">
                        <div class="property-card__ghost" aria-hidden="true"></div>
                        <div class="property-card__inner">
                            <div class="property-card__media">
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <div class="property-card__dots" role="tablist" aria-label="Photo gallery">
                                    <span class="is-active"></span><span></span><span></span><span></span><span></span>
                                </div>
                                <span class="property-card__360" title="360° virtual tour">360°</span>
                            </div>
                            <div class="property-card__body">
                                <h3 class="property-card__title">Majestic Tower</h3>
                                <p class="property-card__location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z"
                                            fill="#2F3F58" />
                                    </svg>
                                    <span>Al Abraj Street Business Bay, Dubai</span>
                                </p>
                                <div class="property-card__meta">
                                    <div class="property-card__price-row">
                                        <div class="property-card__price">92,000 د.إ</div>
                                        <div class="property-card__photos">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path
                                                    d="M13.997 4C14.3578 3.99999 14.7119 4.09759 15.0217 4.28244C15.3316 4.46729 15.5856 4.73251 15.757 5.05L16.243 5.95C16.4144 6.26749 16.6684 6.53271 16.9783 6.71756C17.2881 6.90241 17.6422 7.00001 18.003 7H20C20.5304 7 21.0391 7.21071 21.4142 7.58579C21.7893 7.96086 22 8.46957 22 9V18C22 18.5304 21.7893 19.0391 21.4142 19.4142C21.0391 19.7893 20.5304 20 20 20H4C3.46957 20 2.96086 19.7893 2.58579 19.4142C2.21071 19.0391 2 18.5304 2 18V9C2 8.46957 2.21071 7.96086 2.58579 7.58579C2.96086 7.21071 3.46957 7 4 7H5.997C6.35742 7.00002 6.71115 6.90264 7.02078 6.71817C7.33041 6.53369 7.58444 6.26897 7.756 5.952L8.245 5.048C8.41656 4.73103 8.67059 4.46631 8.98022 4.28183C9.28985 4.09736 9.64358 3.99998 10.004 4H13.997Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            13 Photos
                                        </div>
                                    </div>
                                    <div class="property-card__tags">
                                        <span class="property-tag property-tag--fill">Apartment</span>
                                        <span class="property-tag property-tag--outline">Rentals</span>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M2.25 19.5V14.25C2.25235 13.4551 2.56917 12.6934 3.13128 12.1313C3.69338 11.5692 4.45507 11.2523 5.25 11.25H18.75C19.5449 11.2523 20.3066 11.5692 20.8687 12.1313C21.4308 12.6934 21.7477 13.4551 21.75 14.25V19.5M18 11.25H4.5V6.375C4.50148 5.87818 4.6995 5.40212 5.05081 5.05081C5.40212 4.6995 5.87818 4.50148 6.375 4.5H17.625C18.1218 4.50148 18.5979 4.6995 18.9492 5.05081C19.3005 5.40212 19.4985 5.87818 19.5 6.375V11.25H18Z"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M2.25 19.5V19.125C2.25087 18.8269 2.36967 18.5413 2.58046 18.3305C2.79125 18.1197 3.0769 18.0009 3.375 18H20.625C20.9231 18.0009 21.2087 18.1197 21.4195 18.3305C21.6303 18.5413 21.7491 18.8269 21.75 19.125V19.5M5.25 11.25V10.5C5.25111 10.1025 5.40951 9.72163 5.69057 9.44057C5.97163 9.15951 6.35252 9.00111 6.75 9H10.5C10.8975 9.00111 11.2784 9.15951 11.5594 9.44057C11.8405 9.72163 11.9989 10.1025 12 10.5M12 10.5V11.25M12 10.5C12.0011 10.1025 12.1595 9.72163 12.4406 9.44057C12.7216 9.15951 13.1025 9.00111 13.5 9H17.25C17.6475 9.00111 18.0284 9.15951 18.3094 9.44057C18.5905 9.72163 18.7489 10.1025 18.75 10.5V11.25"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        <span>1 Bedroom</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M21.75 13.125H3.75V4.6875C3.74914 4.36975 3.8113 4.05498 3.9329 3.76142C4.0545 3.46786 4.23312 3.20133 4.45842 2.97726L4.47717 2.95852C4.83026 2.60594 5.28359 2.37097 5.77524 2.28571C6.26688 2.20045 6.77286 2.26906 7.22405 2.48217C6.79794 3.19064 6.62083 4.02119 6.72086 4.84185C6.82089 5.66251 7.19233 6.4262 7.77614 7.01156L8.28947 7.52489L7.34461 8.4698L8.4052 9.53039L9.35006 8.58553L14.5855 3.3502L15.5303 2.40534L14.4697 1.3447L13.5248 2.28956L13.0115 1.77623C12.3969 1.16336 11.5867 0.785558 10.7222 0.708671C9.85764 0.631784 8.99353 0.860685 8.28042 1.35548C7.52846 0.8806 6.63734 0.675589 5.75346 0.774129C4.86958 0.87267 4.04549 1.2689 3.41658 1.89773L3.39783 1.91648C3.03278 2.27952 2.74336 2.71136 2.54634 3.18701C2.34931 3.66266 2.24859 4.17266 2.25 4.6875V13.125H0.75V14.625H2.25V16.0641C2.25001 16.185 2.26951 16.3051 2.30775 16.4198L3.70312 20.6057C3.7776 20.8298 3.92078 21.0248 4.11235 21.1629C4.30392 21.301 4.53413 21.3752 4.77028 21.375H5.37497L4.82812 23.25H6.39061L6.9375 21.375H16.6922L17.2547 23.25H18.8203L18.2578 21.375H19.2295C19.4657 21.3752 19.696 21.301 19.8876 21.1629C20.0792 21.0248 20.2224 20.8299 20.2969 20.6057L21.6922 16.4198C21.7304 16.3051 21.75 16.185 21.75 16.0641V14.625H23.25V13.125H21.75ZM8.83688 2.83687C9.25012 2.42453 9.81007 2.19295 10.3939 2.19295C10.9776 2.19295 11.5376 2.42453 11.9508 2.83687L12.4641 3.3502L9.3502 6.46406L8.83688 5.95083C8.42455 5.53757 8.19298 4.97763 8.19298 4.39385C8.19298 3.81007 8.42455 3.25013 8.83688 2.83687ZM20.25 16.0031L18.9594 19.875H5.04056L3.75 16.0031V14.625H20.25V16.0031Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>1 Bathroom</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                            viewBox="0 0 25 25" fill="none" aria-hidden="true">
                                            <path
                                                d="M3.012 0C1.35888 0 0 1.35864 0 3.012C0 4.45608 1.03608 5.67504 2.4 5.96064V18.0634C1.03608 18.349 0 19.5682 0 21.0122C0 22.6656 1.35864 24.024 3.012 24.024C4.46328 24.024 5.6736 22.9742 5.94984 21.6H18.059C18.3355 22.9757 19.5598 24.024 21.0122 24.024C22.6082 24.024 23.9174 22.7549 24.007 21.1795C24.0181 21.1245 24.0238 21.0684 24.0238 21.0122C24.0238 20.9561 24.0181 20.9 24.007 20.845C23.9287 19.4681 22.9188 18.3254 21.6 18.059V5.96496C22.9188 5.69856 23.9287 4.55616 24.0072 3.17952C24.0184 3.12446 24.024 3.06842 24.024 3.01224C24.024 2.95606 24.0184 2.90002 24.0072 2.84496C23.9172 1.2696 22.6082 0 21.012 0C19.5682 0 18.349 1.03608 18.0634 2.4H5.9448C5.6592 1.03776 4.4544 0 3.012 0ZM3.012 1.68C3.75768 1.68 4.344 2.26656 4.344 3.012C4.344 3.75768 3.75768 4.344 3.012 4.344C2.26632 4.344 1.68 3.75768 1.68 3.012C1.68 2.26632 2.26656 1.68 3.012 1.68ZM21.012 1.68C21.7577 1.68 22.344 2.26656 22.344 3.012C22.344 3.75768 21.7577 4.344 21.012 4.344C20.2663 4.344 19.68 3.75768 19.68 3.012C19.68 2.26632 20.2666 1.68 21.012 1.68ZM5.81232 4.08H18.2011C18.3524 4.47204 18.5832 4.82851 18.879 5.12694C19.1749 5.42537 19.5293 5.65931 19.92 5.814V18.21C19.5332 18.363 19.182 18.5937 18.8878 18.8878C18.5937 19.182 18.363 19.5332 18.21 19.92H5.802C5.64914 19.5302 5.41724 19.1762 5.12088 18.8804C4.82451 18.5845 4.47012 18.3533 4.08 18.2011V5.82288C4.4743 5.66925 4.8321 5.43482 5.1304 5.13466C5.4287 4.8345 5.6609 4.47525 5.81208 4.08M3.012 19.68C3.75744 19.68 4.34376 20.2666 4.34376 21.012C4.34376 21.7577 3.75744 22.344 3.01176 22.344C2.26608 22.344 1.68 21.7577 1.68 21.012C1.68 20.2663 2.26656 19.68 3.012 19.68ZM21.012 19.68C21.7574 19.68 22.3438 20.2666 22.3438 21.012C22.3438 21.7577 21.7574 22.344 21.0118 22.344C20.2661 22.344 19.68 21.7577 19.68 21.012C19.68 20.2663 20.2666 19.68 21.012 19.68Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>1,015 ft²</span>
                                    </div>
                                </div>
                                <div class="property-card__actions">
                                    <a class="property-btn property-btn--primary" href="#" data-bs-toggle="modal"
                                        data-bs-target="#siteEnquiryForm">Enquiry</a>
                                    <a class="property-btn property-btn--light" href="tel:+971501234567">Call Now</a>
                                    <a class="property-btn property-btn--outline" href="https://wa.me/971501234567"
                                        target="_blank" rel="noopener noreferrer">Whatsapp</a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <article class="property-card">
                        <div class="property-card__ghost" aria-hidden="true"></div>
                        <div class="property-card__inner">
                            <div class="property-card__media">
                                <picture>
                                    <img src="public/images/properties/2.jpg" alt="Marina View" width="427" height="260"
                                        loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/2.jpg" alt="Marina View" width="427" height="260"
                                        loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/2.jpg" alt="Marina View" width="427" height="260"
                                        loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/2.jpg" alt="Marina View" width="427" height="260"
                                        loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/2.jpg" alt="Marina View" width="427" height="260"
                                        loading="lazy">
                                </picture>
                                <div class="property-card__dots" role="tablist" aria-label="Photo gallery">
                                    <span class="is-active"></span><span></span><span></span><span></span><span></span>
                                </div>
                                <span class="property-card__360" title="360° virtual tour">360°</span>
                            </div>
                            <div class="property-card__body">
                                <h3 class="property-card__title">Marina View Residences</h3>
                                <p class="property-card__location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z"
                                            fill="#2F3F58" />
                                    </svg>
                                    <span>Dubai Marina, Dubai</span>
                                </p>
                                <div class="property-card__meta">
                                    <div class="property-card__price-row">
                                        <div class="property-card__price">110,000 د.إ</div>
                                        <div class="property-card__photos">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path
                                                    d="M13.997 4C14.3578 3.99999 14.7119 4.09759 15.0217 4.28244C15.3316 4.46729 15.5856 4.73251 15.757 5.05L16.243 5.95C16.4144 6.26749 16.6684 6.53271 16.9783 6.71756C17.2881 6.90241 17.6422 7.00001 18.003 7H20C20.5304 7 21.0391 7.21071 21.4142 7.58579C21.7893 7.96086 22 8.46957 22 9V18C22 18.5304 21.7893 19.0391 21.4142 19.4142C21.0391 19.7893 20.5304 20 20 20H4C3.46957 20 2.96086 19.7893 2.58579 19.4142C2.21071 19.0391 2 18.5304 2 18V9C2 8.46957 2.21071 7.96086 2.58579 7.58579C2.96086 7.21071 3.46957 7 4 7H5.997C6.35742 7.00002 6.71115 6.90264 7.02078 6.71817C7.33041 6.53369 7.58444 6.26897 7.756 5.952L8.245 5.048C8.41656 4.73103 8.67059 4.46631 8.98022 4.28183C9.28985 4.09736 9.64358 3.99998 10.004 4H13.997Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            18 Photos
                                        </div>
                                    </div>
                                    <div class="property-card__tags">
                                        <span class="property-tag property-tag--fill">Apartment</span>
                                        <span class="property-tag property-tag--outline">Rentals</span>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M2.25 19.5V14.25C2.25235 13.4551 2.56917 12.6934 3.13128 12.1313C3.69338 11.5692 4.45507 11.2523 5.25 11.25H18.75C19.5449 11.2523 20.3066 11.5692 20.8687 12.1313C21.4308 12.6934 21.7477 13.4551 21.75 14.25V19.5M18 11.25H4.5V6.375C4.50148 5.87818 4.6995 5.40212 5.05081 5.05081C5.40212 4.6995 5.87818 4.50148 6.375 4.5H17.625C18.1218 4.50148 18.5979 4.6995 18.9492 5.05081C19.3005 5.40212 19.4985 5.87818 19.5 6.375V11.25H18Z"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M2.25 19.5V19.125C2.25087 18.8269 2.36967 18.5413 2.58046 18.3305C2.79125 18.1197 3.0769 18.0009 3.375 18H20.625C20.9231 18.0009 21.2087 18.1197 21.4195 18.3305C21.6303 18.5413 21.7491 18.8269 21.75 19.125V19.5M5.25 11.25V10.5C5.25111 10.1025 5.40951 9.72163 5.69057 9.44057C5.97163 9.15951 6.35252 9.00111 6.75 9H10.5C10.8975 9.00111 11.2784 9.15951 11.5594 9.44057C11.8405 9.72163 11.9989 10.1025 12 10.5M12 10.5V11.25M12 10.5C12.0011 10.1025 12.1595 9.72163 12.4406 9.44057C12.7216 9.15951 13.1025 9.00111 13.5 9H17.25C17.6475 9.00111 18.0284 9.15951 18.3094 9.44057C18.5905 9.72163 18.7489 10.1025 18.75 10.5V11.25"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        <span>2 Bedrooms</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M21.75 13.125H3.75V4.6875C3.74914 4.36975 3.8113 4.05498 3.9329 3.76142C4.0545 3.46786 4.23312 3.20133 4.45842 2.97726L4.47717 2.95852C4.83026 2.60594 5.28359 2.37097 5.77524 2.28571C6.26688 2.20045 6.77286 2.26906 7.22405 2.48217C6.79794 3.19064 6.62083 4.02119 6.72086 4.84185C6.82089 5.66251 7.19233 6.4262 7.77614 7.01156L8.28947 7.52489L7.34461 8.4698L8.4052 9.53039L9.35006 8.58553L14.5855 3.3502L15.5303 2.40534L14.4697 1.3447L13.5248 2.28956L13.0115 1.77623C12.3969 1.16336 11.5867 0.785558 10.7222 0.708671C9.85764 0.631784 8.99353 0.860685 8.28042 1.35548C7.52846 0.8806 6.63734 0.675589 5.75346 0.774129C4.86958 0.87267 4.04549 1.2689 3.41658 1.89773L3.39783 1.91648C3.03278 2.27952 2.74336 2.71136 2.54634 3.18701C2.34931 3.66266 2.24859 4.17266 2.25 4.6875V13.125H0.75V14.625H2.25V16.0641C2.25001 16.185 2.26951 16.3051 2.30775 16.4198L3.70312 20.6057C3.7776 20.8298 3.92078 21.0248 4.11235 21.1629C4.30392 21.301 4.53413 21.3752 4.77028 21.375H5.37497L4.82812 23.25H6.39061L6.9375 21.375H16.6922L17.2547 23.25H18.8203L18.2578 21.375H19.2295C19.4657 21.3752 19.696 21.301 19.8876 21.1629C20.0792 21.0248 20.2224 20.8299 20.2969 20.6057L21.6922 16.4198C21.7304 16.3051 21.75 16.185 21.75 16.0641V14.625H23.25V13.125H21.75ZM8.83688 2.83687C9.25012 2.42453 9.81007 2.19295 10.3939 2.19295C10.9776 2.19295 11.5376 2.42453 11.9508 2.83687L12.4641 3.3502L9.3502 6.46406L8.83688 5.95083C8.42455 5.53757 8.19298 4.97763 8.19298 4.39385C8.19298 3.81007 8.42455 3.25013 8.83688 2.83687ZM20.25 16.0031L18.9594 19.875H5.04056L3.75 16.0031V14.625H20.25V16.0031Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>2 Bathrooms</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                            viewBox="0 0 25 25" fill="none" aria-hidden="true">
                                            <path
                                                d="M3.012 0C1.35888 0 0 1.35864 0 3.012C0 4.45608 1.03608 5.67504 2.4 5.96064V18.0634C1.03608 18.349 0 19.5682 0 21.0122C0 22.6656 1.35864 24.024 3.012 24.024C4.46328 24.024 5.6736 22.9742 5.94984 21.6H18.059C18.3355 22.9757 19.5598 24.024 21.0122 24.024C22.6082 24.024 23.9174 22.7549 24.007 21.1795C24.0181 21.1245 24.0238 21.0684 24.0238 21.0122C24.0238 20.9561 24.0181 20.9 24.007 20.845C23.9287 19.4681 22.9188 18.3254 21.6 18.059V5.96496C22.9188 5.69856 23.9287 4.55616 24.0072 3.17952C24.0184 3.12446 24.024 3.06842 24.024 3.01224C24.024 2.95606 24.0184 2.90002 24.0072 2.84496C23.9172 1.2696 22.6082 0 21.012 0C19.5682 0 18.349 1.03608 18.0634 2.4H5.9448C5.6592 1.03776 4.4544 0 3.012 0ZM3.012 1.68C3.75768 1.68 4.344 2.26656 4.344 3.012C4.344 3.75768 3.75768 4.344 3.012 4.344C2.26632 4.344 1.68 3.75768 1.68 3.012C1.68 2.26632 2.26656 1.68 3.012 1.68ZM21.012 1.68C21.7577 1.68 22.344 2.26656 22.344 3.012C22.344 3.75768 21.7577 4.344 21.012 4.344C20.2663 4.344 19.68 3.75768 19.68 3.012C19.68 2.26632 20.2666 1.68 21.012 1.68ZM5.81232 4.08H18.2011C18.3524 4.47204 18.5832 4.82851 18.879 5.12694C19.1749 5.42537 19.5293 5.65931 19.92 5.814V18.21C19.5332 18.363 19.182 18.5937 18.8878 18.8878C18.5937 19.182 18.363 19.5332 18.21 19.92H5.802C5.64914 19.5302 5.41724 19.1762 5.12088 18.8804C4.82451 18.5845 4.47012 18.3533 4.08 18.2011V5.82288C4.4743 5.66925 4.8321 5.43482 5.1304 5.13466C5.4287 4.8345 5.6609 4.47525 5.81208 4.08M3.012 19.68C3.75744 19.68 4.34376 20.2666 4.34376 21.012C4.34376 21.7577 3.75744 22.344 3.01176 22.344C2.26608 22.344 1.68 21.7577 1.68 21.012C1.68 20.2663 2.26656 19.68 3.012 19.68ZM21.012 19.68C21.7574 19.68 22.3438 20.2666 22.3438 21.012C22.3438 21.7577 21.7574 22.344 21.0118 22.344C20.2661 22.344 19.68 21.7577 19.68 21.012C19.68 20.2663 20.2666 19.68 21.012 19.68Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>1,450 ft²</span>
                                    </div>
                                </div>
                                <div class="property-card__actions">
                                    <a class="property-btn property-btn--primary" href="#" data-bs-toggle="modal"
                                        data-bs-target="#siteEnquiryForm">Enquiry</a>
                                    <a class="property-btn property-btn--light" href="tel:+971501234567">Call Now</a>
                                    <a class="property-btn property-btn--outline" href="https://wa.me/971501234567"
                                        target="_blank" rel="noopener noreferrer">Whatsapp</a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <article class="property-card">
                        <div class="property-card__ghost" aria-hidden="true"></div>
                        <div class="property-card__inner">
                            <div class="property-card__media">
                                <picture>
                                    <img src="public/images/properties/3.jpg" alt="Palm Heights" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/3.jpg" alt="Palm Heights" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/3.jpg" alt="Palm Heights" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/3.jpg" alt="Palm Heights" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/3.jpg" alt="Palm Heights" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <div class="property-card__dots" role="tablist" aria-label="Photo gallery">
                                    <span class="is-active"></span><span></span><span></span><span></span><span></span>
                                </div>
                                <span class="property-card__360" title="360° virtual tour">360°</span>
                            </div>
                            <div class="property-card__body">
                                <h3 class="property-card__title">Palm Heights</h3>
                                <p class="property-card__location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z"
                                            fill="#2F3F58" />
                                    </svg>
                                    <span>Palm Jumeirah, Dubai</span>
                                </p>
                                <div class="property-card__meta">
                                    <div class="property-card__price-row">
                                        <div class="property-card__price">145,000 د.إ</div>
                                        <div class="property-card__photos">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path
                                                    d="M13.997 4C14.3578 3.99999 14.7119 4.09759 15.0217 4.28244C15.3316 4.46729 15.5856 4.73251 15.757 5.05L16.243 5.95C16.4144 6.26749 16.6684 6.53271 16.9783 6.71756C17.2881 6.90241 17.6422 7.00001 18.003 7H20C20.5304 7 21.0391 7.21071 21.4142 7.58579C21.7893 7.96086 22 8.46957 22 9V18C22 18.5304 21.7893 19.0391 21.4142 19.4142C21.0391 19.7893 20.5304 20 20 20H4C3.46957 20 2.96086 19.7893 2.58579 19.4142C2.21071 19.0391 2 18.5304 2 18V9C2 8.46957 2.21071 7.96086 2.58579 7.58579C2.96086 7.21071 3.46957 7 4 7H5.997C6.35742 7.00002 6.71115 6.90264 7.02078 6.71817C7.33041 6.53369 7.58444 6.26897 7.756 5.952L8.245 5.048C8.41656 4.73103 8.67059 4.46631 8.98022 4.28183C9.28985 4.09736 9.64358 3.99998 10.004 4H13.997Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            24 Photos
                                        </div>
                                    </div>
                                    <div class="property-card__tags">
                                        <span class="property-tag property-tag--fill">Apartment</span>
                                        <span class="property-tag property-tag--outline">Rentals</span>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M2.25 19.5V14.25C2.25235 13.4551 2.56917 12.6934 3.13128 12.1313C3.69338 11.5692 4.45507 11.2523 5.25 11.25H18.75C19.5449 11.2523 20.3066 11.5692 20.8687 12.1313C21.4308 12.6934 21.7477 13.4551 21.75 14.25V19.5M18 11.25H4.5V6.375C4.50148 5.87818 4.6995 5.40212 5.05081 5.05081C5.40212 4.6995 5.87818 4.50148 6.375 4.5H17.625C18.1218 4.50148 18.5979 4.6995 18.9492 5.05081C19.3005 5.40212 19.4985 5.87818 19.5 6.375V11.25H18Z"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M2.25 19.5V19.125C2.25087 18.8269 2.36967 18.5413 2.58046 18.3305C2.79125 18.1197 3.0769 18.0009 3.375 18H20.625C20.9231 18.0009 21.2087 18.1197 21.4195 18.3305C21.6303 18.5413 21.7491 18.8269 21.75 19.125V19.5M5.25 11.25V10.5C5.25111 10.1025 5.40951 9.72163 5.69057 9.44057C5.97163 9.15951 6.35252 9.00111 6.75 9H10.5C10.8975 9.00111 11.2784 9.15951 11.5594 9.44057C11.8405 9.72163 11.9989 10.1025 12 10.5M12 10.5V11.25M12 10.5C12.0011 10.1025 12.1595 9.72163 12.4406 9.44057C12.7216 9.15951 13.1025 9.00111 13.5 9H17.25C17.6475 9.00111 18.0284 9.15951 18.3094 9.44057C18.5905 9.72163 18.7489 10.1025 18.75 10.5V11.25"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        <span>3 Bedrooms</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M21.75 13.125H3.75V4.6875C3.74914 4.36975 3.8113 4.05498 3.9329 3.76142C4.0545 3.46786 4.23312 3.20133 4.45842 2.97726L4.47717 2.95852C4.83026 2.60594 5.28359 2.37097 5.77524 2.28571C6.26688 2.20045 6.77286 2.26906 7.22405 2.48217C6.79794 3.19064 6.62083 4.02119 6.72086 4.84185C6.82089 5.66251 7.19233 6.4262 7.77614 7.01156L8.28947 7.52489L7.34461 8.4698L8.4052 9.53039L9.35006 8.58553L14.5855 3.3502L15.5303 2.40534L14.4697 1.3447L13.5248 2.28956L13.0115 1.77623C12.3969 1.16336 11.5867 0.785558 10.7222 0.708671C9.85764 0.631784 8.99353 0.860685 8.28042 1.35548C7.52846 0.8806 6.63734 0.675589 5.75346 0.774129C4.86958 0.87267 4.04549 1.2689 3.41658 1.89773L3.39783 1.91648C3.03278 2.27952 2.74336 2.71136 2.54634 3.18701C2.34931 3.66266 2.24859 4.17266 2.25 4.6875V13.125H0.75V14.625H2.25V16.0641C2.25001 16.185 2.26951 16.3051 2.30775 16.4198L3.70312 20.6057C3.7776 20.8298 3.92078 21.0248 4.11235 21.1629C4.30392 21.301 4.53413 21.3752 4.77028 21.375H5.37497L4.82812 23.25H6.39061L6.9375 21.375H16.6922L17.2547 23.25H18.8203L18.2578 21.375H19.2295C19.4657 21.3752 19.696 21.301 19.8876 21.1629C20.0792 21.0248 20.2224 20.8299 20.2969 20.6057L21.6922 16.4198C21.7304 16.3051 21.75 16.185 21.75 16.0641V14.625H23.25V13.125H21.75ZM8.83688 2.83687C9.25012 2.42453 9.81007 2.19295 10.3939 2.19295C10.9776 2.19295 11.5376 2.42453 11.9508 2.83687L12.4641 3.3502L9.3502 6.46406L8.83688 5.95083C8.42455 5.53757 8.19298 4.97763 8.19298 4.39385C8.19298 3.81007 8.42455 3.25013 8.83688 2.83687ZM20.25 16.0031L18.9594 19.875H5.04056L3.75 16.0031V14.625H20.25V16.0031Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>3 Bathrooms</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                            viewBox="0 0 25 25" fill="none" aria-hidden="true">
                                            <path
                                                d="M3.012 0C1.35888 0 0 1.35864 0 3.012C0 4.45608 1.03608 5.67504 2.4 5.96064V18.0634C1.03608 18.349 0 19.5682 0 21.0122C0 22.6656 1.35864 24.024 3.012 24.024C4.46328 24.024 5.6736 22.9742 5.94984 21.6H18.059C18.3355 22.9757 19.5598 24.024 21.0122 24.024C22.6082 24.024 23.9174 22.7549 24.007 21.1795C24.0181 21.1245 24.0238 21.0684 24.0238 21.0122C24.0238 20.9561 24.0181 20.9 24.007 20.845C23.9287 19.4681 22.9188 18.3254 21.6 18.059V5.96496C22.9188 5.69856 23.9287 4.55616 24.0072 3.17952C24.0184 3.12446 24.024 3.06842 24.024 3.01224C24.024 2.95606 24.0184 2.90002 24.0072 2.84496C23.9172 1.2696 22.6082 0 21.012 0C19.5682 0 18.349 1.03608 18.0634 2.4H5.9448C5.6592 1.03776 4.4544 0 3.012 0ZM3.012 1.68C3.75768 1.68 4.344 2.26656 4.344 3.012C4.344 3.75768 3.75768 4.344 3.012 4.344C2.26632 4.344 1.68 3.75768 1.68 3.012C1.68 2.26632 2.26656 1.68 3.012 1.68ZM21.012 1.68C21.7577 1.68 22.344 2.26656 22.344 3.012C22.344 3.75768 21.7577 4.344 21.012 4.344C20.2663 4.344 19.68 3.75768 19.68 3.012C19.68 2.26632 20.2666 1.68 21.012 1.68ZM5.81232 4.08H18.2011C18.3524 4.47204 18.5832 4.82851 18.879 5.12694C19.1749 5.42537 19.5293 5.65931 19.92 5.814V18.21C19.5332 18.363 19.182 18.5937 18.8878 18.8878C18.5937 19.182 18.363 19.5332 18.21 19.92H5.802C5.64914 19.5302 5.41724 19.1762 5.12088 18.8804C4.82451 18.5845 4.47012 18.3533 4.08 18.2011V5.82288C4.4743 5.66925 4.8321 5.43482 5.1304 5.13466C5.4287 4.8345 5.6609 4.47525 5.81208 4.08M3.012 19.68C3.75744 19.68 4.34376 20.2666 4.34376 21.012C4.34376 21.7577 3.75744 22.344 3.01176 22.344C2.26608 22.344 1.68 21.7577 1.68 21.012C1.68 20.2663 2.26656 19.68 3.012 19.68ZM21.012 19.68C21.7574 19.68 22.3438 20.2666 22.3438 21.012C22.3438 21.7577 21.7574 22.344 21.0118 22.344C20.2661 22.344 19.68 21.7577 19.68 21.012C19.68 20.2663 20.2666 19.68 21.012 19.68Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>2,200 ft²</span>
                                    </div>
                                </div>
                                <div class="property-card__actions">
                                    <a class="property-btn property-btn--primary" href="#" data-bs-toggle="modal"
                                        data-bs-target="#siteEnquiryForm">Enquiry</a>
                                    <a class="property-btn property-btn--light" href="tel:+971501234567">Call Now</a>
                                    <a class="property-btn property-btn--outline" href="https://wa.me/971501234567"
                                        target="_blank" rel="noopener noreferrer">Whatsapp</a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <article class="property-card">
                        <div class="property-card__ghost" aria-hidden="true"></div>
                        <div class="property-card__inner">
                            <div class="property-card__media">
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/1.jpg" alt="Majestic Tower" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <div class="property-card__dots" role="tablist" aria-label="Photo gallery">
                                    <span class="is-active"></span><span></span><span></span><span></span><span></span>
                                </div>
                                <span class="property-card__360" title="360° virtual tour">360°</span>
                            </div>
                            <div class="property-card__body">
                                <h3 class="property-card__title">Majestic Tower</h3>
                                <p class="property-card__location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z"
                                            fill="#2F3F58" />
                                    </svg>
                                    <span>Al Abraj Street Business Bay, Dubai</span>
                                </p>
                                <div class="property-card__meta">
                                    <div class="property-card__price-row">
                                        <div class="property-card__price">92,000 د.إ</div>
                                        <div class="property-card__photos">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path
                                                    d="M13.997 4C14.3578 3.99999 14.7119 4.09759 15.0217 4.28244C15.3316 4.46729 15.5856 4.73251 15.757 5.05L16.243 5.95C16.4144 6.26749 16.6684 6.53271 16.9783 6.71756C17.2881 6.90241 17.6422 7.00001 18.003 7H20C20.5304 7 21.0391 7.21071 21.4142 7.58579C21.7893 7.96086 22 8.46957 22 9V18C22 18.5304 21.7893 19.0391 21.4142 19.4142C21.0391 19.7893 20.5304 20 20 20H4C3.46957 20 2.96086 19.7893 2.58579 19.4142C2.21071 19.0391 2 18.5304 2 18V9C2 8.46957 2.21071 7.96086 2.58579 7.58579C2.96086 7.21071 3.46957 7 4 7H5.997C6.35742 7.00002 6.71115 6.90264 7.02078 6.71817C7.33041 6.53369 7.58444 6.26897 7.756 5.952L8.245 5.048C8.41656 4.73103 8.67059 4.46631 8.98022 4.28183C9.28985 4.09736 9.64358 3.99998 10.004 4H13.997Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            13 Photos
                                        </div>
                                    </div>
                                    <div class="property-card__tags">
                                        <span class="property-tag property-tag--fill">Apartment</span>
                                        <span class="property-tag property-tag--outline">Rentals</span>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M2.25 19.5V14.25C2.25235 13.4551 2.56917 12.6934 3.13128 12.1313C3.69338 11.5692 4.45507 11.2523 5.25 11.25H18.75C19.5449 11.2523 20.3066 11.5692 20.8687 12.1313C21.4308 12.6934 21.7477 13.4551 21.75 14.25V19.5M18 11.25H4.5V6.375C4.50148 5.87818 4.6995 5.40212 5.05081 5.05081C5.40212 4.6995 5.87818 4.50148 6.375 4.5H17.625C18.1218 4.50148 18.5979 4.6995 18.9492 5.05081C19.3005 5.40212 19.4985 5.87818 19.5 6.375V11.25H18Z"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M2.25 19.5V19.125C2.25087 18.8269 2.36967 18.5413 2.58046 18.3305C2.79125 18.1197 3.0769 18.0009 3.375 18H20.625C20.9231 18.0009 21.2087 18.1197 21.4195 18.3305C21.6303 18.5413 21.7491 18.8269 21.75 19.125V19.5M5.25 11.25V10.5C5.25111 10.1025 5.40951 9.72163 5.69057 9.44057C5.97163 9.15951 6.35252 9.00111 6.75 9H10.5C10.8975 9.00111 11.2784 9.15951 11.5594 9.44057C11.8405 9.72163 11.9989 10.1025 12 10.5M12 10.5V11.25M12 10.5C12.0011 10.1025 12.1595 9.72163 12.4406 9.44057C12.7216 9.15951 13.1025 9.00111 13.5 9H17.25C17.6475 9.00111 18.0284 9.15951 18.3094 9.44057C18.5905 9.72163 18.7489 10.1025 18.75 10.5V11.25"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        <span>1 Bedroom</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M21.75 13.125H3.75V4.6875C3.74914 4.36975 3.8113 4.05498 3.9329 3.76142C4.0545 3.46786 4.23312 3.20133 4.45842 2.97726L4.47717 2.95852C4.83026 2.60594 5.28359 2.37097 5.77524 2.28571C6.26688 2.20045 6.77286 2.26906 7.22405 2.48217C6.79794 3.19064 6.62083 4.02119 6.72086 4.84185C6.82089 5.66251 7.19233 6.4262 7.77614 7.01156L8.28947 7.52489L7.34461 8.4698L8.4052 9.53039L9.35006 8.58553L14.5855 3.3502L15.5303 2.40534L14.4697 1.3447L13.5248 2.28956L13.0115 1.77623C12.3969 1.16336 11.5867 0.785558 10.7222 0.708671C9.85764 0.631784 8.99353 0.860685 8.28042 1.35548C7.52846 0.8806 6.63734 0.675589 5.75346 0.774129C4.86958 0.87267 4.04549 1.2689 3.41658 1.89773L3.39783 1.91648C3.03278 2.27952 2.74336 2.71136 2.54634 3.18701C2.34931 3.66266 2.24859 4.17266 2.25 4.6875V13.125H0.75V14.625H2.25V16.0641C2.25001 16.185 2.26951 16.3051 2.30775 16.4198L3.70312 20.6057C3.7776 20.8298 3.92078 21.0248 4.11235 21.1629C4.30392 21.301 4.53413 21.3752 4.77028 21.375H5.37497L4.82812 23.25H6.39061L6.9375 21.375H16.6922L17.2547 23.25H18.8203L18.2578 21.375H19.2295C19.4657 21.3752 19.696 21.301 19.8876 21.1629C20.0792 21.0248 20.2224 20.8299 20.2969 20.6057L21.6922 16.4198C21.7304 16.3051 21.75 16.185 21.75 16.0641V14.625H23.25V13.125H21.75ZM8.83688 2.83687C9.25012 2.42453 9.81007 2.19295 10.3939 2.19295C10.9776 2.19295 11.5376 2.42453 11.9508 2.83687L12.4641 3.3502L9.3502 6.46406L8.83688 5.95083C8.42455 5.53757 8.19298 4.97763 8.19298 4.39385C8.19298 3.81007 8.42455 3.25013 8.83688 2.83687ZM20.25 16.0031L18.9594 19.875H5.04056L3.75 16.0031V14.625H20.25V16.0031Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>1 Bathroom</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                            viewBox="0 0 25 25" fill="none" aria-hidden="true">
                                            <path
                                                d="M3.012 0C1.35888 0 0 1.35864 0 3.012C0 4.45608 1.03608 5.67504 2.4 5.96064V18.0634C1.03608 18.349 0 19.5682 0 21.0122C0 22.6656 1.35864 24.024 3.012 24.024C4.46328 24.024 5.6736 22.9742 5.94984 21.6H18.059C18.3355 22.9757 19.5598 24.024 21.0122 24.024C22.6082 24.024 23.9174 22.7549 24.007 21.1795C24.0181 21.1245 24.0238 21.0684 24.0238 21.0122C24.0238 20.9561 24.0181 20.9 24.007 20.845C23.9287 19.4681 22.9188 18.3254 21.6 18.059V5.96496C22.9188 5.69856 23.9287 4.55616 24.0072 3.17952C24.0184 3.12446 24.024 3.06842 24.024 3.01224C24.024 2.95606 24.0184 2.90002 24.0072 2.84496C23.9172 1.2696 22.6082 0 21.012 0C19.5682 0 18.349 1.03608 18.0634 2.4H5.9448C5.6592 1.03776 4.4544 0 3.012 0ZM3.012 1.68C3.75768 1.68 4.344 2.26656 4.344 3.012C4.344 3.75768 3.75768 4.344 3.012 4.344C2.26632 4.344 1.68 3.75768 1.68 3.012C1.68 2.26632 2.26656 1.68 3.012 1.68ZM21.012 1.68C21.7577 1.68 22.344 2.26656 22.344 3.012C22.344 3.75768 21.7577 4.344 21.012 4.344C20.2663 4.344 19.68 3.75768 19.68 3.012C19.68 2.26632 20.2666 1.68 21.012 1.68ZM5.81232 4.08H18.2011C18.3524 4.47204 18.5832 4.82851 18.879 5.12694C19.1749 5.42537 19.5293 5.65931 19.92 5.814V18.21C19.5332 18.363 19.182 18.5937 18.8878 18.8878C18.5937 19.182 18.363 19.5332 18.21 19.92H5.802C5.64914 19.5302 5.41724 19.1762 5.12088 18.8804C4.82451 18.5845 4.47012 18.3533 4.08 18.2011V5.82288C4.4743 5.66925 4.8321 5.43482 5.1304 5.13466C5.4287 4.8345 5.6609 4.47525 5.81208 4.08M3.012 19.68C3.75744 19.68 4.34376 20.2666 4.34376 21.012C4.34376 21.7577 3.75744 22.344 3.01176 22.344C2.26608 22.344 1.68 21.7577 1.68 21.012C1.68 20.2663 2.26656 19.68 3.012 19.68ZM21.012 19.68C21.7574 19.68 22.3438 20.2666 22.3438 21.012C22.3438 21.7577 21.7574 22.344 21.0118 22.344C20.2661 22.344 19.68 21.7577 19.68 21.012C19.68 20.2663 20.2666 19.68 21.012 19.68Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>1,015 ft²</span>
                                    </div>
                                </div>
                                <div class="property-card__actions">
                                    <a class="property-btn property-btn--primary" href="#" data-bs-toggle="modal"
                                        data-bs-target="#siteEnquiryForm">Enquiry</a>
                                    <a class="property-btn property-btn--light" href="tel:+971501234567">Call Now</a>
                                    <a class="property-btn property-btn--outline" href="https://wa.me/971501234567"
                                        target="_blank" rel="noopener noreferrer">Whatsapp</a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <article class="property-card">
                        <div class="property-card__ghost" aria-hidden="true"></div>
                        <div class="property-card__inner">
                            <div class="property-card__media">
                                <picture>
                                    <img src="public/images/properties/2.jpg" alt="Marina View" width="427" height="260"
                                        loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/2.jpg" alt="Marina View" width="427" height="260"
                                        loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/2.jpg" alt="Marina View" width="427" height="260"
                                        loading="lazy">
                                    <picture>
                                        <img src="public/images/properties/2.jpg" alt="Marina View" width="427"
                                            height="260" loading="lazy">
                                    </picture>
                                    <picture>
                                        <img src="public/images/properties/2.jpg" alt="Marina View" width="427"
                                            height="260" loading="lazy">
                                    </picture>
                                    <picture>
                                        <img src="public/images/properties/2.jpg" alt="Marina View" width="427"
                                            height="260" loading="lazy">
                                    </picture>
                                    <div class="property-card__dots" role="tablist" aria-label="Photo gallery">
                                        <span
                                            class="is-active"></span><span></span><span></span><span></span><span></span>
                                    </div>
                                    <span class="property-card__360" title="360° virtual tour">360°</span>
                            </div>
                            <div class="property-card__body">
                                <h3 class="property-card__title">Marina View Residences</h3>
                                <p class="property-card__location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z"
                                            fill="#2F3F58" />
                                    </svg>
                                    <span>Dubai Marina, Dubai</span>
                                </p>
                                <div class="property-card__meta">
                                    <div class="property-card__price-row">
                                        <div class="property-card__price">110,000 د.إ</div>
                                        <div class="property-card__photos">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path
                                                    d="M13.997 4C14.3578 3.99999 14.7119 4.09759 15.0217 4.28244C15.3316 4.46729 15.5856 4.73251 15.757 5.05L16.243 5.95C16.4144 6.26749 16.6684 6.53271 16.9783 6.71756C17.2881 6.90241 17.6422 7.00001 18.003 7H20C20.5304 7 21.0391 7.21071 21.4142 7.58579C21.7893 7.96086 22 8.46957 22 9V18C22 18.5304 21.7893 19.0391 21.4142 19.4142C21.0391 19.7893 20.5304 20 20 20H4C3.46957 20 2.96086 19.7893 2.58579 19.4142C2.21071 19.0391 2 18.5304 2 18V9C2 8.46957 2.21071 7.96086 2.58579 7.58579C2.96086 7.21071 3.46957 7 4 7H5.997C6.35742 7.00002 6.71115 6.90264 7.02078 6.71817C7.33041 6.53369 7.58444 6.26897 7.756 5.952L8.245 5.048C8.41656 4.73103 8.67059 4.46631 8.98022 4.28183C9.28985 4.09736 9.64358 3.99998 10.004 4H13.997Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            18 Photos
                                        </div>
                                    </div>
                                    <div class="property-card__tags">
                                        <span class="property-tag property-tag--fill">Apartment</span>
                                        <span class="property-tag property-tag--outline">Rentals</span>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M2.25 19.5V14.25C2.25235 13.4551 2.56917 12.6934 3.13128 12.1313C3.69338 11.5692 4.45507 11.2523 5.25 11.25H18.75C19.5449 11.2523 20.3066 11.5692 20.8687 12.1313C21.4308 12.6934 21.7477 13.4551 21.75 14.25V19.5M18 11.25H4.5V6.375C4.50148 5.87818 4.6995 5.40212 5.05081 5.05081C5.40212 4.6995 5.87818 4.50148 6.375 4.5H17.625C18.1218 4.50148 18.5979 4.6995 18.9492 5.05081C19.3005 5.40212 19.4985 5.87818 19.5 6.375V11.25H18Z"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M2.25 19.5V19.125C2.25087 18.8269 2.36967 18.5413 2.58046 18.3305C2.79125 18.1197 3.0769 18.0009 3.375 18H20.625C20.9231 18.0009 21.2087 18.1197 21.4195 18.3305C21.6303 18.5413 21.7491 18.8269 21.75 19.125V19.5M5.25 11.25V10.5C5.25111 10.1025 5.40951 9.72163 5.69057 9.44057C5.97163 9.15951 6.35252 9.00111 6.75 9H10.5C10.8975 9.00111 11.2784 9.15951 11.5594 9.44057C11.8405 9.72163 11.9989 10.1025 12 10.5M12 10.5V11.25M12 10.5C12.0011 10.1025 12.1595 9.72163 12.4406 9.44057C12.7216 9.15951 13.1025 9.00111 13.5 9H17.25C17.6475 9.00111 18.0284 9.15951 18.3094 9.44057C18.5905 9.72163 18.7489 10.1025 18.75 10.5V11.25"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        <span>2 Bedrooms</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M21.75 13.125H3.75V4.6875C3.74914 4.36975 3.8113 4.05498 3.9329 3.76142C4.0545 3.46786 4.23312 3.20133 4.45842 2.97726L4.47717 2.95852C4.83026 2.60594 5.28359 2.37097 5.77524 2.28571C6.26688 2.20045 6.77286 2.26906 7.22405 2.48217C6.79794 3.19064 6.62083 4.02119 6.72086 4.84185C6.82089 5.66251 7.19233 6.4262 7.77614 7.01156L8.28947 7.52489L7.34461 8.4698L8.4052 9.53039L9.35006 8.58553L14.5855 3.3502L15.5303 2.40534L14.4697 1.3447L13.5248 2.28956L13.0115 1.77623C12.3969 1.16336 11.5867 0.785558 10.7222 0.708671C9.85764 0.631784 8.99353 0.860685 8.28042 1.35548C7.52846 0.8806 6.63734 0.675589 5.75346 0.774129C4.86958 0.87267 4.04549 1.2689 3.41658 1.89773L3.39783 1.91648C3.03278 2.27952 2.74336 2.71136 2.54634 3.18701C2.34931 3.66266 2.24859 4.17266 2.25 4.6875V13.125H0.75V14.625H2.25V16.0641C2.25001 16.185 2.26951 16.3051 2.30775 16.4198L3.70312 20.6057C3.7776 20.8298 3.92078 21.0248 4.11235 21.1629C4.30392 21.301 4.53413 21.3752 4.77028 21.375H5.37497L4.82812 23.25H6.39061L6.9375 21.375H16.6922L17.2547 23.25H18.8203L18.2578 21.375H19.2295C19.4657 21.3752 19.696 21.301 19.8876 21.1629C20.0792 21.0248 20.2224 20.8299 20.2969 20.6057L21.6922 16.4198C21.7304 16.3051 21.75 16.185 21.75 16.0641V14.625H23.25V13.125H21.75ZM8.83688 2.83687C9.25012 2.42453 9.81007 2.19295 10.3939 2.19295C10.9776 2.19295 11.5376 2.42453 11.9508 2.83687L12.4641 3.3502L9.3502 6.46406L8.83688 5.95083C8.42455 5.53757 8.19298 4.97763 8.19298 4.39385C8.19298 3.81007 8.42455 3.25013 8.83688 2.83687ZM20.25 16.0031L18.9594 19.875H5.04056L3.75 16.0031V14.625H20.25V16.0031Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>2 Bathrooms</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                            viewBox="0 0 25 25" fill="none" aria-hidden="true">
                                            <path
                                                d="M3.012 0C1.35888 0 0 1.35864 0 3.012C0 4.45608 1.03608 5.67504 2.4 5.96064V18.0634C1.03608 18.349 0 19.5682 0 21.0122C0 22.6656 1.35864 24.024 3.012 24.024C4.46328 24.024 5.6736 22.9742 5.94984 21.6H18.059C18.3355 22.9757 19.5598 24.024 21.0122 24.024C22.6082 24.024 23.9174 22.7549 24.007 21.1795C24.0181 21.1245 24.0238 21.0684 24.0238 21.0122C24.0238 20.9561 24.0181 20.9 24.007 20.845C23.9287 19.4681 22.9188 18.3254 21.6 18.059V5.96496C22.9188 5.69856 23.9287 4.55616 24.0072 3.17952C24.0184 3.12446 24.024 3.06842 24.024 3.01224C24.024 2.95606 24.0184 2.90002 24.0072 2.84496C23.9172 1.2696 22.6082 0 21.012 0C19.5682 0 18.349 1.03608 18.0634 2.4H5.9448C5.6592 1.03776 4.4544 0 3.012 0ZM3.012 1.68C3.75768 1.68 4.344 2.26656 4.344 3.012C4.344 3.75768 3.75768 4.344 3.012 4.344C2.26632 4.344 1.68 3.75768 1.68 3.012C1.68 2.26632 2.26656 1.68 3.012 1.68ZM21.012 1.68C21.7577 1.68 22.344 2.26656 22.344 3.012C22.344 3.75768 21.7577 4.344 21.012 4.344C20.2663 4.344 19.68 3.75768 19.68 3.012C19.68 2.26632 20.2666 1.68 21.012 1.68ZM5.81232 4.08H18.2011C18.3524 4.47204 18.5832 4.82851 18.879 5.12694C19.1749 5.42537 19.5293 5.65931 19.92 5.814V18.21C19.5332 18.363 19.182 18.5937 18.8878 18.8878C18.5937 19.182 18.363 19.5332 18.21 19.92H5.802C5.64914 19.5302 5.41724 19.1762 5.12088 18.8804C4.82451 18.5845 4.47012 18.3533 4.08 18.2011V5.82288C4.4743 5.66925 4.8321 5.43482 5.1304 5.13466C5.4287 4.8345 5.6609 4.47525 5.81208 4.08M3.012 19.68C3.75744 19.68 4.34376 20.2666 4.34376 21.012C4.34376 21.7577 3.75744 22.344 3.01176 22.344C2.26608 22.344 1.68 21.7577 1.68 21.012C1.68 20.2663 2.26656 19.68 3.012 19.68ZM21.012 19.68C21.7574 19.68 22.3438 20.2666 22.3438 21.012C22.3438 21.7577 21.7574 22.344 21.0118 22.344C20.2661 22.344 19.68 21.7577 19.68 21.012C19.68 20.2663 20.2666 19.68 21.012 19.68Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>1,450 ft²</span>
                                    </div>
                                </div>
                                <div class="property-card__actions">
                                    <a class="property-btn property-btn--primary" href="#" data-bs-toggle="modal"
                                        data-bs-target="#siteEnquiryForm">Enquiry</a>
                                    <a class="property-btn property-btn--light" href="tel:+971501234567">Call Now</a>
                                    <a class="property-btn property-btn--outline" href="https://wa.me/971501234567"
                                        target="_blank" rel="noopener noreferrer">Whatsapp</a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <article class="property-card">
                        <div class="property-card__ghost" aria-hidden="true"></div>
                        <div class="property-card__inner">
                            <div class="property-card__media">
                                <picture>
                                    <img src="public/images/properties/3.jpg" alt="Palm Heights" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/3.jpg" alt="Palm Heights" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/3.jpg" alt="Palm Heights" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/3.jpg" alt="Palm Heights" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <picture>
                                    <img src="public/images/properties/3.jpg" alt="Palm Heights" width="427"
                                        height="260" loading="lazy">
                                </picture>
                                <div class="property-card__dots" role="tablist" aria-label="Photo gallery">
                                    <span class="is-active"></span><span></span><span></span><span></span><span></span>
                                </div>
                                <span class="property-card__360" title="360° virtual tour">360°</span>
                            </div>
                            <div class="property-card__body">
                                <h3 class="property-card__title">Palm Heights</h3>
                                <p class="property-card__location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path
                                            d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z"
                                            fill="#2F3F58" />
                                    </svg>
                                    <span>Palm Jumeirah, Dubai</span>
                                </p>
                                <div class="property-card__meta">
                                    <div class="property-card__price-row">
                                        <div class="property-card__price">145,000 د.إ</div>
                                        <div class="property-card__photos">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path
                                                    d="M13.997 4C14.3578 3.99999 14.7119 4.09759 15.0217 4.28244C15.3316 4.46729 15.5856 4.73251 15.757 5.05L16.243 5.95C16.4144 6.26749 16.6684 6.53271 16.9783 6.71756C17.2881 6.90241 17.6422 7.00001 18.003 7H20C20.5304 7 21.0391 7.21071 21.4142 7.58579C21.7893 7.96086 22 8.46957 22 9V18C22 18.5304 21.7893 19.0391 21.4142 19.4142C21.0391 19.7893 20.5304 20 20 20H4C3.46957 20 2.96086 19.7893 2.58579 19.4142C2.21071 19.0391 2 18.5304 2 18V9C2 8.46957 2.21071 7.96086 2.58579 7.58579C2.96086 7.21071 3.46957 7 4 7H5.997C6.35742 7.00002 6.71115 6.90264 7.02078 6.71817C7.33041 6.53369 7.58444 6.26897 7.756 5.952L8.245 5.048C8.41656 4.73103 8.67059 4.46631 8.98022 4.28183C9.28985 4.09736 9.64358 3.99998 10.004 4H13.997Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z"
                                                    stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            24 Photos
                                        </div>
                                    </div>
                                    <div class="property-card__tags">
                                        <span class="property-tag property-tag--fill">Apartment</span>
                                        <span class="property-tag property-tag--outline">Rentals</span>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M2.25 19.5V14.25C2.25235 13.4551 2.56917 12.6934 3.13128 12.1313C3.69338 11.5692 4.45507 11.2523 5.25 11.25H18.75C19.5449 11.2523 20.3066 11.5692 20.8687 12.1313C21.4308 12.6934 21.7477 13.4551 21.75 14.25V19.5M18 11.25H4.5V6.375C4.50148 5.87818 4.6995 5.40212 5.05081 5.05081C5.40212 4.6995 5.87818 4.50148 6.375 4.5H17.625C18.1218 4.50148 18.5979 4.6995 18.9492 5.05081C19.3005 5.40212 19.4985 5.87818 19.5 6.375V11.25H18Z"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M2.25 19.5V19.125C2.25087 18.8269 2.36967 18.5413 2.58046 18.3305C2.79125 18.1197 3.0769 18.0009 3.375 18H20.625C20.9231 18.0009 21.2087 18.1197 21.4195 18.3305C21.6303 18.5413 21.7491 18.8269 21.75 19.125V19.5M5.25 11.25V10.5C5.25111 10.1025 5.40951 9.72163 5.69057 9.44057C5.97163 9.15951 6.35252 9.00111 6.75 9H10.5C10.8975 9.00111 11.2784 9.15951 11.5594 9.44057C11.8405 9.72163 11.9989 10.1025 12 10.5M12 10.5V11.25M12 10.5C12.0011 10.1025 12.1595 9.72163 12.4406 9.44057C12.7216 9.15951 13.1025 9.00111 13.5 9H17.25C17.6475 9.00111 18.0284 9.15951 18.3094 9.44057C18.5905 9.72163 18.7489 10.1025 18.75 10.5V11.25"
                                                stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        <span>3 Bedrooms</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path
                                                d="M21.75 13.125H3.75V4.6875C3.74914 4.36975 3.8113 4.05498 3.9329 3.76142C4.0545 3.46786 4.23312 3.20133 4.45842 2.97726L4.47717 2.95852C4.83026 2.60594 5.28359 2.37097 5.77524 2.28571C6.26688 2.20045 6.77286 2.26906 7.22405 2.48217C6.79794 3.19064 6.62083 4.02119 6.72086 4.84185C6.82089 5.66251 7.19233 6.4262 7.77614 7.01156L8.28947 7.52489L7.34461 8.4698L8.4052 9.53039L9.35006 8.58553L14.5855 3.3502L15.5303 2.40534L14.4697 1.3447L13.5248 2.28956L13.0115 1.77623C12.3969 1.16336 11.5867 0.785558 10.7222 0.708671C9.85764 0.631784 8.99353 0.860685 8.28042 1.35548C7.52846 0.8806 6.63734 0.675589 5.75346 0.774129C4.86958 0.87267 4.04549 1.2689 3.41658 1.89773L3.39783 1.91648C3.03278 2.27952 2.74336 2.71136 2.54634 3.18701C2.34931 3.66266 2.24859 4.17266 2.25 4.6875V13.125H0.75V14.625H2.25V16.0641C2.25001 16.185 2.26951 16.3051 2.30775 16.4198L3.70312 20.6057C3.7776 20.8298 3.92078 21.0248 4.11235 21.1629C4.30392 21.301 4.53413 21.3752 4.77028 21.375H5.37497L4.82812 23.25H6.39061L6.9375 21.375H16.6922L17.2547 23.25H18.8203L18.2578 21.375H19.2295C19.4657 21.3752 19.696 21.301 19.8876 21.1629C20.0792 21.0248 20.2224 20.8299 20.2969 20.6057L21.6922 16.4198C21.7304 16.3051 21.75 16.185 21.75 16.0641V14.625H23.25V13.125H21.75ZM8.83688 2.83687C9.25012 2.42453 9.81007 2.19295 10.3939 2.19295C10.9776 2.19295 11.5376 2.42453 11.9508 2.83687L12.4641 3.3502L9.3502 6.46406L8.83688 5.95083C8.42455 5.53757 8.19298 4.97763 8.19298 4.39385C8.19298 3.81007 8.42455 3.25013 8.83688 2.83687ZM20.25 16.0031L18.9594 19.875H5.04056L3.75 16.0031V14.625H20.25V16.0031Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>3 Bathrooms</span>
                                    </div>
                                    <div class="property-details__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                                            viewBox="0 0 25 25" fill="none" aria-hidden="true">
                                            <path
                                                d="M3.012 0C1.35888 0 0 1.35864 0 3.012C0 4.45608 1.03608 5.67504 2.4 5.96064V18.0634C1.03608 18.349 0 19.5682 0 21.0122C0 22.6656 1.35864 24.024 3.012 24.024C4.46328 24.024 5.6736 22.9742 5.94984 21.6H18.059C18.3355 22.9757 19.5598 24.024 21.0122 24.024C22.6082 24.024 23.9174 22.7549 24.007 21.1795C24.0181 21.1245 24.0238 21.0684 24.0238 21.0122C24.0238 20.9561 24.0181 20.9 24.007 20.845C23.9287 19.4681 22.9188 18.3254 21.6 18.059V5.96496C22.9188 5.69856 23.9287 4.55616 24.0072 3.17952C24.0184 3.12446 24.024 3.06842 24.024 3.01224C24.024 2.95606 24.0184 2.90002 24.0072 2.84496C23.9172 1.2696 22.6082 0 21.012 0C19.5682 0 18.349 1.03608 18.0634 2.4H5.9448C5.6592 1.03776 4.4544 0 3.012 0ZM3.012 1.68C3.75768 1.68 4.344 2.26656 4.344 3.012C4.344 3.75768 3.75768 4.344 3.012 4.344C2.26632 4.344 1.68 3.75768 1.68 3.012C1.68 2.26632 2.26656 1.68 3.012 1.68ZM21.012 1.68C21.7577 1.68 22.344 2.26656 22.344 3.012C22.344 3.75768 21.7577 4.344 21.012 4.344C20.2663 4.344 19.68 3.75768 19.68 3.012C19.68 2.26632 20.2666 1.68 21.012 1.68ZM5.81232 4.08H18.2011C18.3524 4.47204 18.5832 4.82851 18.879 5.12694C19.1749 5.42537 19.5293 5.65931 19.92 5.814V18.21C19.5332 18.363 19.182 18.5937 18.8878 18.8878C18.5937 19.182 18.363 19.5332 18.21 19.92H5.802C5.64914 19.5302 5.41724 19.1762 5.12088 18.8804C4.82451 18.5845 4.47012 18.3533 4.08 18.2011V5.82288C4.4743 5.66925 4.8321 5.43482 5.1304 5.13466C5.4287 4.8345 5.6609 4.47525 5.81208 4.08M3.012 19.68C3.75744 19.68 4.34376 20.2666 4.34376 21.012C4.34376 21.7577 3.75744 22.344 3.01176 22.344C2.26608 22.344 1.68 21.7577 1.68 21.012C1.68 20.2663 2.26656 19.68 3.012 19.68ZM21.012 19.68C21.7574 19.68 22.3438 20.2666 22.3438 21.012C22.3438 21.7577 21.7574 22.344 21.0118 22.344C20.2661 22.344 19.68 21.7577 19.68 21.012C19.68 20.2663 20.2666 19.68 21.012 19.68Z"
                                                fill="#2F3F58" />
                                        </svg>
                                        <span>2,200 ft²</span>
                                    </div>
                                </div>
                                <div class="property-card__actions">
                                    <a class="property-btn property-btn--primary" href="#" data-bs-toggle="modal"
                                        data-bs-target="#siteEnquiryForm">Enquiry</a>
                                    <a class="property-btn property-btn--light" href="tel:+971501234567">Call Now</a>
                                    <a class="property-btn property-btn--outline" href="https://wa.me/971501234567"
                                        target="_blank" rel="noopener noreferrer">Whatsapp</a>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

            </div>

        </div>

        <div class="property-slider-controls">
            <div class="property-slider__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                aria-valuenow="33">
                <span class="property-slider__progress-fill"></span>
            </div>
            <div class="property-slider__arrows">
                <button type="button" class="property-slider__arrow property-slider__arrow--prev"
                    aria-label="Previous properties">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M20 12H4M4 12L10 6M4 12L10 18" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
                <button type="button" class="property-slider__arrow property-slider__arrow--next"
                    aria-label="Next properties">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M4 12L20 12M20 12L14 18M20 12L14 6" stroke="#A4B3C9" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

    </section>


    <section class="neighborhoods commonPadding">
        <div class="container-ctn">
            <div class="head text-center">
                <h2>Explore Neighborhoods</h2>
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                    industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type
                    and scrambled it to make a type specimen book.</p>
            </div>

        </div>
        <div class="container-center">

            <div class="neighborhoods__tabs-wrapper">
                <ul class="nav nav-pills neighborhoods__tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="dubai-tab" data-bs-toggle="tab" data-bs-target="#dubai"
                            type="button" role="tab" aria-controls="dubai" aria-selected="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path d="M7 2V22" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7.25 3C7.25 3 14 6.5 16 11C18 15.5 16.5 22 16.5 22" stroke="#4B5C77"
                                    stroke-width="2" />
                                <path d="M2 22H22" stroke="#4B5C77" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M5 7.5H16" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7 11H11" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7 14.5H13" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7 18H13.5" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            Dubai
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sharjah-tab" data-bs-toggle="tab" data-bs-target="#sharjah"
                            type="button" role="tab" aria-controls="sharjah" aria-selected="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path d="M7 2V22" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7.25 3C7.25 3 14 6.5 16 11C18 15.5 16.5 22 16.5 22" stroke="#4B5C77"
                                    stroke-width="2" />
                                <path d="M2 22H22" stroke="#4B5C77" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M5 7.5H16" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7 11H11" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7 14.5H13" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                                <path d="M7 18H13.5" stroke="#4B5C77" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            Sharjah
                        </button>
                    </li>
                </ul>
                <div class="tab-content neighborhoods__tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="dubai" role="tabpanel" aria-labelledby="dubai-tab">
                        <div class="neighborhoods__map-container">
                            <img src="public/images/map.jpg" alt="Dubai Map" class="neighborhoods__map-img">
                        </div>
                    </div>
                    <div class="tab-pane fade" id="sharjah" role="tabpanel" aria-labelledby="sharjah-tab">
                        <div class="neighborhoods__map-container">
                            <img src="public/images/map.jpg" alt="Sharjah Map" class="neighborhoods__map-img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="about-home commonPadding">
        <div class="container-center ">
            <div class="d-flex flex-wrap justify-content-between">
                <div class="about-home-img position-relative">
                    <picture>
                        <img src="public/images/about.jpg" width="665" height="461" alt="">
                    </picture>
                    <svg xmlns="http://www.w3.org/2000/svg" width="665" height="461" viewBox="0 0 665 461" fill="none">
                        <path
                            d="M49.9999 0.5H664.5V362.181L608.518 404.448L608.507 404.457L608.496 404.466L542.35 460.5H0.499878V50C0.499883 22.6619 22.6618 0.5 49.9999 0.5Z"
                            stroke="#2A559C" />
                    </svg>
                </div>
                <div class="about-home-content">
                    <div class="head w-100">
                        <span>About Us</span>
                        <h2>Welcome to Distinguished Real Estate</h2>
                        <p>Distinguished Real Estate (DRE), established in 2003 by one of the UAE's leading families, is
                            a
                            thriving real estate company headquartered in Dubai with branches in Sharjah. With over
                            3,000
                            properties under our management, our focus...</p>
                    </div>
                    <a href="" class="btn theme-btn d-flex align-items-center">
                        <span>Learn More</span>
                        <i>
                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23"
                                fill="none">
                                <path d="M7.54255 15.0849L15.085 7.54241M15.085 7.54241H9.42817M15.085 7.54241V13.1993"
                                    stroke="white" stroke-width="1.33333" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </i>
                    </a>
                </div>
            </div>

        </div>
    </section>


    <section class="news commonPadding" style="background: #F7F9FB;">
        <div class="container-center">
            <div class="head text-center">
                <span>News & Insights</span>
                <h2>Browse Our Latest News & Articles</h2>
            </div>

            <div class="news-slider-wrap position-relative">
                <div class="news-card-slider">
                    <!-- Article 1 -->

                    <div class="news-card">
                        <div class="news-card__img">
                            <picture>
                                <img src="public/images/news/1.jpg" alt="News Image" width="246" height="452"
                                    class="img-fluid w-100 " style="object-fit: cover;">
                            </picture>
                        </div>
                        <div class="news-card__content">
                            <div class="news-card__meta">
                                <span class="author">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" aria-hidden="true">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    Dist3Admin
                                </span>
                                <span class="date">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    May 27, 2014
                                </span>
                            </div>
                            <h3 class="news-card__title">The Ultimate Guide to First-Time...</h3>
                            <p class="news-card__desc">Business meeting of real estate broker, Business meeting working
                                with new startup project. Idea presentation analyze plan.</p>
                            <a href="#" class="theme-btn d-inline-flex align-items-center mt-auto">
                                <span>Learn More</span>
                                <i>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23"
                                        fill="none">
                                        <path
                                            d="M7.54255 15.0849L15.085 7.54241M15.085 7.54241H9.42817M15.085 7.54241V13.1993"
                                            stroke="white" stroke-width="1.33333" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                </i>
                            </a>
                        </div>
                    </div>


                    <div class="news-card">
                        <div class="news-card__img">
                            <picture>
                                <img src="public/images/news/2.jpg" alt="News Image" width="246" height="452"
                                    class="img-fluid w-100 " style="object-fit: cover;">
                            </picture>
                        </div>
                        <div class="news-card__content">
                            <div class="news-card__meta d-flex flex-wrap align-items-center">
                                <span class="author d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                        fill="none">
                                        <path
                                            d="M11.7825 9.53271C12.5178 8.95421 13.0545 8.16091 13.3179 7.26317C13.5814 6.36544 13.5584 5.40791 13.2523 4.52382C12.9462 3.63972 12.3722 2.87301 11.6101 2.33036C10.8479 1.7877 9.93559 1.49609 9 1.49609C8.06441 1.49609 7.15208 1.7877 6.38995 2.33036C5.62781 2.87301 5.05376 3.63972 4.74766 4.52382C4.44156 5.40791 4.41863 6.36544 4.68207 7.26317C4.9455 8.16091 5.4822 8.95421 6.2175 9.53271C4.95756 10.0375 3.85821 10.8747 3.03667 11.9552C2.21512 13.0356 1.70217 14.3187 1.5525 15.6677C1.54166 15.7662 1.55033 15.8659 1.57802 15.961C1.6057 16.0561 1.65185 16.1449 1.71383 16.2222C1.83901 16.3783 2.02109 16.4783 2.22 16.5002C2.41891 16.5221 2.61837 16.4641 2.77449 16.3389C2.93062 16.2137 3.03062 16.0316 3.0525 15.8327C3.21719 14.3666 3.91626 13.0126 5.01616 12.0293C6.11606 11.046 7.53967 10.5025 9.015 10.5025C10.4903 10.5025 11.9139 11.046 13.0138 12.0293C14.1137 13.0126 14.8128 14.3666 14.9775 15.8327C14.9979 16.017 15.0858 16.1872 15.2243 16.3105C15.3628 16.4337 15.5421 16.5013 15.7275 16.5002H15.81C16.0066 16.4776 16.1863 16.3782 16.3099 16.2237C16.4335 16.0691 16.4911 15.872 16.47 15.6752C16.3196 14.3224 15.8039 13.036 14.9781 11.9539C14.1524 10.8718 13.0477 10.0348 11.7825 9.53271ZM9 9.00022C8.40665 9.00022 7.82663 8.82427 7.33329 8.49462C6.83994 8.16498 6.45542 7.69644 6.22836 7.14827C6.0013 6.60009 5.94189 5.99689 6.05764 5.41494C6.1734 4.833 6.45912 4.29845 6.87868 3.87889C7.29823 3.45934 7.83278 3.17362 8.41473 3.05786C8.99667 2.9421 9.59987 3.00151 10.148 3.22858C10.6962 3.45564 11.1648 3.84016 11.4944 4.3335C11.8241 4.82685 12 5.40687 12 6.00022C12 6.79587 11.6839 7.55893 11.1213 8.12154C10.5587 8.68414 9.79565 9.00022 9 9.00022Z"
                                            fill="#2A559C" />
                                    </svg>
                                    Dist3Admin
                                </span>
                                <span class="date d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                        fill="none">
                                        <path
                                            d="M12.375 3.75V2.25M5.625 3.75V2.25M2.4375 6H15.5625M2.25 7.533C2.25 5.94675 2.25 5.15325 2.577 4.54725C2.87268 4.00673 3.3315 3.57338 3.888 3.309C4.53 3 5.37 3 7.05 3H10.95C12.63 3 13.47 3 14.112 3.309C14.6768 3.5805 15.135 4.014 15.423 4.5465C15.75 5.154 15.75 5.9475 15.75 7.53375V11.2177C15.75 12.804 15.75 13.5975 15.423 14.2035C15.1273 14.744 14.6685 15.1774 14.112 15.4418C13.47 15.75 12.63 15.75 10.95 15.75H7.05C5.37 15.75 4.53 15.75 3.888 15.441C3.33161 15.1768 2.87281 14.7437 2.577 14.2035C2.25 13.596 2.25 12.8025 2.25 11.2162V7.533Z"
                                            stroke="#2A559C" stroke-width="1.125" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    May 27, 2014
                                </span>
                            </div>
                            <h3 class="news-card__title">Distinguished Real Estate Partners...</h3>
                            <p class="news-card__desc">Distinguished Real Estate (DRE), one of the largest fully
                                integrated real estate developers in the UAE, announced its partnership...</p>
                            <a href="#" class="theme-btn d-inline-flex align-items-center mt-auto">
                                <span>Learn More</span>
                                <i>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23"
                                        fill="none">
                                        <path
                                            d="M7.54255 15.0849L15.085 7.54241M15.085 7.54241H9.42817M15.085 7.54241V13.1993"
                                            stroke="white" stroke-width="1.33333" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                </i>
                            </a>
                        </div>

                    </div>
                    <div class="news-card">
                        <div class="news-card__img">
                            <picture>
                                <img src="public/images/news/1.jpg" alt="News Image" width="246" height="452"
                                    class="img-fluid w-100 " style="object-fit: cover;">
                            </picture>
                        </div>
                        <div class="news-card__content">
                            <div class="news-card__meta d-flex flex-wrap align-items-center">
                                <span class="author d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                        fill="none">
                                        <path
                                            d="M11.7825 9.53271C12.5178 8.95421 13.0545 8.16091 13.3179 7.26317C13.5814 6.36544 13.5584 5.40791 13.2523 4.52382C12.9462 3.63972 12.3722 2.87301 11.6101 2.33036C10.8479 1.7877 9.93559 1.49609 9 1.49609C8.06441 1.49609 7.15208 1.7877 6.38995 2.33036C5.62781 2.87301 5.05376 3.63972 4.74766 4.52382C4.44156 5.40791 4.41863 6.36544 4.68207 7.26317C4.9455 8.16091 5.4822 8.95421 6.2175 9.53271C4.95756 10.0375 3.85821 10.8747 3.03667 11.9552C2.21512 13.0356 1.70217 14.3187 1.5525 15.6677C1.54166 15.7662 1.55033 15.8659 1.57802 15.961C1.6057 16.0561 1.65185 16.1449 1.71383 16.2222C1.83901 16.3783 2.02109 16.4783 2.22 16.5002C2.41891 16.5221 2.61837 16.4641 2.77449 16.3389C2.93062 16.2137 3.03062 16.0316 3.0525 15.8327C3.21719 14.3666 3.91626 13.0126 5.01616 12.0293C6.11606 11.046 7.53967 10.5025 9.015 10.5025C10.4903 10.5025 11.9139 11.046 13.0138 12.0293C14.1137 13.0126 14.8128 14.3666 14.9775 15.8327C14.9979 16.017 15.0858 16.1872 15.2243 16.3105C15.3628 16.4337 15.5421 16.5013 15.7275 16.5002H15.81C16.0066 16.4776 16.1863 16.3782 16.3099 16.2237C16.4335 16.0691 16.4911 15.872 16.47 15.6752C16.3196 14.3224 15.8039 13.036 14.9781 11.9539C14.1524 10.8718 13.0477 10.0348 11.7825 9.53271ZM9 9.00022C8.40665 9.00022 7.82663 8.82427 7.33329 8.49462C6.83994 8.16498 6.45542 7.69644 6.22836 7.14827C6.0013 6.60009 5.94189 5.99689 6.05764 5.41494C6.1734 4.833 6.45912 4.29845 6.87868 3.87889C7.29823 3.45934 7.83278 3.17362 8.41473 3.05786C8.99667 2.9421 9.59987 3.00151 10.148 3.22858C10.6962 3.45564 11.1648 3.84016 11.4944 4.3335C11.8241 4.82685 12 5.40687 12 6.00022C12 6.79587 11.6839 7.55893 11.1213 8.12154C10.5587 8.68414 9.79565 9.00022 9 9.00022Z"
                                            fill="#2A559C" />
                                    </svg>
                                    Dist3Admin
                                </span>
                                <span class="date d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                        fill="none">
                                        <path
                                            d="M12.375 3.75V2.25M5.625 3.75V2.25M2.4375 6H15.5625M2.25 7.533C2.25 5.94675 2.25 5.15325 2.577 4.54725C2.87268 4.00673 3.3315 3.57338 3.888 3.309C4.53 3 5.37 3 7.05 3H10.95C12.63 3 13.47 3 14.112 3.309C14.6768 3.5805 15.135 4.014 15.423 4.5465C15.75 5.154 15.75 5.9475 15.75 7.53375V11.2177C15.75 12.804 15.75 13.5975 15.423 14.2035C15.1273 14.744 14.6685 15.1774 14.112 15.4418C13.47 15.75 12.63 15.75 10.95 15.75H7.05C5.37 15.75 4.53 15.75 3.888 15.441C3.33161 15.1768 2.87281 14.7437 2.577 14.2035C2.25 13.596 2.25 12.8025 2.25 11.2162V7.533Z"
                                            stroke="#2A559C" stroke-width="1.125" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    May 27, 2014
                                </span>
                            </div>
                            <h3 class="news-card__title">The Ultimate Guide to First-Time...</h3>
                            <p class="news-card__desc">Business meeting of real estate broker, Business meeting working
                                with new startup project. Idea presentation analyze plan.</p>
                            <a href="#" class="theme-btn d-inline-flex align-items-center mt-auto">
                                <span>Learn More</span>
                                <i>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23"
                                        fill="none">
                                        <path
                                            d="M7.54255 15.0849L15.085 7.54241M15.085 7.54241H9.42817M15.085 7.54241V13.1993"
                                            stroke="white" stroke-width="1.33333" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                </i>
                            </a>
                        </div>
                    </div>


                    <div class="news-card">
                        <div class="news-card__img">
                            <picture>
                                <img src="public/images/news/2.jpg" alt="News Image" width="246" height="452"
                                    class="img-fluid w-100 h-100" style="object-fit: cover;">
                            </picture>
                        </div>
                        <div class="news-card__content">
                            <div class="news-card__meta d-flex flex-wrap align-items-center">
                                <span class="author d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                        fill="none">
                                        <path
                                            d="M11.7825 9.53271C12.5178 8.95421 13.0545 8.16091 13.3179 7.26317C13.5814 6.36544 13.5584 5.40791 13.2523 4.52382C12.9462 3.63972 12.3722 2.87301 11.6101 2.33036C10.8479 1.7877 9.93559 1.49609 9 1.49609C8.06441 1.49609 7.15208 1.7877 6.38995 2.33036C5.62781 2.87301 5.05376 3.63972 4.74766 4.52382C4.44156 5.40791 4.41863 6.36544 4.68207 7.26317C4.9455 8.16091 5.4822 8.95421 6.2175 9.53271C4.95756 10.0375 3.85821 10.8747 3.03667 11.9552C2.21512 13.0356 1.70217 14.3187 1.5525 15.6677C1.54166 15.7662 1.55033 15.8659 1.57802 15.961C1.6057 16.0561 1.65185 16.1449 1.71383 16.2222C1.83901 16.3783 2.02109 16.4783 2.22 16.5002C2.41891 16.5221 2.61837 16.4641 2.77449 16.3389C2.93062 16.2137 3.03062 16.0316 3.0525 15.8327C3.21719 14.3666 3.91626 13.0126 5.01616 12.0293C6.11606 11.046 7.53967 10.5025 9.015 10.5025C10.4903 10.5025 11.9139 11.046 13.0138 12.0293C14.1137 13.0126 14.8128 14.3666 14.9775 15.8327C14.9979 16.017 15.0858 16.1872 15.2243 16.3105C15.3628 16.4337 15.5421 16.5013 15.7275 16.5002H15.81C16.0066 16.4776 16.1863 16.3782 16.3099 16.2237C16.4335 16.0691 16.4911 15.872 16.47 15.6752C16.3196 14.3224 15.8039 13.036 14.9781 11.9539C14.1524 10.8718 13.0477 10.0348 11.7825 9.53271ZM9 9.00022C8.40665 9.00022 7.82663 8.82427 7.33329 8.49462C6.83994 8.16498 6.45542 7.69644 6.22836 7.14827C6.0013 6.60009 5.94189 5.99689 6.05764 5.41494C6.1734 4.833 6.45912 4.29845 6.87868 3.87889C7.29823 3.45934 7.83278 3.17362 8.41473 3.05786C8.99667 2.9421 9.59987 3.00151 10.148 3.22858C10.6962 3.45564 11.1648 3.84016 11.4944 4.3335C11.8241 4.82685 12 5.40687 12 6.00022C12 6.79587 11.6839 7.55893 11.1213 8.12154C10.5587 8.68414 9.79565 9.00022 9 9.00022Z"
                                            fill="#2A559C" />
                                    </svg>
                                    Dist3Admin
                                </span>
                                <span class="date d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                        fill="none">
                                        <path
                                            d="M12.375 3.75V2.25M5.625 3.75V2.25M2.4375 6H15.5625M2.25 7.533C2.25 5.94675 2.25 5.15325 2.577 4.54725C2.87268 4.00673 3.3315 3.57338 3.888 3.309C4.53 3 5.37 3 7.05 3H10.95C12.63 3 13.47 3 14.112 3.309C14.6768 3.5805 15.135 4.014 15.423 4.5465C15.75 5.154 15.75 5.9475 15.75 7.53375V11.2177C15.75 12.804 15.75 13.5975 15.423 14.2035C15.1273 14.744 14.6685 15.1774 14.112 15.4418C13.47 15.75 12.63 15.75 10.95 15.75H7.05C5.37 15.75 4.53 15.75 3.888 15.441C3.33161 15.1768 2.87281 14.7437 2.577 14.2035C2.25 13.596 2.25 12.8025 2.25 11.2162V7.533Z"
                                            stroke="#2A559C" stroke-width="1.125" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    May 27, 2014
                                </span>
                            </div>
                            <h3 class="news-card__title">Distinguished Real Estate Partners...</h3>
                            <p class="news-card__desc">Distinguished Real Estate (DRE), one of the largest fully
                                integrated real estate developers in the UAE, announced its partnership...</p>
                            <a href="#" class="theme-btn d-inline-flex align-items-center mt-auto">
                                <span>Learn More</span>
                                <i>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23"
                                        fill="none">
                                        <path
                                            d="M7.54255 15.0849L15.085 7.54241M15.085 7.54241H9.42817M15.085 7.54241V13.1993"
                                            stroke="white" stroke-width="1.33333" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                </i>
                            </a>
                        </div>

                    </div>

                </div>
                <div class="property-slider-controls w-100 m-0 mt-5 mx-0 d-flex justify-content-between align-items-center"
                    style="max-width: 100%;">
                    <div class="property-slider__progress mb-0" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                        aria-valuenow="40">
                        <span class="property-slider__progress-fill" style="width: 40%;"></span>
                    </div>
                    <div class="property-slider__arrows ml-4 pl-4 flex-shrink-0">
                        <button type="button"
                            class="property-slider__arrow property-slider__arrow--prev bg-transparent border-0"
                            aria-label="Previous news">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M20 12H4M4 12L10 6M4 12L10 18"></path>
                            </svg>
                        </button>
                        <button type="button"
                            class="property-slider__arrow property-slider__arrow--next bg-transparent border-0 ml-2"
                            aria-label="Next news">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="#2A559C" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M4 12L20 12M20 12L14 18M20 12L14 6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="download-app d-flex align-items-center position-relative">
        <div class="container-app ">
            <div class="d-flex align-items-center position-relative justify-content-between">
                <div class="download-app-content text-center">
                    <h2 class="download-app__title">
                        Download the UAE’s most trusted property search app
                    </h2>
                    <p class="download-app__desc">
                        Install the Distinguish Real Estate app and start searching smarter.
                    </p>
                    <div class="download-app__stores d-flex flex-wrap justify-content-center align-items-center">
                        <a href="#" class="store-btn">
                            <img src="public/images/home/google-play.png" alt="Get it on Google Play" class="img-fluid">
                        </a>
                        <a href="#" class="store-btn">
                            <img src="public/images/home/appstore.png" alt="Download on the Apple Store"
                                class="img-fluid">
                        </a>
                    </div>
                </div>

                <div class="qr-code-wrapper">
                    <div class="download-app__qr text-center bg-white shadow-sm">
                        <img src="public/images/home/qr.png" alt="QR Code" class="img-fluid">
                        <p>Scan to download the app</p>
                    </div>
                </div>

                <div class="mobile-image-wrapper">
                    <div class="download-app__phone-wrap">
                        <img src="public/images/home/mobile.png" alt="Mobile App" class="download-app__phone">
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/modal-enquiry.php';
require __DIR__ . '/includes/floating-widgets.php';
require __DIR__ . '/includes/scripts.php';

