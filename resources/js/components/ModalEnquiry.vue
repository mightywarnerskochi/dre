<template>
    <!-- Enquiry Modal -->
    <div
        ref="modalRoot"
        class="modal fade siteEnquiryForm"
        id="siteEnquiryForm"
        data-enquiry-prefill="vue"
        aria-hidden="true"
        aria-labelledby="siteEnquiryFormLabel"
        tabindex="-1"
        role="dialog"
        aria-modal="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    :aria-label="t('modalEnquiry.form.close')"
                    id="siteEnquiryFormClose"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <path
                            d="M1.25 15.3933L15.3933 1.25M15.3933 15.3933L1.25 1.25"
                            stroke="black"
                            stroke-width="2.5"
                            stroke-miterlimit="10"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </button>

                <div class="modal-body p-0">
                    <form class="enquiry-form border-0 bg-white" @submit.prevent="submitPropertyEnquiry">
                        <h3 class="mb-4">{{ t('modalEnquiry.title') }}</h3>

                        <div class="form-row d-flex flex-wrap">
                            <div class="form-group">
                                <input
                                    v-model.trim="form.name"
                                    type="text"
                                    name="name"
                                    :placeholder="t('modalEnquiry.form.name')"
                                    :class="{ 'is-invalid': fieldErrors.name }"
                                    required
                                />
                                <div v-if="fieldErrors.name" class="invalid-feedback d-block">{{ fieldErrors.name }}</div>
                            </div>
                            <div class="form-group">
                                <input
                                    ref="phoneInputEl"
                                    v-model.trim="form.phone"
                                    type="tel"
                                    name="phone"
                                    class="phone_number"
                                    :placeholder="t('modalEnquiry.form.phone')"
                                    :class="{ 'is-invalid': fieldErrors.phone || fieldErrors.phone_national || fieldErrors.phone_dial_code }"
                                    required
                                />
                                <div v-if="fieldErrors.phone || fieldErrors.phone_national || fieldErrors.phone_dial_code" class="invalid-feedback d-block">
                                    {{ fieldErrors.phone || fieldErrors.phone_national || fieldErrors.phone_dial_code }}
                                </div>
                            </div>

                            <div class="form-group">
                                <input
                                    v-model.trim="form.email"
                                    type="email"
                                    name="email"
                                    :placeholder="t('modalEnquiry.form.email')"
                                    :class="{ 'is-invalid': fieldErrors.email }"
                                    required
                                />
                                <div v-if="fieldErrors.email" class="invalid-feedback d-block">{{ fieldErrors.email }}</div>
                            </div>
                            <div class="form-group">
                                <input
                                    ref="propertyTitleInputEl"
                                    v-model.trim="form.property_title"
                                    type="text"
                                    name="property_title"
                                    :placeholder="t('modalEnquiry.form.propertyName')"
                                    :readonly="prefillLocks.title"
                                />
                            </div>
                            <div class="form-group">
                                <input
                                    ref="locationInputEl"
                                    v-model.trim="form.location"
                                    type="text"
                                    name="location"
                                    :placeholder="t('modalEnquiry.form.propertyLocation')"
                                    :disabled="prefillLocks.location"
                                    :class="{ 'is-invalid': fieldErrors.location }"
                                    required
                                />
                                <div v-if="fieldErrors.location" class="invalid-feedback d-block">{{ fieldErrors.location }}</div>
                            </div>

                            <div
                                ref="propertyTypeDropRef"
                                class="form-group enquiry-form__select-field enquiry-form__property-type"
                                :class="{ 'is-open': propertyTypeMenuOpen }"
                            >
                                <button
                                    type="button"
                                    class="enquiry-form__type-trigger"
                                    :class="{
                                        'is-invalid': fieldErrors.property_type,
                                        'enquiry-form__select--placeholder': !form.property_type,
                                        'is-open': propertyTypeMenuOpen,
                                    }"
                                    :disabled="prefillLocks.type || propertyTypesLoading"
                                    :aria-expanded="propertyTypeMenuOpen ? 'true' : 'false'"
                                    aria-haspopup="listbox"
                                    :aria-label="t('listing.selectPropertyType')"
                                    @click.stop="togglePropertyTypeMenu"
                                >
                                    <span class="enquiry-form__type-trigger-text">{{ selectedPropertyTypeDisplay }}</span>
                                    <svg
                                        class="enquiry-form__type-chevron"
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M6 9L12 15L18 9"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </button>
                                <div
                                    v-show="propertyTypeMenuOpen"
                                    class="enquiry-form__type-menu"
                                    role="listbox"
                                    :aria-label="t('listing.propertyTypeHeading')"
                                >
                                    <button
                                        v-for="opt in propertyTypeOptions"
                                        :key="opt.value"
                                        type="button"
                                        class="enquiry-form__type-menu-item"
                                        :class="{ 'is-active': form.property_type === opt.value }"
                                        role="option"
                                        :aria-selected="form.property_type === opt.value ? 'true' : 'false'"
                                        @click.stop="selectPropertyType(opt.value)"
                                    >
                                        {{ opt.label }}
                                    </button>
                                </div>
                                <div v-if="fieldErrors.property_type" class="invalid-feedback d-block">{{ fieldErrors.property_type }}</div>
                            </div>
                            <div class="form-group">
                                <input
                                    ref="propertySizeInputEl"
                                    v-model.trim="form.property_size"
                                    type="text"
                                    name="property_size"
                                    :placeholder="t('modalEnquiry.form.propertySize')"
                                    :disabled="prefillLocks.size"
                                    :class="{ 'is-invalid': fieldErrors.property_size }"
                                    required
                                />
                                <div v-if="fieldErrors.property_size" class="invalid-feedback d-block">{{ fieldErrors.property_size }}</div>
                            </div>
                            <div v-if="fieldErrors.recaptcha_token || formBanner" class="form-group w-100">
                                <div class="invalid-feedback d-block">{{ fieldErrors.recaptcha_token || formBanner }}</div>
                            </div>
                        </div>

                        <div class="form-action">
                            <button type="submit" class="btn-theme" :disabled="submitting">
                                {{ submitting ? t('modalEnquiry.form.pleaseWait') : t('modalEnquiry.form.submit') }}
                            </button>
                        </div>

                        <div class="form-footer">
                            <p>{{ t('modalEnquiry.form.footerNote') }}</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const { locale, t } = useI18n({ useScope: 'global' });

