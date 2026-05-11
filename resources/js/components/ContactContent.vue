<template>
<section class="banner banner--page banner--page--listing position-relative" :aria-label="t('contact.pageHeaderAria')">
        <div class="banner--page__bg">
            <picture>
                <img :src="heroImageSrc" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">{{ pageHeading }}</h1>
                <ol class="breadcrumb-minimal" :aria-label="t('contact.breadcrumbAria')">
                    <li>
                        <a href="/" :aria-label="t('contact.homeAria')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">{{ breadcrumbCurrent }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="contact-form commonPadding-120" aria-labelledby="contact-form-heading">
        <div class="container-ctn">
            <div class="d-flex flex-wrap justify-content-between">
                <div class=" contact-form__left">
                    <h2 class="contact-form__heading" id="contact-form-heading">{{ contactSection.title }}</h2>
                    <div class="form-wrapper">
                        <h3 id="information-request-heading">{{ contactSection.subTitle }}</h3>
                        <p id="information-request-desc">{{ contactSection.content }}</p>
                        <form class="information-request-form" action="" method="post" aria-labelledby="information-request-heading" aria-describedby="information-request-desc" @submit.prevent="submitContactForm">
                            <div class="d-flex flex-wrap justify-content-between">
                                <div class="col--6">
                                    <label class="visually-hidden" for="contact-name">{{ t('contact.form.name') }}</label>
                                    <input v-model="form.name" class="form-control" :class="{ 'is-invalid': fieldErrors.name }" type="text" name="name" id="contact-name" :placeholder="t('contact.form.name')" autocomplete="name" required>
                                    <div v-if="fieldErrors.name" class="invalid-feedback d-block">{{ fieldErrors.name }}</div>
                                </div>
                                <div class="col--6">
                                    <label class="visually-hidden" for="contact-email">{{ t('contact.form.email') }}</label>
                                    <input v-model="form.email" class="form-control" :class="{ 'is-invalid': fieldErrors.email }" type="email" name="email" id="contact-email" :placeholder="t('contact.form.email')" autocomplete="email" required>
                                    <div v-if="fieldErrors.email" class="invalid-feedback d-block">{{ fieldErrors.email }}</div>
                                </div>
                                <div class="col--6">
                                    <label class="visually-hidden" for="contact-phone">{{ t('contact.form.phone') }}</label>
                                    <!-- v-model omitted: intl-tel-input owns the value; Vue re-renders would reset the country selector -->
                                    <input
                                        ref="contactPhoneInput"
                                        class="form-control phone_number"
                                        :class="{ 'is-invalid': fieldErrors.phone || fieldErrors.phone_national || fieldErrors.phone_dial_code }"
                                        type="tel"
                                        name="phone"
                                        id="contact-phone"
                                        :placeholder="t('contact.form.phone')"
                                        autocomplete="tel"
                                        required
                                    >
                                    <div v-if="fieldErrors.phone || fieldErrors.phone_national || fieldErrors.phone_dial_code" class="invalid-feedback d-block">{{ fieldErrors.phone || fieldErrors.phone_national || fieldErrors.phone_dial_code }}</div>
                                </div>
                                <div class="col--6">
                                    <label class="visually-hidden" for="contact-subject">{{ t('contact.form.subject') }}</label>
                                    <input v-model="form.subject" class="form-control" :class="{ 'is-invalid': fieldErrors.subject }" type="text" name="subject" id="contact-subject" :placeholder="t('contact.form.subject')" required>
                                    <div v-if="fieldErrors.subject" class="invalid-feedback d-block">{{ fieldErrors.subject }}</div>
                                </div>
                                <div class="col--12">
                                    <label class="visually-hidden" for="contact-message">{{ t('contact.form.message') }}</label>
                                    <textarea v-model="form.message" class="form-control" :class="{ 'is-invalid': fieldErrors.message }" name="message" id="contact-message" rows="5" :placeholder="t('contact.form.message')" required></textarea>
                                    <div v-if="fieldErrors.message" class="invalid-feedback d-block">{{ fieldErrors.message }}</div>
                                </div>
                                <div v-if="fieldErrors.recaptcha_token || formBanner" class="col--12">
                                    <div class="invalid-feedback d-block">{{ fieldErrors.recaptcha_token || formBanner }}</div>
                                </div>
                                <div class="col--12 mb-0">
                                    <button type="submit" class="btn btn-theme ms-auto" :disabled="submitting">{{ submitting ? t('contact.form.pleaseWait') : t('contact.form.submit') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="contact-form__right">
                    <h2 class="contact-offices__title">{{ officesColumnTitle }}</h2>
                    <p v-if="locationsSection.sectionDescription" class="mb-4 text-muted">{{ locationsSection.sectionDescription }}</p>
                    <div class="contact-offices">
                        <template v-if="!locationsSection.items.length">
                            <p class="text-muted mb-0">{{ t('contact.noLocations') }}</p>
                        </template>
                        <article
                            v-for="loc in locationsSection.items"
                            :key="loc.id"
                            class="contact-office"
                            :aria-label="t('contact.officeAria', { name: officeDisplayName(loc) })"
                        >
                            <div class="contact-office__map">
                                <a
                                    v-if="loc.mapLink"
                                    :href="loc.mapLink"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :aria-label="t('contact.openMap')"
                                >
                                    <img
                                        :src="loc.image || asset('images/map-2.jpg')"
                                        :alt="mapImageAlt(loc)"
                                        width="600"
                                        height="220"
                                        loading="lazy"
                                    >
                                </a>
                                <img
                                    v-else
                                    :src="loc.image || asset('images/map-2.jpg')"
                                    :alt="mapImageAlt(loc)"
                                    width="600"
                                    height="220"
                                    loading="lazy"
                                >
                            </div>
                            <ul class="contact-office__list">
                                <li v-if="loc.address" class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path d="M12 2.00012C7.6 2.00012 4 5.60012 4 10.0001C4 15.4001 11 21.5001 11.3 21.8001C11.5 21.9001 11.8 22.0001 12 22.0001C12.2 22.0001 12.5 21.9001 12.7 21.8001C13 21.5001 20 15.4001 20 10.0001C20 5.60012 16.4 2.00012 12 2.00012ZM12 19.7001C9.9 17.7001 6 13.4001 6 10.0001C6 6.70012 8.7 4.00012 12 4.00012C15.3 4.00012 18 6.70012 18 10.0001C18 13.3001 14.1 17.7001 12 19.7001ZM12 6.00012C9.8 6.00012 8 7.80012 8 10.0001C8 12.2001 9.8 14.0001 12 14.0001C14.2 14.0001 16 12.2001 16 10.0001C16 7.80012 14.2 6.00012 12 6.00012ZM12 12.0001C10.9 12.0001 10 11.1001 10 10.0001C10 8.90012 10.9 8.00012 12 8.00012C13.1 8.00012 14 8.90012 14 10.0001C14 11.1001 13.1 12.0001 12 12.0001Z" fill="#2F3F58"/> </svg>
                                    </span>
                                    <span class="contact-office__text">{{ loc.address }}</span>
                                </li>
                                <li v-if="loc.phones.length" class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.73303 2.04305C6.95003 0.833052 8.95403 1.04805 9.97303 2.41005L11.235 4.09405C12.065 5.20205 11.991 6.75005 11.006 7.72905L10.768 7.96705C10.741 8.06696 10.7383 8.17187 10.76 8.27305C10.823 8.68105 11.164 9.54505 12.592 10.9651C14.02 12.3851 14.89 12.7251 15.304 12.7891C15.4083 12.81 15.5161 12.807 15.619 12.7801L16.027 12.3741C16.903 11.5041 18.247 11.3411 19.331 11.9301L21.241 12.9701C22.878 13.8581 23.291 16.0821 21.951 17.4151L20.53 18.8271C20.082 19.2721 19.48 19.6431 18.746 19.7121C16.936 19.8811 12.719 19.6651 8.28603 15.2581C4.14903 11.1441 3.35503 7.55605 3.25403 5.78805C3.20403 4.89405 3.62603 4.13805 4.16403 3.60405L5.73303 2.04305ZM8.77303 3.30905C8.26603 2.63205 7.32203 2.57805 6.79003 3.10705L5.22003 4.66705C4.89003 4.99505 4.73203 5.35705 4.75203 5.70305C4.83203 7.10805 5.47203 10.3451 9.34403 14.1951C13.406 18.2331 17.157 18.3541 18.607 18.2181C18.903 18.1911 19.197 18.0371 19.472 17.7641L20.892 16.3511C21.47 15.7771 21.343 14.7311 20.525 14.2871L18.615 13.2481C18.087 12.9621 17.469 13.0561 17.085 13.4381L16.63 13.8911L16.1 13.3591C16.63 13.8911 16.629 13.8921 16.628 13.8921L16.627 13.8941L16.624 13.8971L16.617 13.9031L16.602 13.9171C16.5598 13.9562 16.5143 13.9917 16.466 14.0231C16.386 14.0761 16.28 14.1351 16.147 14.1841C15.877 14.2851 15.519 14.3391 15.077 14.2711C14.21 14.1381 13.061 13.5471 11.534 12.0291C10.008 10.5111 9.41203 9.36905 9.27803 8.50305C9.20903 8.06105 9.26403 7.70305 9.36603 7.43305C9.42216 7.28112 9.50254 7.13928 9.60403 7.01305L9.63603 6.97805L9.65003 6.96305L9.65603 6.95705L9.65903 6.95405L9.66103 6.95205L9.94903 6.66605C10.377 6.23905 10.437 5.53205 10.034 4.99305L8.77303 3.30905Z" fill="#2F3F58"/> </svg>
                                    </span>
                                    <span class="contact-office__text">
                                        <template v-for="(phone, idx) in loc.phones" :key="`p-${loc.id}-${idx}`">
                                            <span v-if="idx > 0" class="contact-office__sep" aria-hidden="true">|</span>
                                            <a :href="telHref(phone)">{{ phone }}</a>
                                        </template>
                                    </span>
                                </li>
                                <li v-if="loc.emails.length" class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path d="M22 5.99988C22 4.89988 21.1 3.99988 20 3.99988H4C2.9 3.99988 2 4.89988 2 5.99988V17.9999C2 19.0999 2.9 19.9999 4 19.9999H20C21.1 19.9999 22 19.0999 22 17.9999V5.99988ZM20 5.99988L12 10.9899L4 5.99988H20ZM20 17.9999H4V7.99988L12 12.9999L20 7.99988V17.9999Z" fill="#2F3F58"/> </svg>
                                    </span>
                                    <span class="contact-office__text">
                                        <template v-for="(email, idx) in loc.emails" :key="`e-${loc.id}-${idx}`">
                                            <span v-if="idx > 0" class="contact-office__sep" aria-hidden="true">|</span>
                                            <a :href="`mailto:${email}`">{{ email }}</a>
                                        </template>
                                    </span>
                                </li>
                                <li v-if="loc.whatsapp" class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.123 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" fill="#2F3F58"/></svg>
                                    </span>
                                    <span class="contact-office__text">
                                        <a :href="whatsappHref(loc.whatsapp)" rel="noopener noreferrer" target="_blank">{{ loc.whatsapp }}</a>
                                    </span>
                                </li>
                                <li v-if="loc.fax" class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" fill="#2F3F58"/></svg>
                                    </span>
                                    <span class="contact-office__text"><a :href="telHref(loc.fax)">{{ loc.fax }}</a></span>
                                </li>
                            </ul>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>

</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import { asset } from '@/utils/asset';
import { getContactSectionData, getLocationsSectionData } from '@/utils/publicContent';

const { locale, t } = useI18n({ useScope: 'global' });
const router = useRouter();

const contactSection = computed(() => getContactSectionData(locale.value));
const locationsSection = computed(() => getLocationsSectionData(locale.value));
const submitting = ref(false);
const fieldErrors = ref({});
const formBanner = ref('');
const recaptchaToken = ref('');

const contactPhoneInput = ref(null);

const form = reactive({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

const heroImageSrc = computed(() => {
    const url = contactSection.value.heroBackgroundImage;
    return typeof url === 'string' && url.trim() !== '' ? url.trim() : asset('public/images/inner-banner.jpg');
});

const pageHeading = computed(() => t('contact.heroTitle'));
const breadcrumbCurrent = computed(() => t('contact.breadcrumbCurrent'));

const officesColumnTitle = computed(() => locationsSection.value.sectionTitle || t('contact.officeLocations'));

function officeDisplayName(loc) {
    if (loc.title && String(loc.title).trim() !== '') {
        return loc.title;
    }
    if (loc.address && String(loc.address).trim() !== '') {
        return loc.address;
    }
    return t('contact.heroTitle');
}

function mapImageAlt(loc) {
    if (loc.imageAlt && String(loc.imageAlt).trim() !== '') {
        return loc.imageAlt;
    }
    return t('contact.mapAlt', { name: officeDisplayName(loc) });
}

function telHref(raw) {
    const s = String(raw ?? '').trim();
    if (!s) {
        return '#';
    }
    const compact = s.replace(/\s+/g, '');
    return `tel:${compact}`;
}

function whatsappHref(raw) {
    const s = String(raw ?? '').trim();
    if (!s) {
        return '#';
    }
    if (/^https?:\/\//i.test(s)) {
        return s;
    }
    const digits = s.replace(/\D+/g, '');
    return digits !== '' ? `https://wa.me/${digits}` : '#';
}

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

function extractContactPhone() {
    const input = contactPhoneInput.value || document.querySelector('.contact-form .phone_number');
    if (!input || typeof window.intlTelInputGlobals?.getInstance !== 'function') {
        const raw = String(input?.value ?? form.phone ?? '').trim();
        return { dial: '', national: '', full: raw, iso2: '', countryName: '' };
    }

    const iti = window.intlTelInputGlobals.getInstance(input);
    if (!iti) {
        const raw = String(input.value ?? form.phone ?? '').trim();
        return { dial: '', national: '', full: raw, iso2: '', countryName: '' };
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

    return { dial, national, full: String(input.value ?? form.phone ?? '').trim(), iso2, countryName };
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
    const token = await window.grecaptcha.execute(key, { action: 'contact_enquiry_submit' });
    recaptchaToken.value = String(token || '');
    return recaptchaToken.value;
}

function validateContactForm() {
    clearFieldErrors();
    let ok = true;
    if (!form.name.trim()) {
        fieldErrors.value.name = t('contact.form.validationRequired');
        ok = false;
    }
    if (!form.email.trim()) {
        fieldErrors.value.email = t('contact.form.validationRequired');
        ok = false;
    } else if (!EMAIL_RE.test(form.email.trim())) {
        fieldErrors.value.email = t('contact.form.validationEmail');
        ok = false;
    }
    if (!form.subject.trim()) {
        fieldErrors.value.subject = t('contact.form.validationRequired');
        ok = false;
    }
    if (!form.message.trim()) {
        fieldErrors.value.message = t('contact.form.validationRequired');
        ok = false;
    }

    const phone = extractContactPhone();
    if (!phone.national) {
        fieldErrors.value.phone_national = t('contact.form.validationPhone');
        ok = false;
    }
    if (!phone.dial || !/^[1-9]\d{0,4}$/.test(phone.dial)) {
        fieldErrors.value.phone_dial_code = t('contact.form.validationPhone');
        ok = false;
    }
    if (phone.national && !/^\d{6,13}$/.test(phone.national)) {
        fieldErrors.value.phone_national = t('contact.form.validationPhone');
        ok = false;
    }

    return ok;
}

function buildPayload() {
    const phone = extractContactPhone();
    const payload = {
        name: form.name.trim(),
        email: form.email.trim(),
        phone: phone.full,
        subject: form.subject.trim(),
        message: form.message.trim(),
        phone_dial_code: phone.dial,
        phone_national: phone.national,
        phone_country_iso2: phone.iso2,
        phone_country_name: phone.countryName,
    };
    if (recaptchaToken.value) payload.recaptcha_token = recaptchaToken.value;
    return payload;
}

async function submitContactForm() {
    if (!validateContactForm()) return;
    submitting.value = true;
    try {
        await resolveRecaptchaToken();
        await axios.post('/contact-enquiry', buildPayload(), {
            withCredentials: true,
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'X-XSRF-TOKEN': xsrfToken(),
                Accept: 'application/json',
            },
        });
        Object.assign(form, { name: '', email: '', phone: '', subject: '', message: '' });
        const telInput = contactPhoneInput.value;
        const iti = telInput && window.intlTelInputGlobals?.getInstance?.(telInput);
        if (iti) {
            try {
                iti.setNumber('');
            } catch (_e) {
                /* noop */
            }
        }
        await router.push({ name: 'thank-you' });
    } catch (e) {
        if (e.message === 'recaptcha-not-loaded') {
            fieldErrors.value.recaptcha_token = t('contact.form.validationRecaptcha');
            return;
        }
        if (e.response?.status === 422) {
            applyServerErrors(e.response.data.errors);
            if (!Object.keys(fieldErrors.value).length && e.response.data?.message) {
                formBanner.value = String(e.response.data.message);
            }
        } else {
            formBanner.value = t('contact.form.submitError');
        }
    } finally {
        submitting.value = false;
    }
}

function destroyContactIntlPhone() {
    const input = contactPhoneInput.value;
    if (!input || typeof window.intlTelInputGlobals?.getInstance !== 'function') {
        return;
    }
    const iti = window.intlTelInputGlobals.getInstance(input);
    if (iti) {
        try {
            iti.destroy();
        } catch (_e) {
            /* noop */
        }
    }
}

function initContactIntlPhone() {
    if (typeof window.dreInitContactFormPhone === 'function') {
        window.dreInitContactFormPhone();
    }
}

onMounted(() => {
    nextTick(() => {
        requestAnimationFrame(() => {
            initContactIntlPhone();
        });
    });
});

watch(locale, () => {
    nextTick(() => {
        requestAnimationFrame(() => {
            initContactIntlPhone();
        });
    });
});

onBeforeUnmount(() => {
    destroyContactIntlPhone();
});
</script>
