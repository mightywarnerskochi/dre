import './bootstrap';
import { createApp } from 'vue';
import { startGlobalAltObserver } from '@/utils/seo';

const BOOT_CACHE_KEY = 'dre_spa_boot_v1';
const BOOT_CACHE_TTL_MS = 5 * 60 * 1000;

function assignBootObject(key, value) {
    if (!value || typeof value !== 'object' || Array.isArray(value)) {
        return;
    }

    window[key] = value;
}

function readBootCache() {
    try {
        const raw = sessionStorage.getItem(BOOT_CACHE_KEY);
        if (!raw) {
            return null;
        }

        const parsed = JSON.parse(raw);
        if (!parsed || typeof parsed !== 'object') {
            return null;
        }

        const ts = Number(parsed.ts || 0);
        if (!Number.isFinite(ts) || Date.now() - ts > BOOT_CACHE_TTL_MS) {
            return null;
        }

        const payload = parsed.payload;
        return payload && typeof payload === 'object' ? payload : null;
    } catch (_error) {
        return null;
    }
}

function writeBootCache(payload) {
    try {
        sessionStorage.setItem(
            BOOT_CACHE_KEY,
            JSON.stringify({
                ts: Date.now(),
                payload,
            })
        );
    } catch (_error) {
        // Ignore storage errors (private mode / quota)
    }
}

async function hydrateBootPayload() {
    const cached = readBootCache();
    if (cached) {
        assignBootObject('__DRE_SITE__', cached.sitePublic);
        assignBootObject('__DRE_CONTENT__', cached.contentPublic);
        assignBootObject('__DRE_I18N__', cached.i18nMessages);
        return;
    }

    const endpoint = String(window.__DRE_BOOT_ENDPOINT__ || '/api/public/bootstrap').trim();
    if (endpoint === '') {
        return;
    }

    try {
        const { data } = await window.axios.get(endpoint, {
            headers: {
                Accept: 'application/json',
            },
        });
        const payload = data && typeof data === 'object' ? data : {};

        assignBootObject('__DRE_SITE__', payload.sitePublic);
        assignBootObject('__DRE_CONTENT__', payload.contentPublic);
        assignBootObject('__DRE_I18N__', payload.i18nMessages);
        writeBootCache(payload);
    } catch (_error) {
        // Keep graceful fallback defaults when boot API is unavailable.
        window.__DRE_SITE__ = window.__DRE_SITE__ && typeof window.__DRE_SITE__ === 'object' ? window.__DRE_SITE__ : {};
        window.__DRE_CONTENT__ = window.__DRE_CONTENT__ && typeof window.__DRE_CONTENT__ === 'object' ? window.__DRE_CONTENT__ : {};
        window.__DRE_I18N__ = window.__DRE_I18N__ && typeof window.__DRE_I18N__ === 'object' ? window.__DRE_I18N__ : {};
    }
}

async function bootstrapApp() {
    await hydrateBootPayload();

    const [{ default: App }, { default: router }, { default: i18n }] = await Promise.all([
        import('./App.vue'),
        import('./router'),
        import('./i18n'),
    ]);

    const app = createApp(App).use(router).use(i18n);
    app.mount('#app');
    startGlobalAltObserver();
}

bootstrapApp();