const modalRoot = ref(null);
const phoneInputEl = ref(null);
const propertyTitleInputEl = ref(null);
const locationInputEl = ref(null);
const propertyTypeDropRef = ref(null);
const propertySizeInputEl = ref(null);

const propertyTypeMenuOpen = ref(false);

const submitting = ref(false);
const fieldErrors = ref({});
const formBanner = ref('');
const recaptchaToken = ref('');

const form = reactive({
    name: '',
    email: '',
    phone: '',
    location: '',
    property_type: '',
    property_size: '',
    property_title: '',
});

const prefillLocks = reactive({
    title: false,
    location: false,
    size: false,
    type: false,
});

const propertyTypeOptions = ref([]);
const propertyTypesLoading = ref(false);

const selectedPropertyTypeDisplay = computed(() => {
    if (propertyTypesLoading.value) {
        return t('common.loading');
    }
    if (!form.property_type) {
        return t('listing.selectPropertyType');
    }
    const o = propertyTypeOptions.value.find((x) => x.value === form.property_type);
    return o?.label ?? form.property_type;
});

function togglePropertyTypeMenu() {
    if (prefillLocks.type || propertyTypesLoading.value) {
        return;
    }
    propertyTypeMenuOpen.value = !propertyTypeMenuOpen.value;
}

function selectPropertyType(value) {
    form.property_type = value;
    propertyTypeMenuOpen.value = false;
}

function closePropertyTypeMenuOnOutsideClick(event) {
    const root = propertyTypeDropRef.value;
    if (!root || !propertyTypeMenuOpen.value) {
        return;
    }
    const target = event.target;
    if (target instanceof Node && root.contains(target)) {
        return;
    }
    propertyTypeMenuOpen.value = false;
}

let propertyTypesLoadPromise = null;

async function loadPropertyTypeOptions() {
    if (propertyTypesLoadPromise) {
        return propertyTypesLoadPromise;
    }
    propertyTypesLoadPromise = (async () => {
        propertyTypesLoading.value = true;
        try {
            const { data } = await axios.get('/api/properties/filter-options', {
                params: { lang: locale.value || 'en' },
            });
            const raw = Array.isArray(data?.property_types) ? data.property_types : [];
            propertyTypeOptions.value = raw.filter((o) => o && String(o.value ?? '').trim() !== '');
        } catch (_e) {
            propertyTypeOptions.value = [];
        } finally {
            propertyTypesLoading.value = false;
            propertyTypesLoadPromise = null;
        }
    })();
    return propertyTypesLoadPromise;
}

