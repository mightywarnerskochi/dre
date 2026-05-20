<template v-if="detail">
<section class="banner banner--page banner--page--listing position-relative" :aria-label="t('careers.pageHeaderAria')">
        <div class="banner--page__bg">
            <picture>
                <img :src="heroImageSrc" alt="Careers banner" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">{{ detail.vacancy.title }}</h1>
                <ol class="breadcrumb-minimal" :aria-label="t('careers.breadcrumbAria')">
                    <li>
                        <RouterLink to="/" :aria-label="t('careers.homeAria')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </RouterLink>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__sep">
                        <RouterLink :to="{ name: 'career' }">{{ t('careers.detailBreadcrumbCareers') }}</RouterLink> /
                    </li>
                    <li class="breadcrumb-minimal__current" aria-current="page">{{ detail.vacancy.title }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="career-detail commonPadding-120" :aria-labelledby="'career-detail-title'">
        <div class="container-ctn">
            <div class="career-detail__head">
                <div>
                    <h2 id="career-detail-title">
                        {{ detail.vacancy.title }}
                        <span v-if="detail.vacancy.departmentLabel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true"><circle cx="5" cy="5.00012" r="5" fill="#A8A8A8"/></svg>
                            {{ detail.vacancy.departmentLabel }}
                        </span>
                    </h2>
                    <p v-if="detail.vacancy.shortDescription">{{ detail.vacancy.shortDescription }}</p>
                    <ul class="career-detail__meta">
                        <li v-if="detail.vacancy.jobTypeLabel">
                            <span class="visually-hidden">{{ t('careers.detailMetaJobType') }}: </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M9 15.75C12.7279 15.75 15.75 12.7279 15.75 9C15.75 5.27208 12.7279 2.25 9 2.25C5.27208 2.25 2.25 5.27208 2.25 9C2.25 12.7279 5.27208 15.75 9 15.75Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.25 6V9.75H12" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ detail.vacancy.jobTypeLabel }}
                        </li>
                        <li v-if="detail.vacancy.baseLabel">
                            <span class="visually-hidden">{{ t('careers.detailMetaBase') }}: </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12 5.25C12 3.8355 12 3.129 11.5605 2.6895C11.121 2.25 10.4145 2.25 9 2.25C7.5855 2.25 6.879 2.25 6.4395 2.6895C6 3.129 6 3.8355 6 5.25M1.5 10.5C1.5 8.39325 1.5 7.34025 2.0055 6.5835C2.22443 6.25579 2.50579 5.97443 2.8335 5.7555C3.59025 5.25 4.6425 5.25 6.75 5.25H11.25C13.3568 5.25 14.4098 5.25 15.1665 5.7555C15.4942 5.97443 15.7756 6.25579 15.9945 6.5835C16.5 7.34025 16.5 8.3925 16.5 10.5C16.5 12.6075 16.5 13.6598 15.9945 14.4165C15.7756 14.7442 15.4942 15.0256 15.1665 15.2445C14.4098 15.75 13.3575 15.75 11.25 15.75H6.75C4.64325 15.75 3.59025 15.75 2.8335 15.2445C2.50579 15.0256 2.22443 14.7442 2.0055 14.4165C1.5 13.6598 1.5 12.6075 1.5 10.5Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4.5 8.25L4.989 8.4015C7.60333 9.20039 10.3967 9.20039 13.011 8.4015L13.5 8.25M9 9V10.5" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ detail.vacancy.baseLabel }}
                        </li>
                        <li v-if="detail.vacancy.publishedDate">
                            <span class="visually-hidden">{{ t('careers.detailPublished') }}: </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12.375 3.75V2.25M5.625 3.75V2.25M2.4375 6H15.5625M2.25 7.533C2.25 5.94675 2.25 5.15325 2.577 4.54725C2.87268 4.00673 3.3315 3.57338 3.888 3.309C4.53 3 5.37 3 7.05 3H10.95C12.63 3 13.47 3 14.112 3.309C14.6768 3.5805 15.135 4.014 15.423 4.5465C15.75 5.154 15.75 5.9475 15.75 7.53375V11.2177C15.75 12.804 15.75 13.5975 15.423 14.2035C15.1273 14.744 14.6685 15.1774 14.112 15.4418C13.47 15.75 12.63 15.75 10.95 15.75H7.05C5.37 15.75 4.53 15.75 3.888 15.441C3.33161 15.1768 2.87281 14.7437 2.577 14.2035C2.25 13.596 2.25 12.8025 2.25 11.2162V7.533Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ formatDate(detail.vacancy.publishedDate) }}
                        </li>
                        <li v-if="detail.vacancy.locationLine">
                            <span class="visually-hidden">{{ t('careers.detailLocation') }}: </span>
                            <img :src="detail.vacancy.flagImage || fallbackFlagIcon" :alt="detail.vacancy.flagAlt || 'Career location'" width="16" height="16" loading="lazy" @error="onLocationIconError">
                            {{ detail.vacancy.locationLine }}
                        </li>
                    </ul>
                </div>
                <button type="button" class="career-detail__apply" data-bs-toggle="modal" data-bs-target="#careerApplyModal">
                    {{ t('careers.applyNow') }}
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none" aria-hidden="true">
                        <path d="M9.28234 17.1303L15.8309 7.98271M15.8309 7.98271L9.71616 8.79363M15.8309 7.98271L17.0342 14.0325" stroke="currentColor" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            <div class="career-detail__body">
                <template v-if="detail.vacancy.aboutHtml">
                    <h3>{{ t('careers.sectionAbout') }}</h3>
                    <div class="career-detail__body-rich mb-4" v-html="detail.vacancy.aboutHtml"></div>
                </template>

                <template v-if="detail.vacancy.responsibilitiesHtml">
                    <h3>{{ t('careers.sectionResponsibilities') }}</h3>
                    <div class="career-detail__body-rich mb-4" v-html="detail.vacancy.responsibilitiesHtml"></div>
                </template>

                <template v-if="detail.vacancy.requirementsHtml">
                    <h3>{{ t('careers.sectionRequirements') }}</h3>
                    <div class="career-detail__body-rich mb-4" v-html="detail.vacancy.requirementsHtml"></div>
                </template>

                <template v-if="detail.vacancy.joinTeamHtml">
                    <div class="join-the-team">
                         <h3>{{ t('careers.sectionJoin') }}</h3>
                    <div class="career-detail__body-rich mb-4" v-html="detail.vacancy.joinTeamHtml"></div>
                    </div>
                </template>

                <button type="button" class="career-detail__apply career-detail__apply--bottom" data-bs-toggle="modal" data-bs-target="#careerApplyModal">
                    {{ t('careers.applyNow') }}
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none" aria-hidden="true">
                        <path d="M9.28234 17.1303L15.8309 7.98271M15.8309 7.98271L9.71616 8.79363M15.8309 7.98271L17.0342 14.0325" stroke="currentColor" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <div class="modal fade careerApplyModal" id="careerApplyModal" tabindex="-1" :aria-labelledby="'careerApplyModalLabel'" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="careerApplyModalLabel">{{ t('careers.modalCvHeadline') }}</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" :aria-label="t('careers.modalClose')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
  <path d="M10.3418 21.6555L21.6565 10.3408M21.6565 21.6555L10.3418 10.3408" stroke="#4F5762" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="formBanner" class="alert mb-3" :class="formBanner.type === 'success' ? 'alert-success' : 'alert-danger'" role="alert">
                        {{ formBanner.text }}
                    </div>
                    <form
                        class="career-apply-form"
                        enctype="multipart/form-data"
                        novalidate
                        @submit.prevent="submitApplication"
                    >
                        <div class="career-apply-form__grid">
                            <div class="form-group">
                                <label class="sr-only" for="career-name">{{ t('careers.form.name') }}</label>
                                <input
                                    id="career-name"
                                    v-model.trim="form.firstName"
                                    type="text"
                                    name="first_name"
                                    autocomplete="given-name"
                                    :placeholder="t('careers.form.name')"
                                    :class="{ 'is-invalid': fieldErrors.first_name }"
                                >
                                <div v-if="fieldErrors.first_name" class="invalid-feedback d-block">{{ fieldErrors.first_name }}</div>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="career-last-name">{{ t('careers.form.lastName') }}</label>
                                <input
                                    id="career-last-name"
                                    v-model.trim="form.lastName"
                                    type="text"
                                    name="last_name"
                                    autocomplete="family-name"
                                    :placeholder="t('careers.form.lastName')"
                                    :class="{ 'is-invalid': fieldErrors.last_name }"
                                >
                                <div v-if="fieldErrors.last_name" class="invalid-feedback d-block">{{ fieldErrors.last_name }}</div>
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="career-email">{{ t('careers.form.email') }}</label>
                                <input
                                    id="career-email"
                                    v-model.trim="form.email"
                                    type="email"
                                    name="email"
                                    autocomplete="email"
                                    :placeholder="t('careers.form.email')"
                                    :class="{ 'is-invalid': fieldErrors.email }"
                                >
                                <div v-if="fieldErrors.email" class="invalid-feedback d-block">{{ fieldErrors.email }}</div>
                            </div>
                            <div class="form-group career-apply-form__phone-wrap">
                                <label class="sr-only" for="career-phone">{{ t('careers.form.phone') }}</label>
                                <input
                                    id="career-phone"
                                    type="tel"
                                    class="phone_number"
                                    name="phone"
                                    autocomplete="tel"
                                    :placeholder="t('careers.form.phone')"
                                    :class="{ 'is-invalid': fieldErrors.phone_national || fieldErrors.phone_dial_code }"
                                >
                                <div v-if="fieldErrors.phone_national || fieldErrors.phone_dial_code" class="invalid-feedback d-block">
                                    {{ fieldErrors.phone_national || fieldErrors.phone_dial_code }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="career-country">{{ t('careers.form.country') }}</label>
                                <input
                                    id="career-country"
                                    v-model.trim="form.country"
                                    type="text"
                                    name="country"
                                    :placeholder="t('careers.form.country')"
                                    :class="{ 'is-invalid': fieldErrors.country }"
                                >
                                <div v-if="fieldErrors.country" class="invalid-feedback d-block">{{ fieldErrors.country }}</div>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="career-state">{{ t('careers.form.state') }}</label>
                                <input
                                    id="career-state"
                                    v-model.trim="form.state"
                                    type="text"
                                    name="state"
                                    :placeholder="t('careers.form.state')"
                                    :class="{ 'is-invalid': fieldErrors.state }"
                                >
                                <div v-if="fieldErrors.state" class="invalid-feedback d-block">{{ fieldErrors.state }}</div>
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="career-place">{{ t('careers.form.place') }}</label>
                                <input
                                    id="career-place"
                                    v-model.trim="form.place"
                                    type="text"
                                    name="place"
                                    :placeholder="t('careers.form.place')"
                                    :class="{ 'is-invalid': fieldErrors.place }"
                                >
                                <div v-if="fieldErrors.place" class="invalid-feedback d-block">{{ fieldErrors.place }}</div>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="career-apply-for">{{ t('careers.form.applyFor') }}</label>
                                <select
                                    id="career-apply-for"
                                    v-model="form.applyFor"
                                    name="apply_for"
                                    :class="{ 'is-invalid': fieldErrors.apply_for }"
                                >
                                    <option value="">{{ t('careers.form.applyFor') }}</option>
                                    <option :value="detail.vacancy.title">{{ detail.vacancy.title }}</option>
                                </select>
                                <div v-if="fieldErrors.apply_for" class="invalid-feedback d-block">{{ fieldErrors.apply_for }}</div>
                            </div>

                            <div class="form-group">
                                <label class="sr-only" for="career-designation">{{ t('careers.form.designation') }}</label>
                                <select
                                    id="career-designation"
                                    v-model="form.designation"
                                    name="designation"
                                    :class="{ 'is-invalid': fieldErrors.designation }"
                                >
                                    <option value="">{{ t('careers.form.designation') }}</option>
                                    <option value="mid-level">{{ t('careers.form.designationMid') }}</option>
                                    <option value="senior">{{ t('careers.form.designationSenior') }}</option>
                                </select>
                                <div v-if="fieldErrors.designation" class="invalid-feedback d-block">{{ fieldErrors.designation }}</div>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="career-experience">{{ t('careers.form.experience') }}</label>
                                <input
                                    id="career-experience"
                                    v-model.trim="form.experience"
                                    type="text"
                                    name="experience"
                                    :placeholder="t('careers.form.experience')"
                                    :class="{ 'is-invalid': fieldErrors.experience }"
                                >
                                <div v-if="fieldErrors.experience" class="invalid-feedback d-block">{{ fieldErrors.experience }}</div>
                            </div>

                            <div class="form-group form-group--full">
                                <label class="sr-only" for="career-additional">{{ t('careers.form.additionalInfo') }}</label>
                                <textarea
                                    id="career-additional"
                                    v-model.trim="additionalInfo"
                                    name="additional_information"
                                    :placeholder="t('careers.form.additionalInfo')"
                                    :class="{ 'is-invalid': fieldErrors.additional_information }"
                                ></textarea>
                                <div class="career-apply-form__counter">{{ t('careers.form.counterWords', { n: additionalWords }) }}</div>
                                <div v-if="fieldErrors.additional_information" class="invalid-feedback d-block">{{ fieldErrors.additional_information }}</div>
                            </div>
                        </div>

                        <div class="career-apply-form__attach">
                            <input
                                ref="attachmentRef"
                                type="file"
                                id="career-attachment"
                                name="attachment"
                                class="visually-hidden"
                                accept=".pdf,.doc,.docx,.ps,.jpg,.jpeg,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/postscript,image/jpeg"
                                :class="{ 'is-invalid': fieldErrors.attachment }"
                                @change="onFileChange"
                            >
                            <label for="career-attachment" class="career-apply-form__attach-label d-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M10 9V14C10 14.5304 10.2107 15.0391 10.5858 15.4142C10.9609 15.7893 11.4696 16 12 16C12.5304 16 13.0391 15.7893 13.4142 15.4142C13.7893 15.0391 14 14.5304 14 14V7C14 5.93913 13.5786 4.92172 12.8284 4.17157C12.0783 3.42143 11.0609 3 10 3C8.93913 3 7.92172 3.42143 7.17157 4.17157C6.42143 4.92172 6 5.93913 6 7V15C6 16.5913 6.63214 18.1174 7.75736 19.2426C8.88258 20.3679 10.4087 21 12 21C13.5913 21 15.1174 20.3679 16.2426 19.2426C17.3679 18.1174 18 16.5913 18 15V5" stroke="#0A1119" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="career-apply-form__attach-text">
                                    <span class="career-apply-form__attach-prompt">{{ t('careers.form.attachPrompt') }}<span class="career-apply-form__attach-required" aria-hidden="true">*</span></span>
                                    <span class="career-apply-form__attach-filename" id="career-attachment-filename" hidden></span>
                                    <small>{{ t('careers.form.attachHint') }}</small>
                                </div>
                            </label>
                            <div v-if="fieldErrors.attachment" class="invalid-feedback d-block">{{ fieldErrors.attachment }}</div>
                        </div>

                        <label class="career-apply-form__consent">
                            <input id="career-privacy" v-model="privacyAccepted" type="checkbox" name="privacy" value="1">
                            <span>{{ t('careers.form.consentPrefix') }}<RouterLink :to="{ name: 'privacy-policy' }">{{ t('careers.form.privacyLink') }}</RouterLink>{{ t('careers.form.consentSuffix') }}</span>
                        </label>
                        <div v-if="fieldErrors.privacy" class="invalid-feedback d-block mb-2">{{ fieldErrors.privacy }}</div>
                        <input type="hidden" name="recaptcha_token" :value="recaptchaToken">
                        <div v-if="fieldErrors.recaptcha_token" class="invalid-feedback d-block mb-2">{{ fieldErrors.recaptcha_token }}</div>

                        <div class="career-apply-form__actions">
                            <button type="submit" class="btn btn-theme" :disabled="submitting" :aria-busy="submitting ? 'true' : 'false'">
                                {{ submitting ? t('careers.form.pleaseWait') : t('careers.form.submit') }}
                            </button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ t('careers.form.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import { computed, reactive, ref, watch, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import { asset } from '@/utils/asset';
import { getCareerDetailBySlug, getCareersPublicData } from '@/utils/publicContent';
import { applyGlobalImageAltFallback, applySeoPayload } from '@/utils/seo';

const route = useRoute();
const router = useRouter();
const { locale, t } = useI18n({ useScope: 'global' });

const attachmentRef = ref(null);
const submitting = ref(false);
const privacyAccepted = ref(false);
const additionalInfo = ref('');
const formBanner = ref(null);
const recaptchaToken = ref('');
const successCloseTimer = ref(null);

const fieldErrors = ref({});
const fallbackFlagIcon = asset('public/images/flag.png');

const emptyForm = () => ({
    firstName: '',
    lastName: '',
    email: '',
    country: '',
    state: '',
    place: '',
    applyFor: '',
    designation: '',
    experience: '',
});

const form = reactive(emptyForm());

function countWords(text) {
    const plain = String(text ?? '').replace(/<[^>]*>/g, ' ').trim();
    if (!plain) return 0;
    return plain.split(/\s+/).filter(Boolean).length;
}

const additionalWords = computed(() => countWords(additionalInfo.value));

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

function extractDialAndNationalDigits() {
    const input = document.querySelector('#careerApplyModal .phone_number');
    if (!input || typeof window.intlTelInputGlobals?.getInstance !== 'function') {
        return { dial: '', national: '', okDial: false, okNational: false };
    }
    const iti = window.intlTelInputGlobals.getInstance(input);
    if (!iti) {
        return { dial: '', national: '', okDial: false, okNational: false };
    }
    const dial = String(iti.getSelectedCountryData()?.dialCode ?? '').replace(/\D/g, '');
    const okDial = /^[1-9]\d{0,4}$/.test(dial);
    let national = '';

    const e164Digits = String(iti.getNumber() ?? '').replace(/\D/g, '');
    if (e164Digits && dial && e164Digits.startsWith(dial)) {
        national = e164Digits.slice(dial.length);
    }

    if (!national.length) {
        const rawDigits = String(input.value ?? '').replace(/\D/g, '');
        if (rawDigits.startsWith(dial)) {
            national = rawDigits.slice(dial.length);
        } else if (/^\d{6,13}$/.test(rawDigits)) {
            national = rawDigits;
        }
    }

    const okNational = /^\d{6,13}$/.test(national);
    return { dial, national, okDial, okNational };
}

function syncPhoneDialCodePrefix() {
    const input = document.querySelector('#careerApplyModal .phone_number');
    const iti = window.intlTelInputGlobals?.getInstance?.(input);
    if (!input || !iti) return;
    const dial = String(iti.getSelectedCountryData()?.dialCode ?? '').replace(/\D/g, '');
    if (!dial) return;
    const raw = String(input.value ?? '').trim();
    const digits = raw.replace(/\D/g, '');
    if (!raw || raw === '+' || !raw.startsWith('+')) {
        iti.setNumber(`+${dial}`);
        return;
    }
    const repeatedDialOnly =
        digits.length >= dial.length * 2
        && digits.length % dial.length === 0
        && new RegExp(`^(?:${dial})+$`).test(digits);
    if (repeatedDialOnly) {
        iti.setNumber(`+${dial}`);
    }
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
    const token = await window.grecaptcha.execute(key, { action: 'career_application_submit' });
    recaptchaToken.value = String(token || '');
    return recaptchaToken.value;
}

function clearFieldErrors() {
    fieldErrors.value = {};
}

function applyServerErrors(errors) {
    clearFieldErrors();
    if (!errors || typeof errors !== 'object') return;
    Object.entries(errors).forEach(([key, msgs]) => {
        const msg = Array.isArray(msgs) ? msgs[0] : msgs;
        if (msg) fieldErrors.value[key] = String(msg);
    });
}

function resetCareerForm(presetApplyFor) {
    Object.assign(form, emptyForm(), { applyFor: presetApplyFor ?? '' });
    additionalInfo.value = '';
    privacyAccepted.value = false;
    if (attachmentRef.value) attachmentRef.value.value = '';
    recaptchaToken.value = '';
    const nameEl = document.getElementById('career-attachment-filename');
    if (nameEl) {
        nameEl.textContent = '';
        nameEl.setAttribute('hidden', '');
    }
    clearFieldErrors();
    const phoneEl = document.querySelector('#careerApplyModal .phone_number');
    try {
        const iti = window.intlTelInputGlobals?.getInstance?.(phoneEl);
        if (iti) iti.setNumber('');
    } catch (_) {
        /**/
    }
}

function dismissCareerModal() {
    const el = document.getElementById('careerApplyModal');
    if (!el || typeof window.bootstrap?.Modal?.getInstance !== 'function') return;
    const inst = window.bootstrap.Modal.getInstance(el);
    if (inst) inst.hide();
}

function validateClient() {
    clearFieldErrors();
    let ok = true;
    const req = (field, msgKey) => {
        if (!String(form[field] ?? '').trim()) {
            fieldErrors.value[field] = t(msgKey);
            ok = false;
        }
    };

    req('firstName', 'careers.form.validationRequired');
    req('lastName', 'careers.form.validationRequired');
    req('country', 'careers.form.validationRequiredSelect');
    req('state', 'careers.form.validationRequiredSelect');
    req('place', 'careers.form.validationRequired');
    req('applyFor', 'careers.form.validationRequiredSelect');
    req('designation', 'careers.form.validationRequiredSelect');
    req('experience', 'careers.form.validationRequired');

    const em = form.email.trim();
    if (!em) {
        fieldErrors.value.email = t('careers.form.validationRequired');
        ok = false;
    } else if (!EMAIL_RE.test(em)) {
        fieldErrors.value.email = t('careers.form.validationEmail');
        ok = false;
    }

    const { dial, national, okDial, okNational } = extractDialAndNationalDigits();
    if (!okDial) {
        fieldErrors.value.phone_dial_code = t('careers.form.validationPhoneDial');
        ok = false;
    }
    if (!okNational) {
        fieldErrors.value.phone_national = t('careers.form.validationPhoneNational');
        ok = false;
    }

    if (additionalWords.value > 1000) {
        fieldErrors.value.additional_information = t('careers.form.validationWordsMax');
        ok = false;
    }

    const file = attachmentRef.value?.files?.[0];
    if (!file) {
        fieldErrors.value.attachment = t('careers.form.validationAttachment');
        ok = false;
    } else if (file.size > 10240 * 1024) {
        fieldErrors.value.attachment = t('careers.form.validationAttachmentSize');
        ok = false;
    }

    if (!privacyAccepted.value) {
        fieldErrors.value.privacy = t('careers.form.validationPrivacy');
        ok = false;
    }

    return ok;
}

function buildFormData() {
    const { dial, national } = extractDialAndNationalDigits();
    const fd = new FormData();
    fd.append('first_name', form.firstName.trim());
    fd.append('last_name', form.lastName.trim());
    fd.append('email', form.email.trim());
    fd.append('phone_dial_code', dial);
    fd.append('phone_national', national);
    fd.append('country', form.country);
    fd.append('state', form.state);
    fd.append('place', form.place.trim());
    fd.append('apply_for', form.applyFor);
    fd.append('designation', form.designation);
    fd.append('experience', form.experience.trim());
    if (additionalInfo.value.trim() !== '') {
        fd.append('additional_information', additionalInfo.value.trim());
    }
    fd.append('privacy', privacyAccepted.value ? '1' : '0');
    if (recaptchaToken.value) {
        fd.append('recaptcha_token', recaptchaToken.value);
    }

    const s = slug.value;
    if (s) fd.append('career_slug', s);

    const file = attachmentRef.value?.files?.[0];
    if (file) fd.append('attachment', file);

    return fd;
}

async function submitApplication() {
    formBanner.value = null;
    if (!validateClient()) return;
    submitting.value = true;
    try {
        syncPhoneDialCodePrefix();
        await resolveRecaptchaToken();
        await axios.post('/career-application', buildFormData(), {
            withCredentials: true,
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'X-XSRF-TOKEN': xsrfToken(),
                Accept: 'application/json',
            },
        });
        const titlePreset = detail.value?.vacancy?.title ?? '';
        resetCareerForm(titlePreset);
        if (successCloseTimer.value) {
            clearTimeout(successCloseTimer.value);
        }
        dismissCareerModal();
        await router.push({ name: 'thank-you' });
    } catch (e) {
        if (e.message === 'recaptcha-not-loaded') {
            fieldErrors.value.recaptcha_token = t('careers.form.validationRecaptcha');
            formBanner.value = { type: 'error', text: t('careers.form.validationRecaptcha') };
            return;
        }
        if (e.response?.status === 422) {
            applyServerErrors(e.response.data.errors);
            const fallback = typeof e.response.data?.message === 'string' ? e.response.data.message : null;
            if (fallback && !Object.keys(fieldErrors.value).length) {
                formBanner.value = { type: 'error', text: fallback };
            }
        } else {
            formBanner.value = { type: 'error', text: t('careers.form.submitError') };
        }
    } finally {
        submitting.value = false;
    }
}

function onFileChange() {
    // Filename display is delegated in script.js; keep validation state in sync after file pick.
    if (fieldErrors.value.attachment) {
        delete fieldErrors.value.attachment;
    }
}

function onLocationIconError(event) {
    if (event?.target && event.target.src !== fallbackFlagIcon) {
        event.target.src = fallbackFlagIcon;
    }
}

const slug = computed(() => (typeof route.params.slug === 'string' ? route.params.slug : ''));

const detail = computed(() => getCareerDetailBySlug(slug.value, locale.value));

const heroImageSrc = computed(() => {
    const c = getCareersPublicData(locale.value);
    const u = c.heroBackgroundImage;
    return typeof u === 'string' && u.trim() !== '' ? u.trim() : asset('public/images/inner-banner.jpg');
});

watch(
    detail,
    (d) => {
        nextTick(() => {
            window.dispatchEvent(new CustomEvent('dre:career-form-mounted'));
            const title = d?.vacancy?.title;
            form.applyFor = typeof title === 'string' ? title : '';
            setTimeout(() => {
                syncPhoneDialCodePrefix();
            }, 0);
        });
    },
    { immediate: true },
);

function formatDate(iso) {
    if (!iso) {
        return '';
    }
    const d = new Date(`${iso}T12:00:00`);
    const loc = locale.value === 'ar' ? 'ar' : 'en-GB';
    return new Intl.DateTimeFormat(loc, { day: '2-digit', month: 'short', year: 'numeric' }).format(d);
}

watch(
    [slug, detail, locale],
    ([s, d, activeLocale]) => {
        if (!s) {
            router.replace({ name: 'career' });
            return;
        }
        if (!d) {
            router.replace({ name: 'career' });
            return;
        }

        applySeoPayload(d.seo || d.vacancy?.seo || {}, activeLocale, {
            title: d.pageTitle || d.vacancy?.title || 'Career',
            description: d.vacancy?.shortDescription || d.vacancy?.title || 'Career',
            canonicalUrl: window.location.href,
            ogImage: heroImageSrc.value || fallbackFlagIcon,
        });
        applyGlobalImageAltFallback('DRE image');
    },
    { immediate: true },
);

function handleCareerModalShown() {
    formBanner.value = null;
}

function handleCareerModalHidden() {
    if (successCloseTimer.value) {
        clearTimeout(successCloseTimer.value);
        successCloseTimer.value = null;
    }
    formBanner.value = null;
    const titlePreset = detail.value?.vacancy?.title ?? '';
    resetCareerForm(titlePreset);
}

onMounted(() => {
    const modalEl = document.getElementById('careerApplyModal');
    if (!modalEl) return;
    modalEl.addEventListener('shown.bs.modal', handleCareerModalShown);
    modalEl.addEventListener('hidden.bs.modal', handleCareerModalHidden);
});

onBeforeUnmount(() => {
    if (successCloseTimer.value) {
        clearTimeout(successCloseTimer.value);
    }
    const modalEl = document.getElementById('careerApplyModal');
    if (!modalEl) return;
    modalEl.removeEventListener('shown.bs.modal', handleCareerModalShown);
    modalEl.removeEventListener('hidden.bs.modal', handleCareerModalHidden);
});
</script>
