import './bootstrap';
import { createApp } from 'vue';
import About from './pages/About.vue';

const mountEl = document.getElementById('dre-about-app');
const dataEl = document.getElementById('dre-about-page-data');

if (mountEl && dataEl) {
    let pageData = {};
    try {
        pageData = JSON.parse(dataEl.textContent || '{}');
    } catch {
        pageData = {};
    }
    createApp(About, { pageData }).mount(mountEl);
}
