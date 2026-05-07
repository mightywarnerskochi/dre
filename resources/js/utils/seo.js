import { getPublicContentBoot } from '@/utils/publicContent';
import { getPublicSiteBoot } from '@/utils/publicSite';

const DEFAULT_TITLE = 'Distinguished Real Estate';
const OTHER_META_SELECTOR = '[data-dre-other-meta="1"]';
let altObserverStarted = false;

function hasArabicGlyphs(value) {
    return /[\u0600-\u06FF]/.test(String(value || ''));
}

function normalizeEnglishAlt(value, fallback = 'DRE image') {
    const text = String(value || '').trim();
    const fallbackText = typeof fallback === 'string'
        ? fallback.trim()
        : String(fallback ?? '').trim();
    if (text === '' || text === '[object Object]') {
        return fallbackText;
    }

    if (hasArabicGlyphs(text)) {
        return fallbackText;
    }

    return text;
}

function pickLocalized(value, locale) {
    if (value == null) return '';
    if (typeof value === 'string') return value.trim();
    if (typeof value !== 'object') return '';

    const lang = String(locale || 'en').toLowerCase().startsWith('ar') ? 'ar' : 'en';
    const preferred = typeof value[lang] === 'string' ? value[lang].trim() : '';
    if (preferred !== '') return preferred;

    const fallback = typeof value.en === 'string' ? value.en.trim() : '';
    if (fallback !== '') return fallback;

    return '';
}

function asAbsoluteUrl(rawUrl) {
    const value = String(rawUrl || '').trim();
    if (value === '') return '';

    try {
        return new URL(value, window.location.origin).toString();
    } catch (_error) {
        return '';
    }
}

function defaultOgImageUrl() {
    const site = getPublicSiteBoot();
    const siteLogo = asAbsoluteUrl(site?.colourLogoUrl) || asAbsoluteUrl(site?.logoUrl);
    if (siteLogo) {
        return siteLogo;
    }

    const iconHref = document.head.querySelector('link[rel="apple-touch-icon"]')?.getAttribute('href')
        || document.head.querySelector('link[rel="icon"]')?.getAttribute('href')
        || '';
    return asAbsoluteUrl(iconHref) || '';
}

function upsertMetaName(name, content) {
    if (!name) return;
    let node = document.head.querySelector(`meta[name="${name}"]`);
    if (!node) {
        node = document.createElement('meta');
        node.setAttribute('name', name);
        document.head.appendChild(node);
    }
    node.setAttribute('content', content || '');
}

function upsertMetaProperty(property, content) {
    if (!property) return;
    let node = document.head.querySelector(`meta[property="${property}"]`);
    if (!node) {
        node = document.createElement('meta');
        node.setAttribute('property', property);
        document.head.appendChild(node);
    }
    node.setAttribute('content', content || '');
}

function upsertCanonical(url) {
    const resolved = asAbsoluteUrl(url) || window.location.href;
    let node = document.head.querySelector('link[rel="canonical"]');
    if (!node) {
        node = document.createElement('link');
        node.setAttribute('rel', 'canonical');
        document.head.appendChild(node);
    }
    node.setAttribute('href', resolved);
}

function clearOtherMetaTags() {
    document.head.querySelectorAll(OTHER_META_SELECTOR).forEach((node) => node.remove());
}

function injectOtherMetaTags(rawHtml) {
    clearOtherMetaTags();
    const html = String(rawHtml || '').trim();
    if (html === '') return;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = html;

    Array.from(wrapper.children).forEach((node) => {
        const tag = node.tagName?.toLowerCase();
        if (tag !== 'meta' && tag !== 'link') return;
        if (tag === 'link' && node.getAttribute('rel')?.toLowerCase() !== 'alternate') return;
        node.setAttribute('data-dre-other-meta', '1');
        document.head.appendChild(node);
    });
}

function pageSeoMap() {
    const seoRoot = getPublicContentBoot()?.seo;
    const pages = seoRoot && typeof seoRoot === 'object' ? seoRoot.pages : null;
    return pages && typeof pages === 'object' ? pages : {};
}

function normalizeSeoKey(value) {
    return String(value || '')
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '');
}

