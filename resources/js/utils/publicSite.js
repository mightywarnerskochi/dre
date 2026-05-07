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
 *   languages: Array<{ name: string, code: string, flagImage: string|null, flagAlt: string|null, isDefault: boolean }>
 * }}
 */
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
            languages: [],
        };
    }
    const social = Array.isArray(raw.social) ? raw.social : [];
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
        social: social.filter((row) => row && typeof row.href === 'string' && row.href.trim() !== '' && row.network),
        legalPages,
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
