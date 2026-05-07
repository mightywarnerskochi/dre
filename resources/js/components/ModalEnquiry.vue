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
                    aria-label="Close"
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
                        <h3 class="mb-4">Enquire Now</h3>

                        <div class="form-row d-flex flex-wrap">
                            <div class="form-group">
                                <input
                                    v-model.trim="form.name"
                                    type="text"
                                    name="name"
                                    placeholder="Name"
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
                                    placeholder="Phone"
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
                                    placeholder="Email"
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
                                    placeholder="Property Name"
                                    readonly
                                />
                            </div>
                            <div class="form-group">
                                <input
                                    ref="locationInputEl"
                                    v-model.trim="form.location"
                                    type="text"
                                    name="location"
                                    placeholder="Property Location"
                                    :disabled="prefillLocks.location"
                                    :class="{ 'is-invalid': fieldErrors.location }"
                                    required
                                />
                                <div v-if="fieldErrors.location" class="invalid-feedback d-block">{{ fieldErrors.location }}</div>
                            </div>

                            <div class="form-group">
                                <select
                                    ref="propertyTypeInputEl"
                                    v-model="form.property_type"
                                    name="property_type"
                                    :disabled="prefillLocks.type"
                                    :class="{ 'is-invalid': fieldErrors.property_type }"
                                    required
                                >
                                    <option value="" disabled hidden>Property Type</option>
                                    <option value="apartment">Apartment</option>
                                    <option value="villa">Villa</option>
                                    <option value="townhouse">Townhouse</option>
                                    <option value="plot">Plot</option>
                                </select>
                                <div v-if="fieldErrors.property_type" class="invalid-feedback d-block">{{ fieldErrors.property_type }}</div>
                            </div>
                            <div class="form-group">
                                <input
                                    ref="propertySizeInputEl"
                                    v-model.trim="form.property_size"
                                    type="text"
                                    name="property_size"
                                    placeholder="Property Size"
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
                                {{ submitting ? 'Please wait...' : 'Send Enquiry' }}
                            </button>
                        </div>

                        <div class="form-footer">
                            <p>Our team will contact you within 24 hours.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { nextTick, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const route = useRoute();

const modalRoot = ref(null);
const phoneInputEl = ref(null);
const propertyTitleInputEl = ref(null);
const locationInputEl = ref(null);
const propertyTypeInputEl = ref(null);
const propertySizeInputEl = ref(null);

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
    location: false,
    size: false,
    type: false,
});

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
    const sel = propertyTypeInputEl.value;
    if (!typeStr) {
        form.property_type = '';
        return;
    }
    if (!sel) {
        form.property_type = typeStr.toLowerCase();
        return;
    }

    let matched = false;
    Array.from(sel.options).forEach((opt) => {
        if (opt.text.toLowerCase() === typeStr.toLowerCase()) {
            form.property_type = opt.value;
            matched = true;
        }
    });
    if (!matched) {
        const value = typeStr.toLowerCase().replace(/\s+/g, '_');
        const opt = new Option(typeStr, value);
        sel.add(opt);
        form.property_type = value;
    }
}

function handleModalShow(e) {
    clearFieldErrors();
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
    form.property_type = String(propertyTypeInputEl.value?.value ?? form.property_type ?? '').trim();
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
        fieldErrors.value.name = 'This field is required.';
        ok = false;
    }
    if (!form.email) {
        fieldErrors.value.email = 'This field is required.';
        ok = false;
    } else if (!EMAIL_RE.test(form.email)) {
        fieldErrors.value.email = 'Enter a valid email address.';
        ok = false;
    }
    if (!form.location) {
        fieldErrors.value.location = 'This field is required.';
        ok = false;
    }
    if (!form.property_type) {
        fieldErrors.value.property_type = 'This field is required.';
        ok = false;
    }
    if (!form.property_size) {
        fieldErrors.value.property_size = 'This field is required.';
        ok = false;
    }

    const phone = extractPhone();
    if (!phone.dial || !/^[1-9]\d{0,4}$/.test(phone.dial)) {
        fieldErrors.value.phone_dial_code = 'Select a valid country code.';
        ok = false;
    }
    if (!phone.national || !/^\d{6,13}$/.test(phone.national)) {
        fieldErrors.value.phone_national = 'Enter a phone number with 6 to 13 digits.';
        ok = false;
    }

    const pageSource = resolvePageSource();
    const pageUrl = resolvePageUrl();
    if (!pageSource) {
        fieldErrors.value.page_source = 'Page source is required.';
        ok = false;
    }
    if (!pageUrl) {
        fieldErrors.value.page_url = 'Page URL is required.';
        ok = false;
    }

    return ok;
}

function buildPayload() {
    syncFormFromDom();
    const phone = extractPhone();

    const payload = {
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
            fieldErrors.value.recaptcha_token = 'Unable to verify reCAPTCHA. Please refresh and try again.';
            return;
        }
        if (e.response?.status === 422) {
            applyServerErrors(e.response.data.errors);
            if (!Object.keys(fieldErrors.value).length && e.response.data?.message) {
                formBanner.value = String(e.response.data.message);
            }
        } else {
            formBanner.value = 'Unable to submit now. Please try again.';
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
    prefillLocks.location = false;
    prefillLocks.size = false;
    prefillLocks.type = false;
}

onMounted(() => {
    document.addEventListener('click', captureEnquiryModalOpener, true);
    const element = modalRoot.value;
    if (!element) return;
    element.addEventListener('show.bs.modal', handleModalShow);
    element.addEventListener('shown.bs.modal', handleModalShown);
    element.addEventListener('hidden.bs.modal', handleModalHidden);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', captureEnquiryModalOpener, true);
    const element = modalRoot.value;
    if (!element) return;
    element.removeEventListener('show.bs.modal', handleModalShow);
    element.removeEventListener('shown.bs.modal', handleModalShown);
    element.removeEventListener('hidden.bs.modal', handleModalHidden);
});
</script>
