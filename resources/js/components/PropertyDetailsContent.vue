<template>
<div class="property-details-page-wrap property-details-page-inner">
    <p v-if="loading" class="container-ctn py-5 text-center text-muted">Loading property…</p>
    <p v-else-if="!property" class="container-ctn py-5 text-center">This property could not be found.</p>
    <template v-else>
<section class="banner banner--page banner--page--listing position-relative" aria-label="Page header">
        <div class="banner--page__bg">
            <picture>
                <img :src="heroBannerSrc" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">{{ property.title }}</h1>
                <ol class="breadcrumb-minimal" aria-label="Breadcrumb">
                    <li>
                        <a href="/" aria-label="Home">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li><RouterLink :to="{ name: 'our-property' }">Property</RouterLink></li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">{{ property.title }}</li>
                </ol>
            </div>
        </div>
    </section>

    <div class="property-detail-main">
        <!-- Single BEM block wraps viewport + toolbar so SCSS (.property-detail-gallery &__toolbar) applies and toolbar is not covered -->
        <div class="property-detail-gallery">
            <div class="container-fluid property-detail-gallery-fluid p-0 overflow-hidden">
                <div class="property-detail-gallery__viewport">
                    <div class="property-detail-gallery__slider js-property-detail-slider">
                        <div
                            v-for="(img, idx) in galleryImages"
                            :key="`g-${idx}-${img}`"
                            class="property-detail-gallery__slide"
                            :class="gallerySlideClass(idx)"
                        >
                            <img
                                :src="img"
                                :alt="galleryAlt(idx)"
                                width="960"
                                height="600"
                                loading="lazy"
                                @error="dreOnPropertyImgError"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-ctn">
        <div class="property-detail-gallery__toolbar">
                    <button type="button" class="property-detail-gallery__all-photos js-property-gallery-fancybox" aria-label="View all photos">
                        <span class="property-detail-gallery__all-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M7 5.667C7 4.95967 7.28099 4.28131 7.78115 3.78115C8.28131 3.28099 8.95967 3 9.667 3H18.333C18.6832 3 19.03 3.06898 19.3536 3.20301C19.6772 3.33704 19.9712 3.53349 20.2189 3.78115C20.4665 4.0288 20.663 4.32281 20.797 4.64638C20.931 4.96996 21 5.31676 21 5.667V14.333C21 14.6832 20.931 15.03 20.797 15.3536C20.663 15.6772 20.4665 15.9712 20.2189 16.2189C19.9712 16.4665 19.6772 16.663 19.3536 16.797C19.03 16.931 18.6832 17 18.333 17H9.667C8.95967 17 8.28131 16.719 7.78115 16.2189C7.28099 15.7187 7 15.0403 7 14.333V5.667Z" stroke="#2F3F58" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M4.012 7.26C3.705 7.43443 3.44965 7.68702 3.2719 7.99211C3.09415 8.2972 3.00034 8.64391 3 8.997V18.997C3 20.097 3.9 20.997 5 20.997H15C15.75 20.997 16.158 20.612 16.5 19.997M17 7H17.01" stroke="#2F3F58" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M7 12.9998L10.644 9.35578C10.7564 9.2433 10.8898 9.15407 11.0367 9.09319C11.1836 9.03231 11.341 9.00098 11.5 9.00098C11.659 9.00098 11.8164 9.03231 11.9633 9.09319C12.1102 9.15407 12.2436 9.2433 12.356 9.35578L16 12.9998" stroke="#2F3F58" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M15 11.9998L16.644 10.3558C16.7564 10.2433 16.8898 10.1541 17.0367 10.0932C17.1836 10.0323 17.341 10.001 17.5 10.001C17.659 10.001 17.8164 10.0323 17.9633 10.0932C18.1102 10.1541 18.2436 10.2433 18.356 10.3558L21 12.9998" stroke="#2F3F58" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                        </span>
                        <span class="property-detail-gallery__all-label">All Photos</span>
                    </button>
                    <div class="property-detail-gallery__nav">
                        <button type="button" class="property-detail-gallery__arrow property-detail-gallery__arrow--prev" data-gallery-prev aria-label="Previous photos">
                             <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none"> <path d="M7.2721 22.586C6.89715 22.9611 6.68652 23.4697 6.68652 24C6.68652 24.5304 6.89715 25.039 7.2721 25.414L18.5861 36.728C18.7706 36.9191 18.9913 37.0714 19.2353 37.1762C19.4793 37.2811 19.7417 37.3362 20.0073 37.3385C20.2729 37.3409 20.5362 37.2903 20.782 37.1897C21.0278 37.0891 21.2511 36.9406 21.4389 36.7528C21.6267 36.5651 21.7752 36.3417 21.8757 36.096C21.9763 35.8502 22.0269 35.5868 22.0246 35.3212C22.0223 35.0557 21.9671 34.7932 21.8623 34.5492C21.7575 34.3052 21.6051 34.0845 21.4141 33.9L13.5141 26H40.0001C40.5305 26 41.0392 25.7893 41.4143 25.4143C41.7894 25.0392 42.0001 24.5305 42.0001 24C42.0001 23.4696 41.7894 22.9609 41.4143 22.5858C41.0392 22.2108 40.5305 22 40.0001 22H13.5141L21.4141 14.1C21.7784 13.7228 21.98 13.2176 21.9754 12.6932C21.9709 12.1689 21.7605 11.6672 21.3897 11.2964C21.0189 10.9256 20.5173 10.7153 19.9929 10.7107C19.4685 10.7061 18.9633 10.9077 18.5861 11.272L7.2721 22.586Z" fill="#B1BCCD"/> </svg>
                        </button>
                        <button type="button" class="property-detail-gallery__arrow property-detail-gallery__arrow--next" data-gallery-next aria-label="Next photos">
                              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none"> <path d="M40.7279 25.414C41.1028 25.0389 41.3135 24.5303 41.3135 24C41.3135 23.4696 41.1028 22.961 40.7279 22.586L29.4139 11.272C29.2294 11.0809 29.0087 10.9286 28.7647 10.8238C28.5207 10.7189 28.2583 10.6638 27.9927 10.6615C27.7271 10.6591 27.4638 10.7098 27.218 10.8103C26.9722 10.9109 26.7489 11.0594 26.5611 11.2472C26.3733 11.435 26.2248 11.6583 26.1243 11.904C26.0237 12.1498 25.9731 12.4132 25.9754 12.6788C25.9777 12.9443 26.0329 13.2068 26.1377 13.4508C26.2425 13.6948 26.3949 13.9155 26.5859 14.1L34.4859 22L7.99991 22C7.46947 22 6.96076 22.2107 6.58569 22.5857C6.21062 22.9608 5.99991 23.4695 5.99991 24C5.99991 24.5304 6.21062 25.0391 6.58569 25.4142C6.96076 25.7892 7.46947 26 7.99991 26L34.4859 26L26.5859 33.9C26.2216 34.2772 26.02 34.7824 26.0246 35.3068C26.0291 35.8312 26.2395 36.3328 26.6103 36.7036C26.9811 37.0744 27.4827 37.2847 28.0071 37.2893C28.5315 37.2939 29.0367 37.0923 29.4139 36.728L40.7279 25.414Z" fill="#B1BCCD"/> </svg>
                        </button>
                    </div>
                </div>
  
        </div>
        </div>

        <div class="container-fluid property-detail-summary-outer p-0">
            <div class="property-detail-summary ">
                <div class="container-ctn">
                <div class="d-flex align-items-start property-detail-intro justify-content-between">
                    <div class="property-detail-intro__left">
                        <div class="property-detail-badges">
                            <span class="property-detail-badge property-detail-badge--apartment">{{ propertyTypeLabel }}</span>
                            <span class="property-detail-badge property-detail-badge--outline">{{ listingOutlineLabel }}</span>
                        </div>
                        <h2 class="property-detail-title">{{ property.title }}</h2>
                        <p class="property-detail-location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 2.00012C7.6 2.00012 4 5.60012 4 10.0001C4 15.4001 11 21.5001 11.3 21.8001C11.5 21.9001 11.8 22.0001 12 22.0001C12.2 22.0001 12.5 21.9001 12.7 21.8001C13 21.5001 20 15.4001 20 10.0001C20 5.60012 16.4 2.00012 12 2.00012ZM12 19.7001C9.9 17.7001 6 13.4001 6 10.0001C6 6.70012 8.7 4.00012 12 4.00012C15.3 4.00012 18 6.70012 18 10.0001C18 13.3001 14.1 17.7001 12 19.7001ZM12 6.00012C9.8 6.00012 8 7.80012 8 10.0001C8 12.2001 9.8 14.0001 12 14.0001C14.2 14.0001 16 12.2001 16 10.0001C16 7.80012 14.2 6.00012 12 6.00012ZM12 12.0001C10.9 12.0001 10 11.1001 10 10.0001C10 8.90012 10.9 8.00012 12 8.00012C13.1 8.00012 14 8.90012 14 10.0001C14 11.1001 13.1 12.0001 12 12.0001Z" fill="#2F3F58"/>
                        </svg>
                            <span>{{ property.location }}</span>
                        </p>
                        <div class="property-detail-stats property-detail-stats--divided property-detail-stats--stacked">
                            <div class="property-detail-stat property-detail-stat--stacked">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <path d="M3 26V19C3.00313 17.9401 3.42557 16.9245 4.17503 16.175C4.9245 15.4256 5.9401 15.0031 7 15H25C26.0599 15.0031 27.0755 15.4256 27.825 16.175C28.5744 16.9245 28.9969 17.9401 29 19V26M24 15H6V8.5C6.00198 7.83757 6.26601 7.20283 6.73442 6.73442C7.20283 6.26601 7.83757 6.00198 8.5 6H23.5C24.1624 6.00198 24.7972 6.26601 25.2656 6.73442C25.734 7.20283 25.998 7.83757 26 8.5V15H24Z" stroke="#2F3F58" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 26V25.5C3.00115 25.1025 3.15956 24.7217 3.44061 24.4406C3.72167 24.1596 4.10253 24.0012 4.5 24H27.5C27.8975 24.0012 28.2783 24.1596 28.5594 24.4406C28.8404 24.7217 28.9988 25.1025 29 25.5V26M7 15V14C7.00148 13.47 7.21267 12.9622 7.58743 12.5874C7.96218 12.2127 8.47002 12.0015 9 12H14C14.53 12.0015 15.0378 12.2127 15.4126 12.5874C15.7873 12.9622 15.9985 13.47 16 14M16 14V15M16 14C16.0015 13.47 16.2127 12.9622 16.5874 12.5874C16.9622 12.2127 17.47 12.0015 18 12H23C23.53 12.0015 24.0378 12.2127 24.4126 12.5874C24.7873 12.9622 24.9985 13.47 25 14V15" stroke="#2F3F58" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                <span>{{ property.bedrooms }} Bedroom{{ property.bedrooms === 1 ? '' : 's' }}</span>
                            </div>
                            <div class="property-detail-stat property-detail-stat--stacked">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <path d="M29 17.5H5V6.24996C4.99885 5.82629 5.08174 5.40661 5.24387 5.01519C5.40601 4.62377 5.64416 4.2684 5.94456 3.96965L5.96956 3.94465C6.44035 3.47454 7.04479 3.16125 7.70032 3.04757C8.35584 2.93389 9.03048 3.02538 9.63206 3.30952C9.06392 4.25414 8.82777 5.36154 8.96115 6.45576C9.09452 7.54997 9.58977 8.56822 10.3682 9.34871L11.0526 10.0331L9.79281 11.293L11.2069 12.7071L12.4668 11.4473L19.4473 4.4669L20.7071 3.20708L19.2929 1.7929L18.0331 3.05271L17.3486 2.36827C16.5292 1.5511 15.449 1.04737 14.2962 0.944854C13.1435 0.842339 11.9914 1.14754 11.0406 1.80727C10.0379 1.17409 8.84979 0.900744 7.67128 1.03213C6.49277 1.16352 5.39399 1.69183 4.55544 2.53027L4.53044 2.55527C4.0437 3.03932 3.65782 3.61511 3.39512 4.24931C3.13241 4.8835 2.99812 5.56351 3 6.24996V17.5H1V19.5H3V21.4187C3.00001 21.5799 3.02601 21.7401 3.077 21.893L4.9375 27.4743C5.0368 27.7731 5.22771 28.033 5.48314 28.2171C5.73856 28.4012 6.04551 28.5002 6.36037 28.5H7.16663L6.4375 31H8.52081L9.25 28.5H22.2563L23.0063 31H25.0938L24.3438 28.5H25.6394C25.9543 28.5003 26.2613 28.4013 26.5168 28.2172C26.7722 28.0331 26.9632 27.7731 27.0625 27.4743L28.9229 21.893C28.9739 21.7401 28.9999 21.5799 29 21.4187V19.5H31V17.5H29ZM11.7825 3.78246C12.3335 3.23266 13.0801 2.92389 13.8585 2.92389C14.6368 2.92389 15.3834 3.23266 15.9344 3.78246L16.6187 4.4669L12.4669 8.61871L11.7825 7.9344C11.2327 7.38338 10.924 6.6368 10.924 5.85843C10.924 5.08006 11.2327 4.33347 11.7825 3.78246ZM27 21.3375L25.2792 26.5H6.72075L5 21.3375V19.5H27V21.3375Z" fill="#2F3F58"/>
                            </svg>
                                <span>{{ property.bathrooms }} Bathroom{{ property.bathrooms === 1 ? '' : 's' }}</span>
                            </div>
                            <div class="property-detail-stat property-detail-stat--stacked">
                                <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" viewBox="0 0 33 33" fill="none">
                                <path d="M4.016 0C1.81184 0 0 1.81152 0 4.016C0 5.94144 1.38144 7.56672 3.2 7.94752V24.0845C1.38144 24.4653 0 26.0909 0 28.0163C0 30.2208 1.81152 32.032 4.016 32.032C5.95104 32.032 7.5648 30.6323 7.93312 28.8H24.0787C24.4474 30.6342 26.0797 32.032 28.0163 32.032C30.1443 32.032 31.8899 30.3398 32.0093 28.2394C32.0242 28.166 32.0317 28.0912 32.0317 28.0163C32.0317 27.9414 32.0242 27.8667 32.0093 27.7933C31.905 25.9574 30.5584 24.4339 28.8 24.0787V7.95328C30.5584 7.59808 31.905 6.07488 32.0096 4.23936C32.0245 4.16595 32.032 4.09123 32.032 4.01632C32.032 3.94141 32.0245 3.86669 32.0096 3.79328C31.8896 1.6928 30.1443 0 28.016 0C26.0909 0 24.4653 1.38144 24.0845 3.2H7.9264C7.5456 1.38368 5.9392 0 4.016 0ZM4.016 2.24C5.01024 2.24 5.792 3.02208 5.792 4.016C5.792 5.01024 5.01024 5.792 4.016 5.792C3.02176 5.792 2.24 5.01024 2.24 4.016C2.24 3.02176 3.02208 2.24 4.016 2.24ZM28.016 2.24C29.0102 2.24 29.792 3.02208 29.792 4.016C29.792 5.01024 29.0102 5.792 28.016 5.792C27.0218 5.792 26.24 5.01024 26.24 4.016C26.24 3.02176 27.0221 2.24 28.016 2.24ZM7.74976 5.44H24.2682C24.4699 5.96272 24.7776 6.43801 25.1721 6.83592C25.5665 7.23383 26.0391 7.54574 26.56 7.752V24.28C26.0443 24.4841 25.5759 24.7917 25.1838 25.1838C24.7917 25.5759 24.4841 26.0443 24.28 26.56H7.736C7.53219 26.0402 7.22298 25.5682 6.82783 25.1738C6.43269 24.7794 5.96016 24.471 5.44 24.2682V7.76384C5.96574 7.559 6.4428 7.24643 6.84054 6.84621C7.23827 6.446 7.54787 5.967 7.74944 5.44M4.016 26.24C5.00992 26.24 5.79168 27.0221 5.79168 28.016C5.79168 29.0102 5.00992 29.792 4.01568 29.792C3.02144 29.792 2.24 29.0102 2.24 28.016C2.24 27.0218 3.02208 26.24 4.016 26.24ZM28.016 26.24C29.0099 26.24 29.7917 27.0221 29.7917 28.016C29.7917 29.0102 29.0099 29.792 28.0157 29.792C27.0214 29.792 26.24 29.0102 26.24 28.016C26.24 27.0218 27.0221 26.24 28.016 26.24Z" fill="#2F3F58"/>
                                </svg>
                                <span>{{ formatSqft(property.sqft) }}ft²</span>
                            </div>
                        </div>
                        <div class="property-detail-price-wrap">
                            <p class="property-detail-price">{{ formatPrice(property.price) }} {{ priceCurrencySuffix }}</p>
                        </div>
                    </div>
                        <aside class="property-detail-aside">
                            <div class="property-detail-agent">
                                <div class="property-detail-agent__header">
                                    <div class="property-detail-agent__profile">
                                        <div class="property-detail-agent__avatar">
                                            <img :src="agentAvatar" alt="" width="64" height="64" loading="lazy" @error="dreOnAgentImgError">
                                        </div>
                                        <div class="property-detail-agent__intro">
                                            <p class="property-detail-agent__name">{{ agentName }}</p>
                                            <p class="property-detail-agent__role">{{ agentRole }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="property-detail-agent__body">
                                    <div v-if="agentExperience || agentLanguages" class="property-detail-agent__meta">
                                        <p v-if="agentExperience">
                                            <strong>Experience :</strong>
                                            <span>{{ agentExperience }}</span>
                                        </p>
                                        <p v-if="agentLanguages">
                                            <strong>Languages :</strong>
                                            <span>{{ agentLanguages }}</span>
                                        </p>
                                    </div>
                                    <div class="property-detail-agent__actions">
                                        <div class="property-detail-agent__actions-row">
                                            <button type="button" class="property-detail-btn property-detail-btn--primary property-detail-btn--row" data-bs-toggle="modal" data-bs-target="#siteEnquiryForm">Enquiry</button>
                                            <a class="property-detail-btn property-detail-btn--soft property-detail-btn--row" :href="callHref">Call Now</a>
                                            <a class="property-detail-btn property-detail-btn--outline property-detail-btn--row" :href="whatsappHref" target="_blank" rel="noopener noreferrer">Whatsapp</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                  
                </div>
                </div>
              
            </div>
        </div>

        <div
            class="container-ctn property-detail-body justify-content-between"
            :class="{ 'property-detail-body--no-map': !showMapAside }"
        >
            <div class="property-detail-body__main _left_col">
                <section v-if="descriptionHtml" class="property-detail-section" aria-labelledby="pd-desc-heading">
                    <h3 id="pd-desc-heading" class="property-detail-section__title">Description</h3>
                    <div class="property-detail-section__text">
                        <div class="property-detail-description-html" v-html="descriptionHtml"></div>
                    </div>
                </section>

                <section v-if="accessPlaces.length" class="property-detail-section" aria-labelledby="pd-access-heading">
                    <h3 id="pd-access-heading" class="property-detail-section__title">Easy Access To</h3>
                    <div class="property-detail-access">
                        <div v-for="place in accessPlaces" :key="place.id" class="property-detail-access__item">
                            <span class="property-detail-access__icon" aria-hidden="true">
                                <img v-if="place.icon" :src="place.icon" alt="" width="22" height="22" loading="lazy" @error="dreOnPropertyImgError">
                                <img v-else :src="asset('public/images/properties-details/icons/solar_city-linear.png')" alt="" width="22" height="22" loading="lazy">
                            </span>
                            <span>{{ place.name }}<template v-if="place.distance"> — {{ place.distance }}</template></span>
                        </div>
                    </div>
                </section>

                <section v-if="specCells.length" class="property-detail-section" aria-labelledby="pd-specs-heading">
                    <h3 id="pd-specs-heading" class="property-detail-section__title">Property Details</h3>
                    <div class="property-detail-specs property-detail-specs--cards property-detail-specs--detail-cards">
                        <div v-for="cell in specCells" :key="cell.id" class="property-detail-specs__cell">
                            <span class="property-detail-specs__icon" aria-hidden="true"><img :src="cell.icon" alt="" width="24" height="24" loading="lazy"></span>
                            <p class="property-detail-specs__label">{{ cell.label }}</p>
                            <p class="property-detail-specs__value">{{ cell.value }}</p>
                        </div>
                    </div>
                </section>

                <section v-if="featureList.length || amenityCards.length || propertyAttributeCards.length" class="property-detail-section" aria-labelledby="pd-features-heading">
                    <h3 id="pd-features-heading" class="property-detail-section__title">Key Features</h3>
                    <div v-if="featureList.length" class="property-detail-section__text">
                        <ul class="mb-0 ps-3">
                            <li v-for="(line, fi) in featureList" :key="'f-' + fi">{{ line }}</li>
                        </ul>
                    </div>
                    <template v-if="amenityCards.length">
                        <h4 class="property-detail-section__subtitle">Amenities</h4>
                        <div class="property-detail-icon-grid">
                            <div v-for="am in amenityCards" :key="am.id" class="property-detail-icon-grid__item">
                                <span class="property-detail-icon-grid__icon" aria-hidden="true">
                                    <img
                                        v-if="am.icon"
                                        :src="am.icon"
                                        alt=""
                                        width="26"
                                        height="26"
                                        loading="lazy"
                                        @error="dreOnPropertyImgError"
                                    >
                                    <img
                                        v-else
                                        :src="asset('public/images/properties-details/icons/solar_swimming-outline.png')"
                                        alt=""
                                        width="26"
                                        height="26"
                                        loading="lazy"
                                    >
                                </span>
                                <span>{{ am.label }}</span>
                            </div>
                        </div>
                    </template>
                    <template v-if="propertyAttributeCards.length">
                        <h4 class="property-detail-section__subtitle">Property Attributes</h4>
                        <div class="property-detail-icon-grid property-detail-icon-grid--cols3">
                            <div v-for="attr in propertyAttributeCards" :key="attr.id" class="property-detail-icon-grid__item">
                                <span class="property-detail-icon-grid__icon" aria-hidden="true">
                                    <img
                                        v-if="attr.icon"
                                        :src="attr.icon"
                                        alt=""
                                        width="26"
                                        height="26"
                                        loading="lazy"
                                        @error="dreOnPropertyImgError"
                                    >
                                    <img
                                        v-else
                                        :src="asset('public/images/properties-details/icons/hugeicons_location-star-01.png')"
                                        alt=""
                                        width="26"
                                        height="26"
                                        loading="lazy"
                                    >
                                </span>
                                <span>{{ attr.label }}</span>
                            </div>
                        </div>
                    </template>
                </section>

                <div v-if="showMortgageCalculator" class="property-detail-calc-outer">
                    <section class="property-detail-section property-detail-section--flush" aria-labelledby="pd-calc-heading">
                        <div class="property-detail-calc js-payment-calc">
                            <p id="pd-calc-heading" class="property-detail-calc__heading">Payment Calculator</p>
                            <div class="property-detail-calc__layout">
                                <div class="property-detail-calc__donut-wrap">
                                    <div class="property-detail-calc__donut js-calc-donut" role="img" aria-label="Payment breakdown chart">
                                        <div class="property-detail-calc__donut-label">
                                            <p class="property-detail-calc__donut-value js-calc-monthly">—</p>
                                            <p class="property-detail-calc__donut-sub">Per Month</p>
                                        </div>
                                    </div>
                                    <ul class="property-detail-calc__legend">
                                        <li>
                                            <span class="property-detail-calc__legend-label"><span class="property-detail-calc__dot property-detail-calc__dot--pi" aria-hidden="true"></span> Principal and Interest</span>
                                        </li>
                                        <li>
                                            <span class="property-detail-calc__legend-label"><span class="property-detail-calc__dot property-detail-calc__dot--tax" aria-hidden="true"></span> Property Tax</span>
                                        </li>
                                        <li>
                                            <span class="property-detail-calc__legend-label"><span class="property-detail-calc__dot property-detail-calc__dot--hoa" aria-hidden="true"></span> HOA Fees</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="property-detail-calc__panel">
                                    <div class="property-detail-calc__fields property-detail-calc__fields--grid">
                                        <div class="property-detail-calc__field">
                                            <label for="calc_price">Home Price</label>
                                            <input id="calc_price" name="calc_price" type="number" inputmode="decimal" min="0" step="100" :value="calcHomePrice">
                                        </div>
                                        <div class="property-detail-calc__field property-detail-calc__field--down">
                                            <label for="calc_down_pct">Down Payment</label>
                                            <div class="property-detail-calc__down-row">
                                                <input id="calc_down_amount" name="calc_down_amount" type="number" inputmode="decimal" min="0" step="100" value="9000" class="js-calc-down-amount" aria-describedby="calc_down_pct-hint">
                                                <span class="property-detail-calc__down-sep" aria-hidden="true"></span>
                                                <div class="property-detail-calc__pct-wrap">
                                                    <input id="calc_down_pct" name="calc_down_pct" type="number" inputmode="decimal" min="0" max="100" step="0.01" value="20.00" class="js-calc-down-pct">
                                                    <span class="property-detail-calc__pct-suffix">%</span>
                                                </div>
                                            </div>
                                            <span id="calc_down_pct-hint" class="visually-hidden">Amount and percentage stay in sync with home price.</span>
                                        </div>
                                        <div class="property-detail-calc__field property-detail-calc__field--readonly">
                                            <span class="property-detail-calc__readonly-label">Principal and Interest</span>
                                            <p class="property-detail-calc__readonly-value js-calc-pi-monthly">—</p>
                                        </div>
                                        <div class="property-detail-calc__field">
                                            <label for="calc_tax">Property Tax</label>
                                            <input id="calc_tax" name="calc_tax" type="number" inputmode="decimal" min="0" step="100" value="0">
                                        </div>
                                        <div class="property-detail-calc__field">
                                            <label for="calc_hoa">HOA Fees</label>
                                            <input id="calc_hoa" name="calc_hoa" type="number" inputmode="decimal" min="0" step="100" value="0">
                                        </div>
                                        <div class="property-detail-calc__field">
                                            <label for="calc_years">Term <span class="property-detail-calc__hint">(*in years)</span></label>
                                            <input id="calc_years" name="calc_years" type="number" inputmode="numeric" min="1" max="50" value="30">
                                        </div>
                                        <div class="property-detail-calc__field">
                                            <label for="calc_rate">Interest</label>
                                            <input id="calc_rate" name="calc_rate" type="number" inputmode="decimal" min="0" step="0.001" value="4.125">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <aside v-if="showMapAside" class="property-detail-body__map" aria-label="Location map">
                <PropertyDetailMap
                    v-if="hasMapCenter"
                    :lat="Number(property.latitude)"
                    :lng="Number(property.longitude)"
                    :title="property.title"
                    :price-display="mapPriceDisplay"
                    :beds="Number(property.bedrooms) || 0"
                    :baths="Number(property.bathrooms) || 0"
                    :sqft="Number(property.sqft) || 0"
                    :thumb="mapThumb"
                    :nearby-places="property.nearby_places || []"
                    :external-map-url="googleMapsUrl"
                />
                <div v-else class="property-detail-map-fallback">
                    <p class="property-detail-map-fallback__title">Location</p>
                    <p v-if="property.location" class="property-detail-map-fallback__text">{{ property.location }}</p>
                </div>
            </aside>
        </div>

        <section v-if="similar.length" class="property-detail-similar commonPadding-120 pb-0" aria-labelledby="similar-listings-heading">
            <div class="container-ctn">
                <h2 id="similar-listings-heading" class="property-detail-similar__head">Similar Listing</h2>
                <div class="property-detail-similar__slider-outer">
                    <div class="js-property-detail-similar-slider property-detail-similar__slider">
                        <div v-for="p in similar" :key="p.id" class="property-detail-similar__slide">
                            <article class="property-card property-card--listing" :data-property-url="similarCardDataPropertyUrl(p)">
                                <div class="property-card__ghost" aria-hidden="true"></div>
                                <div class="property-card__inner">
                                    <div class="property-card__media">
                                        <span v-if="p.is_featured" class="property-card__badge">Featured</span>
                                        <picture>
                                            <img
                                                :src="similarImage(p)"
                                                :alt="p.title"
                                                width="427"
                                                height="260"
                                                loading="lazy"
                                                @error="dreOnPropertyImgError"
                                            >
                                        </picture>
                                        <a
                                            v-if="p.virtual_tour_url"
                                            class="property-card__360"
                                            :href="p.virtual_tour_url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            title="360 virtual tour"
                                        >360</a>
                                    </div>
                                    <div class="property-card__body">
                                        <h3 class="property-card__title">
                                            <a class="text-decoration-none text-reset" :href="similarUrl(p)">{{ p.title }}</a>
                                        </h3>
                                        <p class="property-card__location">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z" fill="#2F3F58" />
                                            </svg>
                                            <span>{{ p.location }}</span>
                                        </p>
                                        <div class="property-card__meta property-card__meta--similar">
                                            <div class="property-card__price-row">
                                                <div class="property-card__price">{{ formatPrice(p.price) }} {{ priceSuffixForItem(p) }}</div>
                                            </div>
                                            <div class="property-card__tags">
                                                <span class="property-tag property-tag--fill">{{ typeLabel(p.property_type) }}</span>
                                                <span class="property-tag property-tag--outline">{{ listingLabel(p.listing_type) }}</span>
                                            </div>
                                        </div>
                                        <div class="property-card__photos property-card__photos--similar-row">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M13.997 4C14.3578 3.99999 14.7119 4.09759 15.0217 4.28244C15.3316 4.46729 15.5856 4.73251 15.757 5.05L16.243 5.95C16.4144 6.26749 16.6684 6.53271 16.9783 6.71756C17.2881 6.90241 17.6422 7.00001 18.003 7H20C20.5304 7 21.0391 7.21071 21.4142 7.58579C21.7893 7.96086 22 8.46957 22 9V18C22 18.5304 21.7893 19.0391 21.4142 19.4142C21.0391 19.7893 20.5304 20 20 20H4C3.46957 20 2.96086 19.7893 2.58579 19.4142C2.21071 19.0391 2 18.5304 2 18V9C2 8.46957 2.21071 7.96086 2.58579 7.58579C2.96086 7.21071 3.46957 7 4 7H5.997C6.35742 7.00002 6.71115 6.90264 7.02078 6.71817C7.33041 6.53369 7.58444 6.26897 7.756 5.952L8.245 5.048C8.41656 4.73103 8.67059 4.46631 8.98022 4.28183C9.28985 4.09736 9.64358 3.99998 10.004 4H13.997Z" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            {{ similarPhotoCount(p) }} Photos
                                        </div>
                                        <div class="property-details">
                                            <div class="property-details__item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M2.25 19.5V14.25C2.25235 13.4551 2.56917 12.6934 3.13128 12.1313C3.69338 11.5692 4.45507 11.2523 5.25 11.25H18.75C19.5449 11.2523 20.3066 11.5692 20.8687 12.1313C21.4308 12.6934 21.7477 13.4551 21.75 14.25V19.5M18 11.25H4.5V6.375C4.50148 5.87818 4.6995 5.40212 5.05081 5.05081C5.40212 4.6995 5.87818 4.50148 6.375 4.5H17.625C18.1218 4.50148 18.5979 4.6995 18.9492 5.05081C19.3005 5.40212 19.4985 5.87818 19.5 6.375V11.25H18Z" stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M2.25 19.5V19.125C2.25087 18.8269 2.36967 18.5413 2.58046 18.3305C2.79125 18.1197 3.0769 18.0009 3.375 18H20.625C20.9231 18.0009 21.2087 18.1197 21.4195 18.3305C21.6303 18.5413 21.7491 18.8269 21.75 19.125V19.5M5.25 11.25V10.5C5.25111 10.1025 5.40951 9.72163 5.69057 9.44057C5.97163 9.15951 6.35252 9.00111 6.75 9H10.5C10.8975 9.00111 11.2784 9.15951 11.5594 9.44057C11.8405 9.72163 11.9989 10.1025 12 10.5M12 10.5V11.25M12 10.5C12.0011 10.1025 12.1595 9.72163 12.4406 9.44057C12.7216 9.15951 13.1025 9.00111 13.5 9H17.25C17.6475 9.00111 18.0284 9.15951 18.3094 9.44057C18.5905 9.72163 18.7489 10.1025 18.75 10.5V11.25" stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <span>{{ p.bedrooms }} Bedroom{{ p.bedrooms === 1 ? '' : 's' }}</span>
                                            </div>
                                            <div class="property-details__item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M21.75 13.125H3.75V4.6875C3.74914 4.36975 3.8113 4.05498 3.9329 3.76142C4.0545 3.46786 4.23312 3.20133 4.45842 2.97726L4.47717 2.95852C4.83026 2.60594 5.28359 2.37097 5.77524 2.28571C6.26688 2.20045 6.77286 2.26906 7.22405 2.48217C6.79794 3.19064 6.62083 4.02119 6.72086 4.84185C6.82089 5.66251 7.19233 6.4262 7.77614 7.01156L8.28947 7.52489L7.34461 8.4698L8.4052 9.53039L9.35006 8.58553L14.5855 3.3502L15.5303 2.40534L14.4697 1.3447L13.5248 2.28956L13.0115 1.77623C12.3969 1.16336 11.5867 0.785558 10.7222 0.708671C9.85764 0.631784 8.99353 0.860685 8.28042 1.35548C7.52846 0.8806 6.63734 0.675589 5.75346 0.774129C4.86958 0.87267 4.04549 1.2689 3.41658 1.89773L3.39783 1.91648C3.03278 2.27952 2.74336 2.71136 2.54634 3.18701C2.34931 3.66266 2.24859 4.17266 2.25 4.6875V13.125H0.75V14.625H2.25V16.0641C2.25001 16.185 2.26951 16.3051 2.30775 16.4198L3.70312 20.6057C3.7776 20.8298 3.92078 21.0248 4.11235 21.1629C4.30392 21.301 4.53413 21.3752 4.77028 21.375H5.37497L4.82812 23.25H6.39061L6.9375 21.375H16.6922L17.2547 23.25H18.8203L18.2578 21.375H19.2295C19.4657 21.3752 19.696 21.301 19.8876 21.1629C20.0792 21.0248 20.2224 20.8299 20.2969 20.6057L21.6922 16.4198C21.7304 16.3051 21.75 16.185 21.75 16.0641V14.625H23.25V13.125H21.75ZM8.83688 2.83687C9.25012 2.42453 9.81007 2.19295 10.3939 2.19295C10.9776 2.19295 11.5376 2.42453 11.9508 2.83687L12.4641 3.3502L9.3502 6.46406L8.83688 5.95083C8.42455 5.53757 8.19298 4.97763 8.19298 4.39385C8.19298 3.81007 8.42455 3.25013 8.83688 2.83687ZM20.25 16.0031L18.9594 19.875H5.04056L3.75 16.0031V14.625H20.25V16.0031Z" fill="#2F3F58" />
                                                </svg>
                                                <span>{{ p.bathrooms }} Bathroom{{ p.bathrooms === 1 ? '' : 's' }}</span>
                                            </div>
                                            <div class="property-details__item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none" aria-hidden="true">
                                                    <path d="M3.012 0C1.35888 0 0 1.35864 0 3.012C0 4.45608 1.03608 5.67504 2.4 5.96064V18.0634C1.03608 18.349 0 19.5682 0 21.0122C0 22.6656 1.35864 24.024 3.012 24.024C4.46328 24.024 5.6736 22.9742 5.94984 21.6H18.059C18.3355 22.9757 19.5598 24.024 21.0122 24.024C22.6082 24.024 23.9174 22.7549 24.007 21.1795C24.0181 21.1245 24.0238 21.0684 24.0238 21.0122C24.0238 20.9561 24.0181 20.9 24.007 20.845C23.9287 19.4681 22.9188 18.3254 21.6 18.059V5.96496C22.9188 5.69856 23.9287 4.55616 24.0072 3.17952C24.0184 3.12446 24.024 3.06842 24.024 3.01224C24.024 2.95606 24.0184 2.90002 24.0072 2.84496C23.9172 1.2696 22.6082 0 21.012 0C19.5682 0 18.349 1.03608 18.0634 2.4H5.9448C5.6592 1.03776 4.4544 0 3.012 0ZM3.012 1.68C3.75768 1.68 4.344 2.26656 4.344 3.012C4.344 3.75768 3.75768 4.344 3.012 4.344C2.26632 4.344 1.68 3.75768 1.68 3.012C1.68 2.26632 2.26656 1.68 3.012 1.68ZM21.012 1.68C21.7577 1.68 22.344 2.26656 22.344 3.012C22.344 3.75768 21.7577 4.344 21.012 4.344C20.2663 4.344 19.68 3.75768 19.68 3.012C19.68 2.26632 20.2666 1.68 21.012 1.68ZM5.81232 4.08H18.2011C18.3524 4.47204 18.5832 4.82851 18.879 5.12694C19.1749 5.42537 19.5293 5.65931 19.92 5.814V18.21C19.5332 18.363 19.182 18.5937 18.8878 18.8878C18.5937 19.182 18.363 19.5332 18.21 19.92H5.802C5.64914 19.5302 5.41724 19.1762 5.12088 18.8804C4.82451 18.5845 4.47012 18.3533 4.08 18.2011V5.82288C4.4743 5.66925 4.8321 5.43482 5.1304 5.13466C5.4287 4.8345 5.6609 4.47525 5.81208 4.08M3.012 19.68C3.75744 19.68 4.34376 20.2666 4.34376 21.012C4.34376 21.7577 3.75744 22.344 3.01176 22.344C2.26608 22.344 1.68 21.7577 1.68 21.012C1.68 20.2663 2.26656 19.68 3.012 19.68ZM21.012 19.68C21.7574 19.68 22.3438 20.2666 22.3438 21.012C22.3438 21.7577 21.7574 22.344 21.0118 22.344C20.2661 22.344 19.68 21.7577 19.68 21.012C19.68 20.2663 20.2666 19.68 21.012 19.68Z" fill="#2F3F58" />
                                                </svg>
                                                <span>{{ formatSqft(p.sqft) }} ft²</span>
                                            </div>
                                        </div>
                                        <div class="property-card__footer property-card__footer--actions-only">
                                            <div class="property-card__actions">
                                                <a class="property-btn property-btn--primary" href="#" data-bs-toggle="modal" data-bs-target="#siteEnquiryForm">Enquiry</a>
                                                <a class="property-btn property-btn--outline" :href="p.phone">Call Now</a>
                                                <a class="property-btn property-btn--outline" :href="p.whatsapp" target="_blank" rel="noopener noreferrer">Whatsapp</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                    <!-- <div class="property-slider-controls property-detail-similar__controls">
                        <div class="property-slider__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="25">
                            <span class="property-slider__progress-fill"></span>
                        </div>
                        <div class="property-slider__arrows">
                            <button type="button" class="property-slider__arrow property-slider__arrow--prev" aria-label="Previous similar listings">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M20 12H4M4 12L10 6M4 12L10 18" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <button type="button" class="property-slider__arrow property-slider__arrow--next" aria-label="Next similar listings">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 12L20 12M20 12L14 18M20 12L14 6" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    </div> -->
                </div>
            </div>
        </section>

    </div>
    </template>
</div>
</template>

<script setup>
import PropertyDetailMap from '@/components/properties/PropertyDetailMap.vue';
import { asset } from '@/utils/asset';
import {
    DRE_AGENT_PLACEHOLDER_IMAGE,
    dreNormalizePropertyImages,
    dreOnAgentImgError,
    dreOnPropertyImgError,
    drePickImageUrl,
    DRE_PROPERTY_PLACEHOLDER_IMAGE,
} from '@/utils/propertyImages';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';

const route = useRoute();
const { locale } = useI18n({ useScope: 'global' });

const loading = ref(true);
const property = ref(null);
const similar = ref([]);

const galleryImages = computed(() => {
    const list = dreNormalizePropertyImages(property.value?.images);
    // Drop duplicate URLs (CMS sometimes repeats the same image) so the slider does not show clones.
    return [...new Set(list)];
});

const heroBannerSrc = computed(() => asset('public/images/inner-banner.jpg'));

const priceCurrencySuffix = computed(() => {
    const c = String(property.value?.currency || 'AED').toUpperCase();
    if (c === 'AED') return 'د.إ';
    return c;
});

function priceSuffixForItem(item) {
    const c = String(item?.currency || 'AED').toUpperCase();
    if (c === 'AED') return 'د.إ';
    return c;
}

const SLIDE_MODS = [
    'property-detail-gallery__slide--portrait',
    'property-detail-gallery__slide--wide',
    'property-detail-gallery__slide--landscape',
    'property-detail-gallery__slide--wide',
];

function gallerySlideClass(i) {
    return SLIDE_MODS[i % SLIDE_MODS.length];
}

function galleryAlt(i) {
    const t = property.value?.title || 'Property';

    return `${t} — photo ${i + 1}`;
}

const propertyTypeLabel = computed(() => {
    const raw = String(property.value?.property_type || '').replace(/[_-]+/g, ' ').trim();

    return raw ? raw.replace(/\b\w/g, (c) => c.toUpperCase()) : 'Property';
});

const listingOutlineLabel = computed(() => {
    const t = String(property.value?.listing_type || '').trim().toLowerCase();

    if (t === 'sale') return 'For Sale';

    return 'Rentals';
});

const agentName = computed(() => property.value?.agent?.name || 'DRE Team');
const agentRole = computed(() => property.value?.agent?.designation || '');
const agentExperience = computed(() => property.value?.agent?.experience || '');
const agentLanguages = computed(() => property.value?.agent?.languages || '');
const agentAvatar = computed(
    () => property.value?.agent?.image || DRE_AGENT_PLACEHOLDER_IMAGE,
);

const callHref = computed(() => property.value?.agent?.phone || property.value?.phone || '#');
const whatsappHref = computed(() => property.value?.whatsapp || '#');

function formatNumber(num) {
    const value = Number(num || 0);

    return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-AE' : 'en-AE').format(value);
}

function formatPrice(num) {
    return formatNumber(num);
}

function formatSqft(sqft) {
    return formatNumber(sqft);
}

const descriptionHtml = computed(() => {
    const raw = property.value?.description;

    if (raw == null || String(raw).trim() === '') return '';

    const s = String(raw);

    return s.includes('<') ? s : `<p>${escapeHtml(s).replace(/\n/g, '<br>')}</p>`;
});

function escapeHtml(s) {
    return s
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function specCellDash(value) {
    const s = value == null ? '' : String(value).trim();

    return s !== '' ? s : '—';
}

function formatYearBuiltDisplay(y) {
    if (y == null || y === '') return '—';
    const n = Number(y);
    if (Number.isFinite(n) && n > 0) {
        return String(Math.round(n));
    }
    const str = String(y).trim();
    const m = str.match(/^(\d{4})/);

    return m ? m[1] : specCellDash(str);
}

function formatSecurityDepositDisplay(p) {
    const raw = p?.security_deposit;
    const num = Number(raw);
    if (!Number.isFinite(num) || num <= 0) {
        return '—';
    }
    const cur = String(p.currency || 'AED').toUpperCase();
    const curWord = cur === 'AED' ? 'AED' : cur;
    const n = formatNumber(num);
    const isRent = String(p.listing_type || '').trim().toLowerCase() === 'rent';

    return isRent ? `Minimum ${curWord} ${n}` : `${curWord} ${n}`;
}

/** Fixed seven-card grid (design): Property ID, Price, Size, Bathroom, Year built, Direct from owner, Security deposit. */
const specCells = computed(() => {
    const p = property.value;
    if (!p) return [];

    const icons = {
        id: asset('public/images/properties-details/icons/solar_tag-price-linear.png'),
        price: asset('public/images/properties-details/icons/solar_hand-money-outline.png'),
        size: asset('public/images/properties-details/icons/gis_square-pt.png'),
        bath: asset('public/images/properties-details/icons/cil_bathroom.png'),
        year: asset('public/images/properties-details/icons/mynaui_calendar.png'),
        owner: asset('public/images/properties-details/icons/fluent_building-32-regular.png'),
        deposit: asset('public/images/properties-details/icons/ph_hand-deposit.png'),
    };

    const cur = String(p.currency || 'AED').toUpperCase();
    const curSuff = cur === 'AED' ? 'د.إ' : cur;
    const priceVal =
        Number(p.price) > 0 ? `${formatPrice(p.price)} ${curSuff}` : '—';
    const sizeVal = Number(p.sqft) > 0 ? `${formatSqft(p.sqft)} ft²` : '—';

    return [
        { id: 'spec-id', label: 'Property ID', value: String(p.id), icon: icons.id },
        { id: 'spec-price', label: 'Price', value: priceVal, icon: icons.price },
        { id: 'spec-size', label: 'Property Size', value: sizeVal, icon: icons.size },
        {
            id: 'spec-bath',
            label: 'Bathroom',
            value: specCellDash(Number.isFinite(Number(p.bathrooms)) ? String(p.bathrooms) : ''),
            icon: icons.bath,
        },
        {
            id: 'spec-year',
            label: 'Year Built',
            value: formatYearBuiltDisplay(p.year_built),
            icon: icons.year,
        },
        {
            id: 'spec-owner',
            label: 'Direct from Owner',
            value: specCellDash(p.direct_from_owner),
            icon: icons.owner,
        },
        {
            id: 'spec-deposit',
            label: 'Security Deposit',
            value: formatSecurityDepositDisplay(p),
            icon: icons.deposit,
        },
    ];
});

const amenityCards = computed(() => {
    const rows = Array.isArray(property.value?.amenities) ? property.value.amenities : [];

    return rows
        .map((item, index) => {
            if (typeof item === 'string') {
                return { id: `${item}-${index}`, label: item, icon: null };
            }

            const label = item?.label || item?.title || item?.name || item?.value || '';

            return {
                id: item?.label || item?.title || `amenity-${index}`,
                label: label || 'Amenity',
                icon: typeof item?.icon === 'string' && item.icon.trim() !== '' ? item.icon : null,
            };
        })
        .filter((item) => item.label);
});

const featureList = computed(() => {
    const rows = Array.isArray(property.value?.features) ? property.value.features : [];

    return rows
        .map((item) => {
            if (typeof item === 'string') {
                return item.trim();
            }

            return String(
                item?.text
                ?? item?.label
                ?? item?.title
                ?? item?.value
                ?? item?.description
                ?? '',
            ).trim();
        })
        .filter(Boolean);
});

/** CMS “Property attributes” rows from API `details_grid` (localized icon + label). */
const propertyAttributeCards = computed(() => {
    const rows = Array.isArray(property.value?.details_grid) ? property.value.details_grid : [];

    return rows
        .map((item, index) => {
            const label =
                typeof item === 'string'
                    ? item.trim()
                    : String(item?.label ?? item?.name ?? item?.title ?? item?.value ?? '').trim();
            if (!label) {
                return null;
            }
            const icon = typeof item?.icon === 'string' && item.icon.trim() !== '' ? item.icon : null;

            return {
                id: `attr-${index}-${label}`,
                label,
                icon,
            };
        })
        .filter(Boolean);
});

const accessPlaces = computed(() => {
    const rows = Array.isArray(property.value?.easy_access) ? property.value.easy_access : [];

    return rows
        .map((item, index) => {
            const dist = item?.distance;
            const distanceLabel = dist != null && String(dist).trim() !== '' ? String(dist) : '';
            const name = String(item?.name || '').trim();

            return {
                id: `${name || 'place'}-${index}`,
                name: name || '',
                distance: distanceLabel,
                icon: item?.icon || null,
            };
        })
        .filter((row) => row.name !== '' && row.name !== 'Nearby location');
});

const hasMapCenter = computed(() => {
    const lat = Number(property.value?.latitude);
    const lng = Number(property.value?.longitude);

    return Number.isFinite(lat) && Number.isFinite(lng) && (Math.abs(lat) > 1e-6 || Math.abs(lng) > 1e-6);
});

const googleMapsUrl = computed(() => {
    if (!property.value) return '#';

    const lat = Number(property.value.latitude);
    const lng = Number(property.value.longitude);

    if (Number.isFinite(lat) && Number.isFinite(lng) && lat !== 0 && lng !== 0) {
        return `https://www.google.com/maps?q=${lat},${lng}`;
    }

    return property.value.location
        ? `https://www.google.com/maps/search/${encodeURIComponent(property.value.location)}`
        : '#';
});

const mapThumb = computed(() => galleryImages.value[0] || DRE_PROPERTY_PLACEHOLDER_IMAGE);

const mapPriceDisplay = computed(() => {
    if (!property.value) return '';

    return `${formatPrice(property.value.price)} ${property.value.currency || 'AED'}`;
});

const calcHomePrice = computed(() => Math.max(0, Math.round(Number(property.value?.price) || 0)));

const showMortgageCalculator = computed(() => calcHomePrice.value > 0);

const showMapAside = computed(() => {
    if (!property.value) return false;
    if (hasMapCenter.value) return true;
    return googleMapsUrl.value !== '#';
});

function similarUrl(item) {
    if (typeof item?.url === 'string' && item.url.trim() !== '') return item.url;
    if (item?.slug) return `/property-details/${item.slug}`;
    if (item?.id) return `/property-details/${item.id}`;

    return '#';
}

/** Global script.js uses this for whole-card clicks on .property-card. */
function similarCardDataPropertyUrl(item) {
    const u = similarUrl(item);
    return u && u !== '#' ? u : null;
}

function similarImage(item) {
    const direct =
        drePickImageUrl(item?.featured_image) ||
        drePickImageUrl(item?.thumbnail) ||
        drePickImageUrl(item?.cover_image) ||
        drePickImageUrl(item?.image) ||
        drePickImageUrl(item?.photo);
    if (direct) {
        return direct;
    }

    const imgs = dreNormalizePropertyImages(item?.images);

    return imgs[0] || DRE_PROPERTY_PLACEHOLDER_IMAGE;
}

function similarPhotoCount(item) {
    const n = Number(item?.image_count);

    return Number.isFinite(n) && n > 0 ? n : (Array.isArray(item?.images) ? item.images.length : 0);
}

function typeLabel(t) {
    const raw = String(t || '').replace(/[_-]+/g, ' ').trim();

    return raw ? raw.replace(/\b\w/g, (c) => c.toUpperCase()) : 'Property';
}

function listingLabel(listingType) {
    const type = String(listingType || '').trim().toLowerCase();

    if (type === 'rent') return 'Rentals';
    if (type === 'sale') return 'For Sale';

    return 'Property';
}

async function fetchProperty() {
    window.dreDestroyPropertyDetailGallerySlider?.();
    loading.value = true;
    property.value = null;
    similar.value = [];

    const slug = String(route.params.slug || '').trim();

    if (!slug) {
        loading.value = false;

        return;
    }

    try {
        const { data } = await window.axios.get(`/api/properties/by-slug/${encodeURIComponent(slug)}`, {
            params: { lang: locale.value || 'en' },
        });
        property.value = data.property;
        similar.value = data.similar || [];
    } catch {
        property.value = null;
    } finally {
        loading.value = false;
    }
}

onMounted(fetchProperty);

watch(() => route.params.slug, fetchProperty);

watch(locale, fetchProperty);

watch(
    similar,
    () => {
        if (!similar.value.length) {
            window.dreDestroyPropertyDetailSimilarSlider?.();
        }
    },
    { flush: 'pre' },
);

watch(
    () => similar.value.length,
    async (len) => {
        if (!len) {
            return;
        }
        await nextTick();
        await new Promise((resolve) => {
            requestAnimationFrame(() => requestAnimationFrame(resolve));
        });
        window.dreInitPropertyDetailSimilarSlider?.();
    },
);

watch(
    () =>
        !loading.value && property.value?.id != null
            ? `${property.value.id}:${galleryImages.value.join('\x1e')}`
            : '',
    async (sig) => {
        if (!sig) {
            window.dreDestroyPropertyDetailGallerySlider?.();

            return;
        }
        await nextTick();
        await new Promise((resolve) => {
            requestAnimationFrame(() => requestAnimationFrame(resolve));
        });
        window.dreInitPropertyDetailGallerySlider?.();
    },
);

onBeforeUnmount(() => {
    window.dreDestroyPropertyDetailGallerySlider?.();
    window.dreDestroyPropertyDetailSimilarSlider?.();
});
</script>