function resolvePageSeoPayload(pages, pageKey) {
    if (!pageKey || typeof pageKey !== 'string') {
        return {};
    }

    const direct = pages[pageKey];
    if (direct && typeof direct === 'object') {
        return direct;
    }

    const aliasesByKey = {
        home: ['home', 'home-page', 'homepage', 'index'],
        about: ['about', 'about-us', 'aboutus'],
        properties: ['properties', 'property', 'our-property', 'ourproperty'],
        blogs: ['blogs', 'blog', 'insights', 'news', 'news-insights'],
        careers: ['careers', 'career', 'jobs'],
        contact: ['contact', 'contact-us', 'contactus'],
        map: ['map', 'map-search', 'mapsearch'],
        terms: ['terms', 'terms-conditions', 'termsandconditions'],
        privacy: ['privacy', 'privacy-policy', 'privacypolicy'],
        disclaimer: ['disclaimer'],
        cookie: ['cookie', 'cookie-policy', 'cookiepolicy'],
    };

    const normalizedCandidates = new Set(
        (aliasesByKey[pageKey] || [pageKey])
            .map((candidate) => normalizeSeoKey(candidate))
            .filter((candidate) => candidate !== '')
    );

    for (const [key, payload] of Object.entries(pages)) {
        if (!payload || typeof payload !== 'object') {
            continue;
        }

        if (normalizedCandidates.has(normalizeSeoKey(key))) {
            return payload;
        }
    }

    return {};
}

export function applySeoPayload(payload = {}, locale = 'en', fallback = {}) {
    const title = pickLocalized(payload.metaTitle ?? payload.title, locale)
        || pickLocalized(fallback.metaTitle ?? fallback.title, locale)
        || DEFAULT_TITLE;

    const description = pickLocalized(payload.metaDescription ?? payload.description, locale)
        || pickLocalized(fallback.metaDescription ?? fallback.description, locale)
        || title;

    const keywords = pickLocalized(payload.metaKeywords ?? payload.keywords, locale)
        || pickLocalized(fallback.metaKeywords ?? fallback.keywords, locale)
        || '';

    const canonicalUrl = pickLocalized(payload.canonicalUrl, locale)
        || pickLocalized(fallback.canonicalUrl, locale)
        || window.location.href;

    const ogTitle = pickLocalized(payload.ogTitle, locale)
        || pickLocalized(fallback.ogTitle, locale)
        || title;

    const ogDescription = pickLocalized(payload.ogDescription, locale)
        || pickLocalized(fallback.ogDescription, locale)
        || description;

    const ogImage = asAbsoluteUrl(payload.ogImage)
        || asAbsoluteUrl(fallback.ogImage)
        || defaultOgImageUrl();

    document.title = title;
    upsertMetaName('description', description);
    upsertMetaName('keywords', keywords);
    upsertMetaProperty('og:title', ogTitle);
    upsertMetaProperty('og:description', ogDescription);
    upsertMetaProperty('og:type', 'website');
    upsertMetaProperty('og:url', asAbsoluteUrl(canonicalUrl) || window.location.href);
    upsertMetaProperty('og:image', ogImage);
    upsertCanonical(canonicalUrl);
    injectOtherMetaTags(pickLocalized(payload.otherMetaTags, locale) || pickLocalized(fallback.otherMetaTags, locale));
}

export function applyPageSeoByKey(pageKey, locale = 'en', fallback = {}) {
    const pages = pageSeoMap();
    const payload = resolvePageSeoPayload(pages, pageKey);
    applySeoPayload(payload, locale, fallback);
}

export function applyGlobalImageAltFallback(defaultAltText = 'DRE image') {
    const fallback = normalizeEnglishAlt(defaultAltText, 'DRE image') || 'DRE image';
    document.querySelectorAll('img').forEach((img) => {
        const currentRaw = img.getAttribute('alt');
        const current = normalizeEnglishAlt(currentRaw, '');
        if (current !== '') {
            if (currentRaw !== current) {
                img.setAttribute('alt', current);
            }
            return;
        }

        const inferredRaw = img.getAttribute('data-alt')
            || img.getAttribute('title')
            || '';
        const inferred = normalizeEnglishAlt(inferredRaw, fallback);

        img.setAttribute('alt', inferred || fallback);
    });
}

export function startGlobalAltObserver() {
    if (altObserverStarted || typeof MutationObserver === 'undefined') {
        return;
    }

    altObserverStarted = true;
    applyGlobalImageAltFallback(document.title || 'DRE image');

    const observer = new MutationObserver(() => {
        applyGlobalImageAltFallback(document.title || 'DRE image');
    });

    observer.observe(document.body, { childList: true, subtree: true });
}

