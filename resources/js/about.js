import './bootstrap';
import { createApp } from 'vue';
import About from './pages/About.vue';
import i18n from './i18n';

const mountEl = document.getElementById('dre-about-app');
const dataEl = document.getElementById('dre-about-page-data');

if (mountEl && dataEl) {
    let pageData = {};
    try {
        pageData = JSON.parse(dataEl.textContent || '{}');
    } catch {
        pageData = {};
    }
    createApp(About, { pageData }).use(i18n).mount(mountEl);
}
