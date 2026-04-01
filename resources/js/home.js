import './bootstrap';
import { createApp } from 'vue';
import HomePage from './pages/HomePage.vue';

const mountEl = document.getElementById('dre-home-app');
const dataEl = document.getElementById('dre-page-data');

if (mountEl && dataEl) {
    let pageData = {};
    try {
        pageData = JSON.parse(dataEl.textContent || '{}');
    } catch {
        pageData = {};
    }
    createApp(HomePage, { pageData }).mount(mountEl);
}
