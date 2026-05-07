import { createI18n } from 'vue-i18n';

function bundledLocaleMessages() {
    const modules = import.meta.glob('./locales/*.json', { eager: true });
    return Object.entries(modules).reduce((acc, [path, mod]) => {
        const match = path.match(/\/([^/]+)\.json$/);
        if (!match?.[1]) {
            return acc;
        }
        const code = String(match[1]).toLowerCase();
        const payload = mod?.default ?? mod;
        acc[code] = payload && typeof payload === 'object' ? payload : {};
        return acc;
    }, {});
}

function runtimeLocaleMessages() {
    if (typeof window === 'undefined') {
        return null;
    }

    const payload = window.__DRE_I18N__;
    if (!payload || typeof payload !== 'object' || Array.isArray(payload)) {
        return null;
    }

    return Object.entries(payload).reduce((acc, [code, value]) => {
        const normalized = String(code).toLowerCase();
        acc[normalized] = value && typeof value === 'object' ? value : {};
        return acc;
    }, {});
}

function resolveMessages() {
    const runtimeMessages = runtimeLocaleMessages();
    const bundledMessages = bundledLocaleMessages();
    const merged = { ...bundledMessages };

    if (runtimeMessages) {
        Object.entries(runtimeMessages).forEach(([code, payload]) => {
            const base = bundledMessages[code] && typeof bundledMessages[code] === 'object'
                ? bundledMessages[code]
                : {};
            const runtime = payload && typeof payload === 'object' ? payload : {};
            merged[code] = { ...base, ...runtime };
        });
    }

    if (!merged.en || typeof merged.en !== 'object') {
        merged.en = {};
    }

    return merged;
}

function getInitialLocale() {
    if (typeof document === 'undefined') {
        return 'en';
    }
    const code = String(document.documentElement.getAttribute('lang') || 'en').toLowerCase();
    return code || 'en';
}

const messages = resolveMessages();
const initialLocale = getInitialLocale();
const activeLocale = Object.prototype.hasOwnProperty.call(messages, initialLocale) ? initialLocale : 'en';

export const i18n = createI18n({
    legacy: false,
    locale: activeLocale,
    fallbackLocale: 'en',
    messages,
});

export default i18n;
