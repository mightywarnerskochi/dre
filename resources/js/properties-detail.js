import './bootstrap';
import { createApp } from 'vue';
import PropertyDetailPage from './pages/PropertyDetailPage.vue';
import i18n from './i18n';

const mountEl = document.getElementById('dre-properties-detail-app');
const dataEl = document.getElementById('dre-page-data');

if (mountEl && dataEl) {
    let pageData = {};
    try {
        pageData = JSON.parse(dataEl.textContent || '{}');
    } catch {
        pageData = {};
    }
    const propertyId = mountEl.dataset.propertyId;
    createApp(PropertyDetailPage, { pageData, propertyId }).use(i18n).mount(mountEl);
}