function extractEnquiryFromTriggerDataset(triggerEl) {
    if (!triggerEl?.dataset) return null;
    const title = String(triggerEl.dataset.propertyTitle || '').trim();
    const location = String(triggerEl.dataset.propertyLocation || '').trim();
    const type = String(triggerEl.dataset.propertyType || '').trim();
    const size = String(triggerEl.dataset.propertySize || '').trim();
    if (!title && !location && !type && !size) return null;
    return { title, location, type, size };
}

function extractEnquiryFromPropertyCard(triggerEl) {
    if (!triggerEl?.closest) return null;
    const card = triggerEl.closest('.property-card__inner');
    if (!card) return null;

    const titleEl = card.querySelector('.property-card__title');
    const locEl = card.querySelector('.property-card__location span');
    const typeEl = card.querySelector('.property-tag--fill');

    let sizeStr = '';
    card.querySelectorAll('.property-details__item span').forEach((span) => {
        const txt = String(span.textContent || '').trim();
        if (/ft|sq\s*ft|sqft|m²|sq\.?\s*m/i.test(txt)) {
            sizeStr = txt;
        }
    });

    const title = titleEl ? titleEl.textContent.trim() : '';
    const location = locEl ? locEl.textContent.trim() : '';
    const type = typeEl ? typeEl.textContent.trim() : '';

    if (!title && !location && !type && !sizeStr) return null;

    return { title, location, type, size: sizeStr };
}

/**
 * Bootstrap sometimes omits `relatedTarget` on show.bs.modal (e.g. Slick clones / complex DOM).
 * Capture the real opener on click so similar-listing cards still prefill like the listing grid.
 */
let lastEnquiryModalOpener = null;

function captureEnquiryModalOpener(event) {
    const el = event.target?.closest?.('[data-bs-toggle="modal"][data-bs-target="#siteEnquiryForm"]');
    if (el) {
        lastEnquiryModalOpener = el;
    }
}

function extractEnquiryFromDetailMain() {
    const detailRoot = document.querySelector('.property-detail-main');
    if (!detailRoot) return null;

    const titleEl = detailRoot.querySelector('.property-detail-title') || document.querySelector('.banner--page__title');
    const locEl = detailRoot.querySelector('.property-detail-location span');
    const typeEl = detailRoot.querySelector('.property-detail-badge');

    let sizeStr = '';
    detailRoot.querySelectorAll('.property-detail-stat span').forEach((span) => {
        const txt = String(span.textContent || '').trim();
        if (/ft|sq\s*ft|sqft|m²|sq\.?\s*m/i.test(txt)) {
            sizeStr = txt;
        }
    });

    const title = titleEl ? titleEl.textContent.trim() : '';
    const location = locEl ? locEl.textContent.trim() : '';
    const type = typeEl ? typeEl.textContent.trim() : '';

    if (!title && !location && !type && !sizeStr) return null;

    return { title, location, type, size: sizeStr };
}

function applyPropertyTypeFromLabel(typeLabelRaw) {
    const typeStr = String(typeLabelRaw || '').trim();
    if (!typeStr) {
        form.property_type = '';
        return;
    }
    const lower = typeStr.toLowerCase();
    const opts = propertyTypeOptions.value;
    for (const o of opts) {
        const val = String(o.value ?? '').trim();
        if (val.toLowerCase() === lower) {
            form.property_type = val;
            return;
        }
        const lab = String(o.label ?? '').trim().toLowerCase();
        if (lab === lower) {
            form.property_type = val;
            return;
        }
    }
    form.property_type = typeStr.toLowerCase().replace(/\s+/g, '_');
}

async function handleModalShow(e) {
    clearFieldErrors();
    propertyTypeMenuOpen.value = false;
    await loadPropertyTypeOptions();
    const trigger = e?.relatedTarget ?? lastEnquiryModalOpener ?? null;
    lastEnquiryModalOpener = null;

    const ctx = extractEnquiryFromTriggerDataset(trigger)
        || extractEnquiryFromPropertyCard(trigger)
        || extractEnquiryFromDetailMain()
        || { title: '', location: '', type: '', size: '' };

    form.property_title = ctx.title;
    form.location = ctx.location;
    form.property_size = ctx.size;
    applyPropertyTypeFromLabel(ctx.type);

    prefillLocks.title = ctx.title !== '';
    prefillLocks.location = ctx.location !== '';
    prefillLocks.size = ctx.size !== '';
    prefillLocks.type = ctx.type !== '';
}

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function xsrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
    if (!match?.[1]) return '';
    try {
        return decodeURIComponent(match[1]);
    } catch (_) {
        return match[1];
    }
}

