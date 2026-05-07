/** Public path — must match `public/images/dre/property-placeholder.png` used in Laravel. */
export const DRE_PROPERTY_PLACEHOLDER_IMAGE = '/images/dre/property-placeholder.png';

/** Public path — agent avatar when CMS has no photo or URL fails (not the property/house icon). */
export const DRE_AGENT_PLACEHOLDER_IMAGE = '/images/dre/agent-placeholder.svg';

/**
 * @param {unknown} entry
 * @returns {string|null}
 */
export function drePickImageUrl(entry) {
    if (typeof entry === 'string') {
        const t = entry.trim();

        return t !== '' ? t : null;
    }
    if (entry && typeof entry === 'object') {
        const path = entry.image ?? entry.url ?? entry.src ?? entry.path;
        if (typeof path === 'string' && path.trim() !== '') {
            return path.trim();
        }
    }

    return null;
}

/**
 * @param {unknown} images
 * @returns {string[]}
 */
export function dreNormalizePropertyImages(images) {
    if (!Array.isArray(images)) {
        return [DRE_PROPERTY_PLACEHOLDER_IMAGE];
    }
    const cleaned = images.map((entry) => drePickImageUrl(entry)).filter(Boolean);
    return cleaned.length ? cleaned : [DRE_PROPERTY_PLACEHOLDER_IMAGE];
}

/** Use on <img @error="dreOnPropertyImgError"> — avoids infinite loop. */
export function dreOnPropertyImgError(event) {
    const el = event?.target;
    if (!el || el.tagName !== 'IMG') return;
    if (el.dataset.dreImgFallback === '1') return;
    el.dataset.dreImgFallback = '1';
    el.src = DRE_PROPERTY_PLACEHOLDER_IMAGE;
}

/** Use on agent avatar <img @error="dreOnAgentImgError"> — avoids swapping to property placeholder. */
export function dreOnAgentImgError(event) {
    const el = event?.target;
    if (!el || el.tagName !== 'IMG') return;
    if (el.dataset.dreAgentImgFallback === '1') return;
    el.dataset.dreAgentImgFallback = '1';
    el.src = DRE_AGENT_PLACEHOLDER_IMAGE;
}
