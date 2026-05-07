<template>
<section class="banner banner--page banner--page--listing position-relative" :aria-label="t('careers.pageHeaderAria')">
        <div class="banner--page__bg">
            <picture>
                <img :src="heroImageSrc" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">{{ t('careers.heroTitle') }}</h1>
                <ol class="breadcrumb-minimal" :aria-label="t('careers.breadcrumbAria')">
                    <li>
                        <RouterLink to="/" :aria-label="t('careers.homeAria')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </RouterLink>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">{{ t('careers.breadcrumbCurrent') }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="career-openings commonPadding-120" aria-labelledby="career-openings-heading">
        <div class="container-ctn">
            <div class="head text-center">
                <h2 id="career-openings-heading">{{ careers.listingHeading || t('careers.heroTitle') }}</h2>
                <p v-if="careers.listingIntroHtml" class="careers-intro" v-html="careers.listingIntroHtml"></p>
            </div>

            <form
                v-if="careers.filterEnabled && careers.filters.length"
                class="career-openings__filters"
                @submit.prevent="applyFilters"
                :aria-label="t('careers.filtersAria')"
            >
                <div
                    v-for="f in careers.filters"
                    :key="f.key"
                    class="career-openings__filter"
                >
                    <label class="sr-only" :for="`career-filter-${f.key}`">{{ f.label }}</label>
                    <select
                        :id="`career-filter-${f.key}`"
                        v-model="selections[f.key]"
                    >
                        <option value="">{{ f.label }} — {{ t('careers.allOption') }}</option>
                        <option
                            v-for="opt in f.options"
                            :key="`${f.key}-${opt.value}`"
                            :value="opt.value"
                        >
                            {{ opt.label }}
                        </option>
                    </select>
                </div>
                <button type="submit" class="btn btn-theme">{{ t('careers.search') }}</button>
            </form>

            <p v-if="!filteredVacancies.length" class="text-center text-muted mt-4 mb-0">{{ t('careers.empty') }}</p>

            <div v-else class="career-openings__list" role="list">
                <article
                    v-for="job in displayedVacancies"
                    :key="job.id"
                    class="career-job-card"
                    role="listitem"
                >
                    <div class="career-job-card__top">
                        <h3>
                            {{ job.title }}
                            <span v-if="job.departmentLabel">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                    <circle cx="5" cy="5.00012" r="5" fill="#A8A8A8"/>
                                </svg>
                                {{ job.departmentLabel }}
                            </span>
                        </h3>
                        <p v-if="job.shortDescription">{{ job.shortDescription }}</p>
                    </div>
                    <ul class="career-job-card__meta">
                        <li v-if="job.jobTypeLabel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M9 15.75C12.7279 15.75 15.75 12.7279 15.75 9C15.75 5.27208 12.7279 2.25 9 2.25C5.27208 2.25 2.25 5.27208 2.25 9C2.25 12.7279 5.27208 15.75 9 15.75Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.25 6V9.75H12" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ job.jobTypeLabel }}
                        </li>
                        <li v-if="job.baseLabel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12 5.25C12 3.8355 12 3.129 11.5605 2.6895C11.121 2.25 10.4145 2.25 9 2.25C7.5855 2.25 6.879 2.25 6.4395 2.6895C6 3.129 6 3.8355 6 5.25M1.5 10.5C1.5 8.39325 1.5 7.34025 2.0055 6.5835C2.22443 6.25579 2.50579 5.97443 2.8335 5.7555C3.59025 5.25 4.6425 5.25 6.75 5.25H11.25C13.3568 5.25 14.4098 5.25 15.1665 5.7555C15.4942 5.97443 15.7756 6.25579 15.9945 6.5835C16.5 7.34025 16.5 8.3925 16.5 10.5C16.5 12.6075 16.5 13.6598 15.9945 14.4165C15.7756 14.7442 15.4942 15.0256 15.1665 15.2445C14.4098 15.75 13.3575 15.75 11.25 15.75H6.75C4.64325 15.75 3.59025 15.75 2.8335 15.2445C2.50579 15.0256 2.22443 14.7442 2.0055 14.4165C1.5 13.6598 1.5 12.6075 1.5 10.5Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4.5 8.25L4.989 8.4015C7.60333 9.20039 10.3967 9.20039 13.011 8.4015L13.5 8.25M9 9V10.5" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ job.baseLabel }}
                        </li>
                        <li v-if="job.publishedDate">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12.375 3.75V2.25M5.625 3.75V2.25M2.4375 6H15.5625M2.25 7.533C2.25 5.94675 2.25 5.15325 2.577 4.54725C2.87268 4.00673 3.3315 3.57338 3.888 3.309C4.53 3 5.37 3 7.05 3H10.95C12.63 3 13.47 3 14.112 3.309C14.6768 3.5805 15.135 4.014 15.423 4.5465C15.75 5.154 15.75 5.9475 15.75 7.53375V11.2177C15.75 12.804 15.75 13.5975 15.423 14.2035C15.1273 14.744 14.6685 15.1774 14.112 15.4418C13.47 15.75 12.63 15.75 10.95 15.75H7.05C5.37 15.75 4.53 15.75 3.888 15.441C3.33161 15.1768 2.87281 14.7437 2.577 14.2035C2.25 13.596 2.25 12.8025 2.25 11.2162V7.533Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ formatDate(job.publishedDate) }}
                        </li>
                        <li v-if="job.locationLine">
                            <img :src="job.flagImage || fallbackLocationIcon" :alt="job.flagAlt || ''" width="16" height="16" loading="lazy" @error="onLocationIconError">
                            {{ job.locationLine }}
                        </li>
                    </ul>
                    <RouterLink
                        v-if="job.slug"
                        class="career-job-card__apply"
                        :to="{ name: 'career-details', params: { slug: job.slug } }"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none" aria-hidden="true">
                            <path d="M9.28234 17.1303L15.8309 7.98271M15.8309 7.98271L9.71616 8.79363M15.8309 7.98271L17.0342 14.0325" stroke="black" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ t('careers.applyNow') }}
                    </RouterLink>
                </article>
            </div>
            <div
                v-if="hasMoreVacancies"
                ref="loadMoreSentinel"
                class="career-load-sentinel"
                aria-hidden="true"
            />
        </div>
    </section>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { asset } from '@/utils/asset';
