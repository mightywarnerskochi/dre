/**
 * Site-wide Call / WhatsApp CTAs from `getPublicSiteBoot()` / `window.__DRE_SITE__`.
 * Returns null when data is missing so templates can use v-if.
 */

/**
 * @param {string|null|undefined} raw
 * @returns {string|null}
 */
export function siteWhatsappUrl(raw) {
    const value = String(raw ?? '').trim();
    if (value === '' || value === '#') {
        return null;
    }
    const digits = value.replace(/\D+/g, '');
    if (digits !== '') {
        return `https://wa.me/${digits}`;
    }
    if (/^https?:\/\//i.test(value)) {
        return value;
    }

    return `https://${value}`;
}

/**
 * @param {{ phone1?: string|null, phone2?: string|null }|null|undefined} site
 * @returns {string|null}
 */
export function siteTelHref(site) {
    const phone = site?.phone1 || site?.phone2;
    if (phone == null || String(phone).trim() === '') {
        return null;
    }
    const digits = String(phone).replace(/\D/g, '');

    return digits !== '' ? `tel:${digits}` : null;
}

/**
 * @param {{ whatsappNumber?: string|null }|null|undefined} site
 * @returns {string|null}
 */
export function siteWhatsappHref(site) {
    return siteWhatsappUrl(site?.whatsappNumber);
}
