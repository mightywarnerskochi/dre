import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import i18n from './i18n';

const app = createApp(App).use(router).use(i18n);

app.mount('#app');
