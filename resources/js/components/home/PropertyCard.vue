<script setup>
import { computed, ref, watch } from 'vue';
import { dreNormalizePropertyImages, dreOnPropertyImgError } from '../../utils/propertyImages';

const props = defineProps({
    property: { type: Object, required: true },
});

const imgIndex = ref(0);

const galleryImages = computed(() => dreNormalizePropertyImages(props.property.images));

const currentImage = computed(() => galleryImages.value[imgIndex.value] ?? galleryImages.value[0]);

watch(galleryImages, () => {
    imgIndex.value = 0;
});

function setImage(i) {
    imgIndex.value = i;
}

function formatPrice(num) {
    return new Intl.NumberFormat('en-AE').format(num);
}
</script>

<template>
    <article class="dre-card-property">
        <div class="dre-card-property__media">
            <img :src="currentImage" :alt="property.title" @error="dreOnPropertyImgError" />
            <div class="dre-card-property__dots">
                <button
                    v-for="(_, i) in galleryImages"
                    :key="i"
                    type="button"
                    class="dre-card-property__dot"
                    :class="{ 'is-active': i === imgIndex }"
                    :aria-label="`Photo ${i + 1}`"
                    @click="setImage(i)"
                />
            </div>
        </div>
        <div class="dre-card-property__body">
            <div>
                <h3 class="dre-card-property__title">
                    <a v-if="property.url" :href="property.url" class="dre-card-property__title-link">{{ property.title }}</a>
                    <template v-else>{{ property.title }}</template>
                </h3>
                <p class="dre-card-property__loc">
                    <svg class="dre-icon-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>
                    {{ property.location }}
                </p>
            </div>
            <p class="dre-card-property__price">
                {{ formatPrice(property.price) }}
                <span class="dre-card-property__price-suffix">{{ property.period || ' / yr' }}</span>
            </p>
            <div class="dre-card-property__meta">
                <span>
                    <svg class="dre-icon-sm" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                        />
                    </svg>
                    {{ property.beds }} Bedroom
                </span>
                <span>
                    <svg class="dre-icon-sm" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v10H5V5z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ property.baths }} Bathroom
                </span>
                <span>
                    <svg class="dre-icon-sm" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12h-2V6H6v10H4V4z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ property.sqft }} sqft
                </span>
            </div>
            <div class="dre-card-property__actions">
                <a :href="property.phone" class="dre-btn dre-btn--call">Call</a>
                <a
                    :href="property.whatsapp"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="dre-btn dre-btn--whatsapp"
                >WhatsApp</a>
                <a
                    :href="property.url || property.inquireUrl || '#'"
                    class="dre-btn dre-btn--inquire"
                >Inquire</a>
            </div>
        </div>
    </article>
</template>
