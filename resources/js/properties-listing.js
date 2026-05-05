import './bootstrap';
import { createApp } from 'vue';
import PropertyListingPage from './pages/PropertyListingPage.vue';
import i18n from './i18n';

const mountEl = document.getElementById('dre-properties-listing-app');
const dataEl = document.getElementById('dre-page-data');

if (mountEl && dataEl) {
    let pageData = {};
    try {
        pageData = JSON.parse(dataEl.textContent || '{}');
    } catch {
        pageData = {};
    }
    createApp(PropertyListingPage, { pageData }).use(i18n).mount(mountEl);
}
