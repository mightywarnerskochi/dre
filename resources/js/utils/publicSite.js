/**
 * Site contact + social URLs from Laravel (site_information), embedded in app.blade.php.
 *
 * @returns {{
 *   phone1: string|null,
 *   phone2: string|null,
 *   email: string|null,
 *   logoUrl: string|null,
 *   colourLogoUrl: string|null,
 *   logoAlt: string|null,
 *   social: Array<{ network: string, href: string }>,
 *   legalPages: Record<string, { title: Record<string, string>, content: Record<string, string> }>
 * }}
 */
export function getPublicSiteBoot() {
    if (typeof window === 'undefined') {
        return {
            phone1: null,
            phone2: null,
            email: null,
            logoUrl: null,
            colourLogoUrl: null,
            logoAlt: null,
            social: [],
            legalPages: {},
        };
    }
    const raw = window.__DRE_SITE__;
    if (!raw || typeof raw !== 'object') {
        return {
            phone1: null,
            phone2: null,
            email: null,
            logoUrl: null,
            colourLogoUrl: null,
            logoAlt: null,
            social: [],
            legalPages: {},
        };
    }
    const social = Array.isArray(raw.social) ? raw.social : [];
    const legalPages = raw.legalPages && typeof raw.legalPages === 'object' ? raw.legalPages : {};

    return {
        phone1: typeof raw.phone1 === 'string' && raw.phone1.trim() !== '' ? raw.phone1.trim() : null,
        phone2: typeof raw.phone2 === 'string' && raw.phone2.trim() !== '' ? raw.phone2.trim() : null,
        email: typeof raw.email === 'string' && raw.email.trim() !== '' ? raw.email.trim() : null,
        logoUrl: typeof raw.logoUrl === 'string' && raw.logoUrl.trim() !== '' ? raw.logoUrl.trim() : null,
        colourLogoUrl: typeof raw.colourLogoUrl === 'string' && raw.colourLogoUrl.trim() !== '' ? raw.colourLogoUrl.trim() : null,
        logoAlt: typeof raw.logoAlt === 'string' && raw.logoAlt.trim() !== '' ? raw.logoAlt.trim() : null,
        social: social.filter((row) => row && typeof row.href === 'string' && row.href.trim() !== '' && row.network),
        legalPages,
    };
}

export function telHref(phone) {
    const digits = String(phone).replace(/\D/g, '');

    return digits !== '' ? `tel:${digits}` : '#';
}
