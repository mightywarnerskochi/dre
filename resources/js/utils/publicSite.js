/**
 * Site contact + social URLs from Laravel (site_information), embedded in app.blade.php.
 *
 * @returns {{
 *   phone1: string|null,
 *   phone2: string|null,
 *   whatsappNumber: string|null,
 *   email: string|null,
 *   logoUrl: string|null,
 *   colourLogoUrl: string|null,
 *   logoAlt: string|null,
 *   social: Array<{ network: string, href: string }>,
 *   legalPages: Record<string, { title: Record<string, string>, content: Record<string, string> }>,
 *   languagesEnabled: boolean,
 *   languages: Array<{ name: string, code: string, flagImage: string|null, flagAlt: string|null, isDefault: boolean }>
 * }}
 */
/**
 * Accept API/legacy rows that use `url` / `link` instead of `href`, and normalize `network`.
 * @param {unknown} row
 * @returns {{ network: string, href: string } | null}
 */
function normalizeSocialRow(row) {
    if (!row || typeof row !== 'object' || Array.isArray(row)) {
        return null;
    }

    const hrefRaw = /** @type {Record<string, unknown>} */ (row).href ?? /** @type {Record<string, unknown>} */ (row).url ?? /** @type {Record<string, unknown>} */ (row).link;
    const href = typeof hrefRaw === 'string' ? hrefRaw.trim() : '';
    if (href === '' || href === '#') {
        return null;
    }

    const netRaw =
        /** @type {Record<string, unknown>} */ (row).network ??
        /** @type {Record<string, unknown>} */ (row).name ??
        /** @type {Record<string, unknown>} */ (row).platform;
    const network = typeof netRaw === 'string' ? netRaw.trim().toLowerCase() : '';
    if (network === '') {
        return null;
    }

    return { network, href };
}

export function getPublicSiteBoot() {
    if (typeof window === 'undefined') {
        return {
            phone1: null,
            phone2: null,
            whatsappNumber: null,
            email: null,
            logoUrl: null,
            colourLogoUrl: null,
            logoAlt: null,
            social: [],
            legalPages: {},
            languagesEnabled: false,
            languages: [],
        };
    }
    const raw = window.__DRE_SITE__;
    if (!raw || typeof raw !== 'object') {
        return {
            phone1: null,
            phone2: null,
            whatsappNumber: null,
            email: null,
            logoUrl: null,
            colourLogoUrl: null,
            logoAlt: null,
            social: [],
            legalPages: {},
            languagesEnabled: false,
            languages: [],
        };
    }
    const socialRaw = Array.isArray(raw.social) ? raw.social : [];
    const social = socialRaw.map(normalizeSocialRow).filter(Boolean);
    const legalPages = raw.legalPages && typeof raw.legalPages === 'object' ? raw.legalPages : {};
    const languages = Array.isArray(raw.languages) ? raw.languages : [];
    const appLinks = raw.appLinks && typeof raw.appLinks === 'object' ? raw.appLinks : {};

    return {
        phone1: typeof raw.phone1 === 'string' && raw.phone1.trim() !== '' ? raw.phone1.trim() : null,
        phone2: typeof raw.phone2 === 'string' && raw.phone2.trim() !== '' ? raw.phone2.trim() : null,
        whatsappNumber: typeof raw.whatsappNumber === 'string' && raw.whatsappNumber.trim() !== '' ? raw.whatsappNumber.trim() : null,
        email: typeof raw.email === 'string' && raw.email.trim() !== '' ? raw.email.trim() : null,
        logoUrl: typeof raw.logoUrl === 'string' && raw.logoUrl.trim() !== '' ? raw.logoUrl.trim() : null,
        colourLogoUrl: typeof raw.colourLogoUrl === 'string' && raw.colourLogoUrl.trim() !== '' ? raw.colourLogoUrl.trim() : null,
        logoAlt: typeof raw.logoAlt === 'string' && raw.logoAlt.trim() !== '' ? raw.logoAlt.trim() : null,
        social,
        legalPages,
        languagesEnabled: raw.languagesEnabled !== false,
        languages,
        appLinks: {
            qrCodeUrl: typeof appLinks.qrCodeUrl === 'string' && appLinks.qrCodeUrl.trim() !== '' ? appLinks.qrCodeUrl.trim() : null,
            googlePlayUrl: typeof appLinks.googlePlayUrl === 'string' && appLinks.googlePlayUrl.trim() !== '' ? appLinks.googlePlayUrl.trim() : null,
            appStoreUrl: typeof appLinks.appStoreUrl === 'string' && appLinks.appStoreUrl.trim() !== '' ? appLinks.appStoreUrl.trim() : null,
        },
    };
}

export function telHref(phone) {
    const digits = String(phone).replace(/\D/g, '');

    return digits !== '' ? `tel:${digits}` : '#';
}