function clearFieldErrors() {
    fieldErrors.value = {};
    formBanner.value = '';
}

function applyServerErrors(errors) {
    fieldErrors.value = {};
    if (!errors || typeof errors !== 'object') return;
    Object.entries(errors).forEach(([key, msgs]) => {
        const msg = Array.isArray(msgs) ? msgs[0] : msgs;
        if (msg) fieldErrors.value[key] = String(msg);
    });
}

function syncFormFromDom() {
    form.phone = String(phoneInputEl.value?.value ?? form.phone ?? '').trim();
    form.property_title = String(propertyTitleInputEl.value?.value ?? form.property_title ?? '').trim();
    form.location = String(locationInputEl.value?.value ?? form.location ?? '').trim();
    form.property_size = String(propertySizeInputEl.value?.value ?? form.property_size ?? '').trim();
}

function extractPhone() {
    const input = phoneInputEl.value;
    if (!input || typeof window.intlTelInputGlobals?.getInstance !== 'function') {
        const raw = String(form.phone ?? '').trim();
        const rawDigits = raw.replace(/\D/g, '');
        return {
            dial: '',
            national: rawDigits,
            full: raw,
            iso2: '',
            countryName: '',
        };
    }

    const iti = window.intlTelInputGlobals.getInstance(input);
    if (!iti) {
        const raw = String(input.value ?? form.phone ?? '').trim();
        const rawDigits = raw.replace(/\D/g, '');
        return {
            dial: '',
            national: rawDigits,
            full: raw,
            iso2: '',
            countryName: '',
        };
    }

    const selectedCountryData = iti.getSelectedCountryData() ?? {};
    const dial = String(selectedCountryData.dialCode ?? '').replace(/\D/g, '');
    const iso2 = String(selectedCountryData.iso2 ?? '').toUpperCase();
    const countryName = String(selectedCountryData.name ?? '').trim();
    let national = '';

    const e164Digits = String(iti.getNumber() ?? '').replace(/\D/g, '');
    if (e164Digits && dial && e164Digits.startsWith(dial)) {
        national = e164Digits.slice(dial.length);
    }
    if (!national) {
        const rawDigits = String(input.value ?? '').replace(/\D/g, '');
        national = rawDigits.startsWith(dial) ? rawDigits.slice(dial.length) : rawDigits;
    }

    return {
        dial,
        national,
        full: String(input.value ?? form.phone ?? '').trim(),
        iso2,
        countryName,
    };
}

function resolvePageSource() {
    const routeName = String(route.name ?? '').toLowerCase();
    const path = String(route.path ?? window.location.pathname ?? '').toLowerCase();

    if (routeName === 'home' || path === '/' || path === '/index.php') {
        return 'Home Page';
    }
    if (routeName === 'our-property' || routeName === 'map-search' || path.includes('/our-property') || path.includes('/map-search')) {
        return 'Listing Page';
    }
    if (routeName === 'property-details' || path.includes('/property-details')) {
        return 'Property Detail Page';
    }

    const title = String(document.title ?? '').trim();
    return title !== '' ? title : 'Website';
}

function resolvePageUrl() {
    return String(window.location.href || '').trim();
}

async function resolveRecaptchaToken() {
    const enabled = Boolean(window.__DRE_RECAPTCHA_ENABLED__);
    const key = String(window.__DRE_RECAPTCHA_SITE_KEY__ || '').trim();
    if (!enabled || !key) {
        recaptchaToken.value = '';
        return '';
    }

    if (!window.grecaptcha?.execute) {
        throw new Error('recaptcha-not-loaded');
    }

    await new Promise((resolve) => {
        window.grecaptcha.ready(resolve);
    });

    const token = await window.grecaptcha.execute(key, { action: 'property_enquiry_submit' });
    recaptchaToken.value = String(token || '');
    return recaptchaToken.value;
}

