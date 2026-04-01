import './bootstrap';
import { createApp } from 'vue';
import PropertyListingPage from './pages/PropertyListingPage.vue';

const mountEl = document.getElementById('dre-properties-listing-app');
const dataEl = document.getElementById('dre-page-data');

if (mountEl && dataEl) {
    let pageData = {};
    try {
        pageData = JSON.parse(dataEl.textContent || '{}');
    } catch {
        pageData = {};
    }
    createApp(PropertyListingPage, { pageData }).mount(mountEl);
}
