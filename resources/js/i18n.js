import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import ar from './locales/ar.json';

function getInitialLocale() {
    if (typeof document === 'undefined') {
        return 'en';
    }
    const code = document.documentElement.getAttribute('lang');
    return code === 'ar' ? 'ar' : 'en';
}

export const i18n = createI18n({
    legacy: false,
    locale: getInitialLocale(),
    fallbackLocale: 'en',
    messages: { en, ar },
});

export default i18n;
