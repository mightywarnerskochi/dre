import './bootstrap';
import { createApp } from 'vue';
import MapSearchPage from './components/MapContent.vue';
import i18n from './i18n';

const mountEl = document.getElementById('dre-map-search-app');
const dataEl = document.getElementById('dre-map-page-data');

if (mountEl && dataEl) {
    let pageData = {};
    try {
        pageData = JSON.parse(dataEl.textContent || '{}');
    } catch {
        pageData = {};
    }
    createApp(MapSearchPage, { pageData }).use(i18n).mount(mountEl);
}
