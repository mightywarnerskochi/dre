<template>
    <section class="banner banner--page banner--page--listing book-viewing-page-banner position-relative" :aria-label="t('bookViewing.pageHeaderAria')">
        <div class="banner--page__bg">
            <picture>
                <img :src="asset('public/images/inner-banner.jpg')" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">{{ t('bookViewing.title') }}</h1>
                <ol class="breadcrumb-minimal" :aria-label="t('bookViewing.breadcrumbAria')">
                    <li><a href="/">{{ t('bookViewing.home') }}</a></li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">{{ t('bookViewing.title') }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="book-viewing-form commonPadding-120" aria-labelledby="book-viewing-title">
        <div class="container-ctn">
            <h2 id="book-viewing-title" class="sr-only">{{ t('bookViewing.formTitle') }}</h2>
            <div class="book-viewing-form__inner">
                <div class="book-viewing-form__panel">
                    <form autocomplete="on" @submit.prevent="submitBookViewing">
                        <div class="book-viewing-form__grid">
                            <div class="book-viewing-form__col">
                                <input v-model.trim="form.name" class="book-viewing-form__field" :class="{ 'is-invalid': fieldErrors.name }" type="text" name="name" :placeholder="t('bookViewing.name')" autocomplete="name">
                                <div v-if="fieldErrors.name" class="invalid-feedback d-block">{{ fieldErrors.name }}</div>
                            </div>
                            <div class="book-viewing-form__col">
                                <input v-model.trim="form.email" class="book-viewing-form__field" :class="{ 'is-invalid': fieldErrors.email }" type="email" name="email" :placeholder="t('bookViewing.email')" autocomplete="email">
                                <div v-if="fieldErrors.email" class="invalid-feedback d-block">{{ fieldErrors.email }}</div>
                            </div>
                            <div class="book-viewing-form__col">
                                <input
                                    ref="phoneInputEl"
                                    class="book-viewing-form__field phone_number"
                                    :class="{ 'is-invalid': fieldErrors.phone || fieldErrors.phone_national || fieldErrors.phone_dial_code }"
                                    type="tel"
                                    name="phone"
                                    :placeholder="t('bookViewing.phone')"
                                    autocomplete="tel"
                                >
                                <div v-if="fieldErrors.phone || fieldErrors.phone_national || fieldErrors.phone_dial_code" class="invalid-feedback d-block">
                                    {{ fieldErrors.phone || fieldErrors.phone_national || fieldErrors.phone_dial_code }}
                                </div>
                            </div>

                            <div class="book-viewing-form__col d-flex justify-content-between ">
                            <div class="book-viewing-form__field-wrap book-viewing-form__field-wrap--date">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M16.5 5V3M7.5 5V3M3.25 8H20.75M3 10.044C3 7.929 3 6.871 3.436 6.063C3.83025 5.34231 4.44199 4.7645 5.184 4.412C6.04 4 7.16 4 9.4 4H14.6C16.84 4 17.96 4 18.816 4.412C19.569 4.774 20.18 5.352 20.564 6.062C21 6.872 21 7.93 21 10.045V14.957C21 17.072 21 18.13 20.564 18.938C20.1698 19.6587 19.558 20.2365 18.816 20.589C17.96 21 16.84 21 14.6 21H9.4C7.16 21 6.04 21 5.184 20.588C4.44214 20.2358 3.83041 19.6583 3.436 18.938C3 18.128 3 17.07 3 14.955V10.044Z" stroke="#4B535D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                <input v-model="form.date" class="book-viewing-form__field" :class="{ 'is-invalid': fieldErrors.date }" type="text" name="date" :placeholder="t('bookViewing.date')" onfocus="this.type='date'" onblur="if(!this.value){this.type='text'}">
                                <div v-if="fieldErrors.date" class="invalid-feedback d-block">{{ fieldErrors.date }}</div>
                            </div>

                            <div class="book-viewing-form__field-wrap book-viewing-form__field-wrap--time">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M15.098 12.634L13 11.423V7C13 6.73478 12.8946 6.48043 12.7071 6.29289C12.5196 6.10536 12.2652 6 12 6C11.7348 6 11.4804 6.10536 11.2929 6.29289C11.1054 6.48043 11 6.73478 11 7V12C11 12.1755 11.0462 12.348 11.134 12.5C11.2218 12.652 11.348 12.7782 11.5 12.866L14.098 14.366C14.2118 14.4327 14.3376 14.4762 14.4683 14.4941C14.5989 14.512 14.7319 14.5038 14.8594 14.4701C14.9869 14.4364 15.1064 14.3778 15.2112 14.2977C15.3159 14.2176 15.4038 14.1175 15.4698 14.0033C15.5357 13.8891 15.5784 13.763 15.5954 13.6322C15.6124 13.5014 15.6034 13.3686 15.5688 13.2413C15.5343 13.114 15.4749 12.9949 15.3941 12.8906C15.3133 12.7864 15.2127 12.6992 15.098 12.634ZM12 2C10.0222 2 8.08879 2.58649 6.4443 3.6853C4.79981 4.78412 3.51809 6.3459 2.76121 8.17317C2.00433 10.0004 1.8063 12.0111 2.19215 13.9509C2.578 15.8907 3.53041 17.6725 4.92894 19.0711C6.32746 20.4696 8.10929 21.422 10.0491 21.8079C11.9889 22.1937 13.9996 21.9957 15.8268 21.2388C17.6541 20.4819 19.2159 19.2002 20.3147 17.5557C21.4135 15.9112 22 13.9778 22 12C21.9974 9.34865 20.9429 6.80665 19.0681 4.93186C17.1934 3.05707 14.6514 2.00265 12 2ZM12 20C10.4178 20 8.87104 19.5308 7.55544 18.6518C6.23985 17.7727 5.21447 16.5233 4.60897 15.0615C4.00347 13.5997 3.84504 11.9911 4.15372 10.4393C4.4624 8.88743 5.22433 7.46197 6.34315 6.34315C7.46197 5.22433 8.88743 4.4624 10.4393 4.15372C11.9911 3.84504 13.5997 4.00346 15.0615 4.60896C16.5233 5.21447 17.7727 6.23984 18.6518 7.55544C19.5308 8.87103 20 10.4177 20 12C19.9974 14.1209 19.1537 16.1542 17.6539 17.6539C16.1542 19.1536 14.1209 19.9974 12 20Z" fill="#4B535D"/>
                            </svg>
                                <select v-model="form.time" class="book-viewing-form__field" :class="{ 'is-invalid': fieldErrors.time }" name="time" :aria-label="t('bookViewing.time')">
                                    <option value="" selected disabled>{{ t('bookViewing.time') }}</option>
                                    <option value="09:00">09:00 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="12:00">12:00 PM</option>
                                    <option value="13:00">01:00 PM</option>
                                    <option value="14:00">02:00 PM</option>
                                    <option value="15:00">03:00 PM</option>
                                    <option value="16:00">04:00 PM</option>
                                    <option value="17:00">05:00 PM</option>
                                    <option value="18:00">06:00 PM</option>
                                </select>
                                <span class="book-viewing-form__select-arrow" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M3.5 5.5L7 9L10.5 5.5" stroke="#4B535D" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                            <div v-if="fieldErrors.time" class="invalid-feedback d-block">{{ fieldErrors.time }}</div>
                            </div>

                            <div class="book-viewing-form__textarea-wrap w-100">
                                <textarea v-model.trim="form.message" class="book-viewing-form__field book-viewing-form__textarea" :class="{ 'is-invalid': fieldErrors.message }" name="message" :placeholder="t('bookViewing.message')"></textarea>
                                <div v-if="fieldErrors.message" class="invalid-feedback d-block">{{ fieldErrors.message }}</div>
                            </div>
                            <div v-if="fieldErrors.recaptcha_token || formBanner" class="w-100">
                                <div class="invalid-feedback d-block">{{ fieldErrors.recaptcha_token || formBanner }}</div>
                            </div>
                        </div>
                        <div class="book-viewing-form__actions">
                            <button type="submit" class="btn btn-theme book-viewing-form__submit" :disabled="submitting">{{ submitting ? t('bookViewing.pleaseWait') : t('bookViewing.submit') }}</button>
                        </div>
                    </form>
                </div>

                <figure class="book-viewing-form__media">
                    <img :src="asset('public/images/book-a-view.jpg')" alt="Residential community view in Dubai" width="720" height="620" loading="lazy">
                </figure>
            </div>
        </div>
    </section>

