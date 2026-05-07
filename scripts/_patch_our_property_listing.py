from pathlib import Path

p = Path(__file__).resolve().parent.parent / "resources/js/components/OurPropertyContent.vue"
text = p.read_text(encoding="utf-8")
start = text.index('            <div class=" properties-grid')
end = text.index("    </section>\n\n\n\n</template>")

new_block = r'''            <div class="properties-grid d-flex flex-wrap">
                <p v-if="listError" class="w-100 text-danger py-4">{{ listError }}</p>
                <template v-for="prop in properties" :key="prop.id">
                    <div class="properties-col">
                        <article class="property-card property-card--listing">
                            <div class="property-card__ghost" aria-hidden="true"></div>
                            <div class="property-card__inner">
                                <div class="property-card__media">
                                    <span v-if="prop.is_featured" class="property-card__badge">Featured</span>
                                    <picture>
                                        <img
                                            :key="prop.id + '-' + (activeSlide[prop.id] ?? 0)"
                                            :src="cardImages(prop)[activeSlide[prop.id] ?? 0]"
                                            :alt="prop.title"
                                            width="427"
                                            height="260"
                                            loading="lazy"
                                            @error="dreOnPropertyImgError"
                                        >
                                    </picture>
                                    <div class="property-card__dots" role="tablist" aria-label="Photo gallery">
                                        <button
                                            v-for="(img, idx) in cardImages(prop)"
                                            :key="prop.id + '-dot-' + idx"
                                            type="button"
                                            :class="{ 'is-active': idx === (activeSlide[prop.id] ?? 0) }"
                                            :aria-label="'Photo ' + (idx + 1)"
                                            @click="activeSlide[prop.id] = idx"
                                        />
                                    </div>
                                    <a
                                        v-if="prop.virtual_tour_url"
                                        class="property-card__360"
                                        :href="prop.virtual_tour_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        title="360 virtual tour"
                                    >360</a>
                                </div>
                                <div class="property-card__body">
                                    <h3 class="property-card__title">
                                        <RouterLink v-if="prop.slug" class="text-decoration-none text-reset" :to="detailLink(prop.slug)">{{ prop.title }}</RouterLink>
                                        <template v-else>{{ prop.title }}</template>
                                    </h3>
                                    <p class="property-card__location">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 2C7.6 2 4 5.6 4 10C4 15.4 11 21.5 11.3 21.8C11.5 21.9 11.8 22 12 22C12.2 22 12.5 21.9 12.7 21.8C13 21.5 20 15.4 20 10C20 5.6 16.4 2 12 2ZM12 19.7C9.9 17.7 6 13.4 6 10C6 6.7 8.7 4 12 4C15.3 4 18 6.7 18 10C18 13.3 14.1 17.7 12 19.7ZM12 6C9.8 6 8 7.8 8 10C8 12.2 9.8 14 12 14C14.2 14 16 12.2 16 10C16 7.8 14.2 6 12 6ZM12 12C10.9 12 10 11.1 10 10C10 8.9 10.9 8 12 8C13.1 8 14 8.9 14 10C14 11.1 13.1 12 12 12Z" fill="#2F3F58" />
                                        </svg>
                                        <span>{{ prop.location }}</span>
                                    </p>
                                    <div class="property-card__meta">
                                        <div class="property-card__price-row">
                                            <div class="property-card__price">{{ formatPrice(prop.price) }} د.إ</div>
                                            <div class="property-card__photos">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M13.997 4C14.3578 3.99999 14.7119 4.09759 15.0217 4.28244C15.3316 4.46729 15.5856 4.73251 15.757 5.05L16.243 5.95C16.4144 6.26749 16.6684 6.53271 16.9783 6.71756C17.2881 6.90241 17.6422 7.00001 18.003 7H20C20.5304 7 21.0391 7.21071 21.4142 7.58579C21.7893 7.96086 22 8.46957 22 9V18C22 18.5304 21.7893 19.0391 21.4142 19.4142C21.0391 19.7893 20.5304 20 20 20H4C3.46957 20 2.96086 19.7893 2.58579 19.4142C2.21071 19.0391 2 18.5304 2 18V9C2 8.46957 2.21071 7.96086 2.58579 7.58579C2.96086 7.21071 3.46957 7 4 7H5.997C6.35742 7.00002 6.71115 6.90264 7.02078 6.71817C7.33041 6.53369 7.58444 6.26897 7.756 5.952L8.245 5.048C8.41656 4.73103 8.67059 4.46631 8.98022 4.28183C9.28985 4.09736 9.64358 3.99998 10.004 4H13.997Z" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z" stroke="#A4B3C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                {{ photoCount(prop) }} Photos
                                            </div>
                                        </div>
                                        <div class="property-card__tags">
                                            <span class="property-tag property-tag--fill">{{ typeLabel(prop.property_type) }}</span>
                                            <span class="property-tag property-tag--outline">{{ listingLabel(prop.listing_type) }}</span>
                                        </div>
                                    </div>
                                    <div class="property-details">
                                        <div class="property-details__item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M2.25 19.5V14.25C2.25235 13.4551 2.56917 12.6934 3.13128 12.1313C3.69338 11.5692 4.45507 11.2523 5.25 11.25H18.75C19.5449 11.2523 20.3066 11.5692 20.8687 12.1313C21.4308 12.6934 21.7477 13.4551 21.75 14.25V19.5M18 11.25H4.5V6.375C4.50148 5.87818 4.6995 5.40212 5.05081 5.05081C5.40212 4.6995 5.87818 4.50148 6.375 4.5H17.625C18.1218 4.50148 18.5979 4.6995 18.9492 5.05081C19.3005 5.40212 19.4985 5.87818 19.5 6.375V11.25H18Z" stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M2.25 19.5V19.125C2.25087 18.8269 2.36967 18.5413 2.58046 18.3305C2.79125 18.1197 3.0769 18.0009 3.375 18H20.625C20.9231 18.0009 21.2087 18.1197 21.4195 18.3305C21.6303 18.5413 21.7491 18.8269 21.75 19.125V19.5M5.25 11.25V10.5C5.25111 10.1025 5.40951 9.72163 5.69057 9.44057C5.97163 9.15951 6.35252 9.00111 6.75 9H10.5C10.8975 9.00111 11.2784 9.15951 11.5594 9.44057C11.8405 9.72163 11.9989 10.1025 12 10.5M12 10.5V11.25M12 10.5C12.0011 10.1025 12.1595 9.72163 12.4406 9.44057C12.7216 9.15951 13.1025 9.00111 13.5 9H17.25C17.6475 9.00111 18.0284 9.15951 18.3094 9.44057C18.5905 9.72163 18.7489 10.1025 18.75 10.5V11.25" stroke="#2F3F58" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <span>{{ prop.bedrooms }} Bedroom{{ prop.bedrooms === 1 ? '' : 's' }}</span>
                                        </div>
                                        <div class="property-details__item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M21.75 13.125H3.75V4.6875C3.74914 4.36975 3.8113 4.05498 3.9329 3.76142C4.0545 3.46786 4.23312 3.20133 4.45842 2.97726L4.47717 2.95852C4.83026 2.60594 5.28359 2.37097 5.77524 2.28571C6.26688 2.20045 6.77286 2.26906 7.22405 2.48217C6.79794 3.19064 6.62083 4.02119 6.72086 4.84185C6.82089 5.66251 7.19233 6.4262 7.77614 7.01156L8.28947 7.52489L7.34461 8.4698L8.4052 9.53039L9.35006 8.58553L14.5855 3.3502L15.5303 2.40534L14.4697 1.3447L13.5248 2.28956L13.0115 1.77623C12.3969 1.16336 11.5867 0.785558 10.7222 0.708671C9.85764 0.631784 8.99353 0.860685 8.28042 1.35548C7.52846 0.8806 6.63734 0.675589 5.75346 0.774129C4.86958 0.87267 4.04549 1.2689 3.41658 1.89773L3.39783 1.91648C3.03278 2.27952 2.74336 2.71136 2.54634 3.18701C2.34931 3.66266 2.24859 4.17266 2.25 4.6875V13.125H0.75V14.625H2.25V16.0641C2.25001 16.185 2.26951 16.3051 2.30775 16.4198L3.70312 20.6057C3.7776 20.8298 3.92078 21.0248 4.11235 21.1629C4.30392 21.301 4.53413 21.3752 4.77028 21.375H5.37497L4.82812 23.25H6.39061L6.9375 21.375H16.6922L17.2547 23.25H18.8203L18.2578 21.375H19.2295C19.4657 21.3752 19.696 21.301 19.8876 21.1629C20.0792 21.0248 20.2224 20.8299 20.2969 20.6057L21.6922 16.4198C21.7304 16.3051 21.75 16.185 21.75 16.0641V14.625H23.25V13.125H21.75ZM8.83688 2.83687C9.25012 2.42453 9.81007 2.19295 10.3939 2.19295C10.9776 2.19295 11.5376 2.42453 11.9508 2.83687L12.4641 3.3502L9.3502 6.46406L8.83688 5.95083C8.42455 5.53757 8.19298 4.97763 8.19298 4.39385C8.19298 3.81007 8.42455 3.25013 8.83688 2.83687ZM20.25 16.0031L18.9594 19.875H5.04056L3.75 16.0031V14.625H20.25V16.0031Z" fill="#2F3F58" />
                                            </svg>
                                            <span>{{ prop.bathrooms }} Bathroom{{ prop.bathrooms === 1 ? '' : 's' }}</span>
                                        </div>
                                        <div class="property-details__item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none" aria-hidden="true">
                                                <path d="M3.012 0C1.35888 0 0 1.35864 0 3.012C0 4.45608 1.03608 5.67504 2.4 5.96064V18.0634C1.03608 18.349 0 19.5682 0 21.0122C0 22.6656 1.35864 24.024 3.012 24.024C4.46328 24.024 5.6736 22.9742 5.94984 21.6H18.059C18.3355 22.9757 19.5598 24.024 21.0122 24.024C22.6082 24.024 23.9174 22.7549 24.007 21.1795C24.0181 21.1245 24.0238 21.0684 24.0238 21.0122C24.0238 20.9561 24.0181 20.9 24.007 20.845C23.9287 19.4681 22.9188 18.3254 21.6 18.059V5.96496C22.9188 5.69856 23.9287 4.55616 24.0072 3.17952C24.0184 3.12446 24.024 3.06842 24.024 3.01224C24.024 2.95606 24.0184 2.90002 24.0072 2.84496C23.9172 1.2696 22.6082 0 21.012 0C19.5682 0 18.349 1.03608 18.0634 2.4H5.9448C5.6592 1.03776 4.4544 0 3.012 0ZM3.012 1.68C3.75768 1.68 4.344 2.26656 4.344 3.012C4.344 3.75768 3.75768 4.344 3.012 4.344C2.26632 4.344 1.68 3.75768 1.68 3.012C1.68 2.26632 2.26656 1.68 3.012 1.68ZM21.012 1.68C21.7577 1.68 22.344 2.26656 22.344 3.012C22.344 3.75768 21.7577 4.344 21.012 4.344C20.2663 4.344 19.68 3.75768 19.68 3.012C19.68 2.26632 20.2666 1.68 21.012 1.68ZM5.81232 4.08H18.2011C18.3524 4.47204 18.5832 4.82851 18.879 5.12694C19.1749 5.42537 19.5293 5.65931 19.92 5.814V18.21C19.5332 18.363 19.182 18.5937 18.8878 18.8878C18.5937 19.182 18.363 19.5332 18.21 19.92H5.802C5.64914 19.5302 5.41724 19.1762 5.12088 18.8804C4.82451 18.5845 4.47012 18.3533 4.08 18.2011V5.82288C4.4743 5.66925 4.8321 5.43482 5.1304 5.13466C5.4287 4.8345 5.6609 4.47525 5.81208 4.08M3.012 19.68C3.75744 19.68 4.34376 20.2666 4.34376 21.012C4.34376 21.7577 3.75744 22.344 3.01176 22.344C2.26608 22.344 1.68 21.7577 1.68 21.012C1.68 20.2663 2.26656 19.68 3.012 19.68ZM21.012 19.68C21.7574 19.68 22.3438 20.2666 22.3438 21.012C22.3438 21.7577 21.7574 22.344 21.0118 22.344C20.2661 22.344 19.68 21.7577 19.68 21.012C19.68 20.2663 20.2666 19.68 21.012 19.68Z" fill="#2F3F58" />
                                            </svg>
                                            <span>{{ formatSqft(prop.sqft) }} ft²</span>
                                        </div>
                                    </div>
                                    <div class="property-card__footer">
                                        <div v-if="prop.agent" class="property-card__agent">
                                            <div class="property-card__agent-avatar">
                                                <img :src="prop.agent.image" alt="" width="44" height="44" loading="lazy" @error="dreOnPropertyImgError">
                                            </div>
                                            <div class="property-card__agent-text">
                                                <p class="property-card__agent-name">{{ prop.agent.name }}</p>
                                                <p class="property-card__agent-role">{{ prop.agent.designation || '' }}</p>
                                            </div>
                                        </div>
                                        <div class="property-card__actions">
                                            <a class="property-btn property-btn--primary" href="#" data-bs-toggle="modal" data-bs-target="#siteEnquiryForm">Enquiry</a>
                                            <a class="property-btn property-btn--light" :href="prop.phone">Call Now</a>
                                            <a class="property-btn property-btn--outline" :href="prop.whatsapp" target="_blank" rel="noopener noreferrer">Whatsapp</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </template>
                <div v-if="listLoading" class="w-100 text-center py-5" role="status">Loading properties…</div>
                <div ref="loadMoreSentinel" class="w-100" style="height: 1px;" aria-hidden="true" />
            </div>
        </div>'''

p.write_text(text[:start] + new_block + text[end:], encoding="utf-8")
print("patched", p)
