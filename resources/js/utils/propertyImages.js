/** Public path — must match `public/images/dre/property-placeholder.svg` used in Laravel. */
export const DRE_PROPERTY_PLACEHOLDER_IMAGE = '/images/dre/property-placeholder.svg';

/**
 * @param {unknown} images
 * @returns {string[]}
 */
export function dreNormalizePropertyImages(images) {
    if (!Array.isArray(images)) {
        return [DRE_PROPERTY_PLACEHOLDER_IMAGE];
    }
    const cleaned = images.filter((u) => typeof u === 'string' && u.trim() !== '');
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