</template>

<script setup>
import { nextTick, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import { asset } from '@/utils/asset';

const router = useRouter();
const { t } = useI18n({ useScope: 'global' });
const phoneInputEl = ref(null);
const submitting = ref(false);
const fieldErrors = ref({});
const formBanner = ref('');
const recaptchaToken = ref('');

const form = reactive({
    name: '',
    email: '',
    phone: '',
    date: '',
    time: '',
    message: '',
});

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function xsrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
    if (!match?.[1]) return '';
    try {
        return decodeURIComponent(match[1]);
    } catch (_error) {
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

function extractPhone() {
    const input = phoneInputEl.value;
    if (!input || typeof window.intlTelInputGlobals?.getInstance !== 'function') {
        const raw = String(form.phone ?? '').trim();
        const rawDigits = raw.replace(/\D/g, '');
        return { dial: '', national: rawDigits, full: raw, iso2: '', countryName: '' };
    }
    const iti = window.intlTelInputGlobals.getInstance(input);
    if (!iti) {
        const raw = String(input.value ?? form.phone ?? '').trim();
        const rawDigits = raw.replace(/\D/g, '');
        return { dial: '', national: rawDigits, full: raw, iso2: '', countryName: '' };
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

function validateForm() {
    clearFieldErrors();
    let ok = true;
    if (!form.name.trim()) {
        fieldErrors.value.name = t('bookViewing.validationRequired');
        ok = false;
    }
    if (!form.email.trim()) {
        fieldErrors.value.email = t('bookViewing.validationRequired');
        ok = false;
    } else if (!EMAIL_RE.test(form.email.trim())) {
        fieldErrors.value.email = t('bookViewing.validationEmail');
        ok = false;
    }
    if (!form.date.trim()) {
        fieldErrors.value.date = t('bookViewing.validationRequired');
        ok = false;
    }
    if (!form.time.trim()) {
        fieldErrors.value.time = t('bookViewing.validationRequired');
        ok = false;
    }
    if (!form.message.trim()) {
        fieldErrors.value.message = t('bookViewing.validationRequired');
        ok = false;
    }
    const phone = extractPhone();
    if (!phone.dial || !/^[1-9]\d{0,4}$/.test(phone.dial)) {
        fieldErrors.value.phone_dial_code = t('bookViewing.validationPhoneDial');
        ok = false;
    }
    if (!phone.national || !/^\d{6,13}$/.test(phone.national)) {
        fieldErrors.value.phone_national = t('bookViewing.validationPhoneNational');
        ok = false;
    }

    return ok;
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
    const token = await window.grecaptcha.execute(key, { action: 'book_viewing_submit' });
    recaptchaToken.value = String(token || '');
    return recaptchaToken.value;
}

function buildPayload() {
    const phone = extractPhone();
    const payload = {
        name: form.name.trim(),
        email: form.email.trim(),
        phone: phone.full,
        phone_dial_code: phone.dial,
        phone_national: phone.national,
        phone_country_iso2: phone.iso2,
        phone_country_name: phone.countryName,
        date: form.date,
        time: form.time,
        message: form.message.trim(),
        page_source: 'Book a Viewing',
        page_url: String(window.location.href || '').trim(),
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
    form.date = '';
    form.time = '';
    form.message = '';
    clearFieldErrors();
    const input = phoneInputEl.value;
    if (input) {
        input.value = '';
    }
}

async function submitBookViewing() {
    if (!validateForm()) return;
    submitting.value = true;
    try {
        await resolveRecaptchaToken();
        await axios.post('/book-viewing-enquiry', buildPayload(), {
            withCredentials: true,
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'X-XSRF-TOKEN': xsrfToken(),
                Accept: 'application/json',
            },
        });
        resetForm();
        await router.push({ name: 'thank-you' });
    } catch (e) {
        if (e.message === 'recaptcha-not-loaded') {
            fieldErrors.value.recaptcha_token = t('bookViewing.validationRecaptcha');
            return;
        }
        if (e.response?.status === 422) {
            applyServerErrors(e.response.data.errors);
            if (!Object.keys(fieldErrors.value).length && e.response.data?.message) {
                formBanner.value = String(e.response.data.message);
            }
        } else {
            formBanner.value = t('bookViewing.submitError');
        }
    } finally {
        submitting.value = false;
    }
}

onMounted(async () => {
    await nextTick();
    if (typeof window.dreInitBookViewingPhone === 'function') {
        window.dreInitBookViewingPhone();
    }
});

onBeforeUnmount(() => {
    clearFieldErrors();
});
</script>

<style scoped>
.book-viewing-page-banner {
    min-height: 260px;
}

.book-viewing-page-banner .banner--page__content {
    position: relative;
    z-index: 2;
}
</style>