import { getCareersPublicData } from '@/utils/publicContent';

const { locale, t } = useI18n({ useScope: 'global' });
const PAGE_SIZE = 6;

const careers = computed(() => getCareersPublicData(locale.value));
const fallbackLocationIcon = asset('public/images/career-location-placeholder.svg');
const visibleCount = ref(PAGE_SIZE);
const loadMoreSentinel = ref(null);
let loadMoreObserver = null;

const heroImageSrc = computed(() => {
    const u = careers.value.heroBackgroundImage;
    return typeof u === 'string' && u.trim() !== '' ? u.trim() : asset('public/images/inner-banner.jpg');
});

const selections = reactive({});
const committed = reactive({});

watch(
    careers,
    (c) => {
        c.filters.forEach((f) => {
            if (selections[f.key] === undefined) {
                selections[f.key] = '';
            }
            if (committed[f.key] === undefined) {
                committed[f.key] = '';
            }
        });
    },
    { immediate: true, deep: true },
);

function applyFilters() {
    careers.value.filters.forEach((f) => {
        committed[f.key] = selections[f.key] ?? '';
    });
    visibleCount.value = PAGE_SIZE;
}

function onLocationIconError(event) {
    if (event?.target && event.target.src !== fallbackLocationIcon) {
        event.target.src = fallbackLocationIcon;
    }
}

function vacancyMatches(job) {
    const cfg = careers.value.filters;
    for (let i = 0; i < cfg.length; i += 1) {
        const f = cfg[i];
        const sel = committed[f.key];
        if (!sel) {
            continue;
        }
        if (f.key === 'title' && job.titleFilterKey !== sel) {
            return false;
        }
        if (f.key === 'job_type' && job.jobTypeKey !== sel) {
            return false;
        }
        if (f.key === 'department' && job.departmentKey !== sel) {
            return false;
        }
        if (f.key === 'location' && job.locationKey !== sel) {
            return false;
        }
    }
    return true;
}

const filteredVacancies = computed(() => {
    const list = careers.value.vacancies;
    if (!careers.value.filterEnabled || !careers.value.filters.length) {
        return list;
    }
    return list.filter((job) => vacancyMatches(job));
});
const displayedVacancies = computed(() => filteredVacancies.value.slice(0, visibleCount.value));
const hasMoreVacancies = computed(() => visibleCount.value < filteredVacancies.value.length);

function loadNextPage() {
    if (!hasMoreVacancies.value) {
        return;
    }
    visibleCount.value = Math.min(visibleCount.value + PAGE_SIZE, filteredVacancies.value.length);
}

function disconnectObserver() {
    if (loadMoreObserver) {
        loadMoreObserver.disconnect();
        loadMoreObserver = null;
    }
}

function initLoadMoreObserver() {
    disconnectObserver();
    if (typeof window === 'undefined' || !('IntersectionObserver' in window)) {
        return;
    }
    const target = loadMoreSentinel.value;
    if (!target || !hasMoreVacancies.value) {
        return;
    }
    loadMoreObserver = new IntersectionObserver(
        (entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                loadNextPage();
            }
        },
        { root: null, rootMargin: '240px 0px', threshold: 0 },
    );
    loadMoreObserver.observe(target);
}

watch(locale, async () => {
    visibleCount.value = PAGE_SIZE;
    await nextTick();
    initLoadMoreObserver();
});

watch(filteredVacancies, async () => {
    if (visibleCount.value > filteredVacancies.value.length) {
        visibleCount.value = Math.min(PAGE_SIZE, filteredVacancies.value.length);
    }
    await nextTick();
    initLoadMoreObserver();
});

watch(hasMoreVacancies, async () => {
    await nextTick();
    initLoadMoreObserver();
});

onMounted(async () => {
    await nextTick();
    initLoadMoreObserver();
});

onBeforeUnmount(() => {
    disconnectObserver();
});

function formatDate(iso) {
    if (!iso) {
        return '';
    }
    const d = new Date(`${iso}T12:00:00`);
    const loc = locale.value === 'ar' ? 'ar' : 'en-GB';
    return new Intl.DateTimeFormat(loc, { day: '2-digit', month: 'short', year: 'numeric' }).format(d);
}
</script>

<style scoped>
.career-load-sentinel {
    width: 100%;
    height: 1px;
}
</style>
