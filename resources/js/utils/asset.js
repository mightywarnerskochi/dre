/**
 * Maps legacy `public/...` paths from the static HTML to Laravel public URLs (/...).
 */
export function asset(path) {
    if (!path) return '';
    const normalized = path.replace(/^public\/?/, '').replace(/^\//, '');
    return `/${normalized}`;
}