function validateForm() {
    clearFieldErrors();
    syncFormFromDom();

    let ok = true;
    if (!form.name) {
        fieldErrors.value.name = t('modalEnquiry.validation.required');
        ok = false;
    }
    if (!form.email) {
        fieldErrors.value.email = t('modalEnquiry.validation.required');
        ok = false;
    } else if (!EMAIL_RE.test(form.email)) {
        fieldErrors.value.email = t('modalEnquiry.validation.email');
        ok = false;
    }
    if (!form.location) {
        fieldErrors.value.location = t('modalEnquiry.validation.required');
        ok = false;
    }
    if (!form.property_type) {
        fieldErrors.value.property_type = t('modalEnquiry.validation.required');
        ok = false;
    }
    if (!form.property_size) {
        fieldErrors.value.property_size = t('modalEnquiry.validation.required');
        ok = false;
    }

    const phone = extractPhone();
    if (!phone.dial || !/^[1-9]\d{0,4}$/.test(phone.dial)) {
        fieldErrors.value.phone_dial_code = t('modalEnquiry.validation.phoneDial');
        ok = false;
    }
    if (!phone.national || !/^\d{6,13}$/.test(phone.national)) {
        fieldErrors.value.phone_national = t('modalEnquiry.validation.phoneNational');
        ok = false;
    }

    const pageSource = resolvePageSource();
    const pageUrl = resolvePageUrl();
    if (!pageSource) {
        fieldErrors.value.page_source = t('modalEnquiry.validation.pageSource');
        ok = false;
    }
    if (!pageUrl) {
        fieldErrors.value.page_url = t('modalEnquiry.validation.pageUrl');
        ok = false;
    }

    return ok;
}

function buildPayload() {
    syncFormFromDom();
    const phone = extractPhone();

    const payload = {
        enquiry_type: 'property',
        name: form.name,
        email: form.email,
        phone: phone.full,
        location: form.location,
        property_type: form.property_type,
        property_size: form.property_size,
        property_title: form.property_title,
        phone_dial_code: phone.dial,
        phone_national: phone.national,
        phone_country_iso2: phone.iso2,
        phone_country_name: phone.countryName,
        page_source: resolvePageSource(),
        page_url: resolvePageUrl(),
    };

    if (recaptchaToken.value) {
        payload.recaptcha_token = recaptchaToken.value;
    }

    return payload;
}

function resetForm() {
    form.name = '';
    form.email = '';
    form.phone = '';
    form.location = '';
    form.property_type = '';
    form.property_size = '';
    form.property_title = '';
}

function closeModal() {
    const element = modalRoot.value;
    if (!element || !window.bootstrap?.Modal) return;
    const modal = window.bootstrap.Modal.getInstance(element) || window.bootstrap.Modal.getOrCreateInstance(element);
    modal.hide();
}

async function submitPropertyEnquiry() {
    if (!validateForm()) {
        return;
    }

    submitting.value = true;
    try {
        await resolveRecaptchaToken();
        await axios.post('/property-enquiry', buildPayload(), {
            withCredentials: true,
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'X-XSRF-TOKEN': xsrfToken(),
                Accept: 'application/json',
            },
        });

        resetForm();
        clearFieldErrors();
        closeModal();
        await router.push({ name: 'thank-you' });
    } catch (e) {
        if (e.message === 'recaptcha-not-loaded') {
            fieldErrors.value.recaptcha_token = t('modalEnquiry.validation.recaptcha');
            return;
        }
        if (e.response?.status === 422) {
            applyServerErrors(e.response.data.errors);
            if (!Object.keys(fieldErrors.value).length && e.response.data?.message) {
                formBanner.value = String(e.response.data.message);
            }
        } else {
            formBanner.value = t('modalEnquiry.validation.submitError');
        }
    } finally {
        submitting.value = false;
    }
}

async function handleModalShown() {
    await nextTick();
    if (typeof window.dreInitSiteEnquiryPhone === 'function') {
        window.dreInitSiteEnquiryPhone();
    }
    await nextTick();
    syncFormFromDom();
}

function handleModalHidden() {
    clearFieldErrors();
    resetForm();
    propertyTypeMenuOpen.value = false;
    prefillLocks.title = false;
    prefillLocks.location = false;
    prefillLocks.size = false;
    prefillLocks.type = false;
}

watch(locale, () => {
    // Lazy load on demand per locale when modal opens.
    propertyTypeOptions.value = [];
    propertyTypesLoadPromise = null;
});

onMounted(() => {
    document.addEventListener('click', captureEnquiryModalOpener, true);
    document.addEventListener('click', closePropertyTypeMenuOnOutsideClick, false);
    const element = modalRoot.value;
    if (!element) return;
    element.addEventListener('show.bs.modal', handleModalShow);
    element.addEventListener('shown.bs.modal', handleModalShown);
    element.addEventListener('hidden.bs.modal', handleModalHidden);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', captureEnquiryModalOpener, true);
    document.removeEventListener('click', closePropertyTypeMenuOnOutsideClick, false);
    const element = modalRoot.value;
    if (!element) return;
    element.removeEventListener('show.bs.modal', handleModalShow);
    element.removeEventListener('shown.bs.modal', handleModalShown);
    element.removeEventListener('hidden.bs.modal', handleModalHidden);
});
</script>
