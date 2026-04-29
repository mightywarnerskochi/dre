import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '@/layouts/AppLayout.vue';
import Home from '@/pages/Home.vue';
import About from '@/pages/About.vue';
import PlaceholderPage from '@/pages/PlaceholderPage.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: AppLayout,
            children: [
                {
                    path: '',
                    name: 'home',
                    component: Home,
                    meta: { title: 'DRE', bodyClass: '' },
                },
                {
                    path: 'about',
                    name: 'about',
                    component: About,
                    meta: { title: 'About Us | Distinguished Real Estate', bodyClass: 'about-page' },
                },
                {
                    path: 'properties',
                    name: 'properties',
                    component: PlaceholderPage,
                    meta: { title: 'Our Properties | DRE', bodyClass: 'properties-page', pageTitle: 'Our Properties' },
                },
                {
                    path: 'map',
                    name: 'map',
                    component: PlaceholderPage,
                    meta: { title: 'Map View | DRE', bodyClass: 'map-page', pageTitle: 'Map View' },
                },
                {
                    path: 'insights',
                    name: 'insights',
                    component: PlaceholderPage,
                    meta: { title: 'Insights | DRE', bodyClass: 'insights-page', pageTitle: 'Insights' },
                },
                {
                    path: 'career',
                    name: 'career',
                    component: PlaceholderPage,
                    meta: { title: 'Careers | DRE', bodyClass: 'career-page', pageTitle: 'Careers' },
                },
                {
                    path: 'contact',
                    name: 'contact',
                    component: PlaceholderPage,
                    meta: { title: 'Contact | DRE', bodyClass: 'contact-page', pageTitle: 'Contact Us' },
                },
                {
                    path: 'book-a-viewing',
                    name: 'book-a-viewing',
                    component: PlaceholderPage,
                    meta: {
                        title: 'Book a Viewing | DRE',
                        bodyClass: 'book-viewing-page',
                        pageTitle: 'Book a Viewing',
                    },
                },
                {
                    path: 'terms-conditions',
                    name: 'terms',
                    component: PlaceholderPage,
                    meta: {
                        title: 'Terms & Conditions | DRE',
                        bodyClass: 'terms-page',
                        pageTitle: 'Terms & Conditions',
                    },
                },
                {
                    path: 'thank-you',
                    name: 'thank-you',
                    component: PlaceholderPage,
                    meta: { title: 'Thank You | DRE', bodyClass: 'thank-you-page', pageTitle: 'Thank You' },
                },
                {
                    path: ':pathMatch(.*)*',
                    name: 'not-found',
                    component: PlaceholderPage,
                    meta: { title: 'Page Not Found | DRE', bodyClass: 'error-404', pageTitle: '404' },
                },
            ],
        },
    ],
    scrollBehavior() {
        return { top: 0 };
    },
});

router.beforeEach((to) => {
    document.title = to.meta.title ?? 'Distinguished Real Estate';
});

router.afterEach((to) => {
    const cls = typeof to.meta.bodyClass === 'string' ? to.meta.bodyClass : '';
    document.body.className = cls;
});

export default router;
