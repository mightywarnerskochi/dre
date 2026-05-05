import './bootstrap';
import { createApp } from 'vue';
import HomePage from './pages/Home.vue';
import i18n from './i18n';

const mountEl = document.getElementById('dre-home-app');
const dataEl = document.getElementById('dre-page-data');

if (mountEl && dataEl) {
    let pageData = {};
    try {
        pageData = JSON.parse(dataEl.textContent || '{}');
    } catch {
        pageData = {};
    }
    createApp(HomePage, { pageData }).use(i18n).mount(mountEl);
}
