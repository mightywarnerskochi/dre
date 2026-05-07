import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '@/layouts/AppLayout.vue';
import Home from '@/pages/Home.vue';
import About from '@/pages/About.vue';
import OurProperty from '@/pages/OurProperty.vue';
import PropertyDetails from '@/pages/PropertyDetails.vue';
import Insights from '@/pages/Insights.vue';
import InsightDetail from '@/pages/InsightDetail.vue';
import Career from '@/pages/Career.vue';
import CareerDetail from '@/pages/CareerDetail.vue';
import Contact from '@/pages/Contact.vue';
import Map from '@/pages/Map.vue';
import BookAViewing from '@/pages/BookAViewing.vue';
import TermsConditions from '@/pages/TermsConditions.vue';
import ThankYou from '@/pages/ThankYou.vue';
import NotFound from '@/pages/NotFound.vue';
import i18n from '@/i18n';
import { applyGlobalImageAltFallback, applyPageSeoByKey } from '@/utils/seo';

const PAGE_KEY_BY_ROUTE_NAME = {
    home: 'home',
    about: 'about',
    'our-property': 'properties',
    'property-details': 'properties',
    insights: 'blogs',
    'insights-details': 'blogs',
    career: 'careers',
    'career-details': 'careers',
    contact: 'contact',
    map: 'map',
    terms: 'terms',
    'privacy-policy': 'privacy',
    disclaimer: 'disclaimer',
    'cookie-policy': 'cookie',
};

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
                    path: 'our-property',
                    name: 'our-property',
                    component: OurProperty,
                    meta: { title: 'Our Properties | DRE', bodyClass: 'properties-page' },
                },
                {
                    path: 'properties',
                    redirect: { name: 'our-property' },
                },
                {
                    path: 'properties.php',
                    redirect: { name: 'our-property' },
                },
                {
                    path: 'property-details/:slug?',
                    name: 'property-details',
                    component: PropertyDetails,
                    meta: { title: 'Property Details | DRE', bodyClass: 'property-details-page' },
                },
                {
                    path: 'properties-details.php',
                    redirect: { name: 'property-details' },
                },
                {
                    path: 'insights',
                    name: 'insights',
                    component: Insights,
                    meta: { title: 'Insights | Distinguished Real Estate', bodyClass: 'insights-page' },
                },
                {
                    path: 'insights.php',
                    redirect: { name: 'insights' },
                },
                {
                    path: 'insights-details/:slug?',
                    name: 'insights-details',
                    component: InsightDetail,
                    meta: { title: 'Insight | Distinguished Real Estate', bodyClass: 'insights-page insights-details-page' },
                },
                {
                    path: 'insights-details.php',
                    redirect: { name: 'insights-details' },
                },
                {
                    path: 'career',
                    name: 'career',
                    component: Career,
                    meta: { title: 'Careers | DRE', bodyClass: 'careers-page' },
                },
                {
                    path: 'career.php',
                    redirect: { name: 'career' },
                },
                {
                    path: 'career-details/:slug?',
                    name: 'career-details',
                    component: CareerDetail,
                    meta: { title: 'Career | Distinguished Real Estate', bodyClass: 'careers-page' },
                },
                {
                    path: 'career-details.php',
                    redirect: { name: 'career-details' },
                },
                {
                    path: 'contact',
                    name: 'contact',
                    component: Contact,
                    meta: { title: 'Contact | Distinguished Real Estate', bodyClass: 'contact-page' },
                },
                {
                    path: 'contact.php',
                    redirect: { name: 'contact' },
                },
                {
                    path: 'map',
                    name: 'map',
                    component: Map,
                    meta: { title: 'Map View | DRE', bodyClass: 'map-page' },
                },
                {
                    path: 'map.php',
                    redirect: { name: 'map' },
                },
                {
                    path: 'book-a-viewing',
                    name: 'book-a-viewing',
                    component: BookAViewing,
                    meta: {
                        title: 'Book a Viewing | DRE',
                        bodyClass: 'book-viewing-page',
                    },
                },
                {
                    path: 'book-a-viewing.php',
                    redirect: { name: 'book-a-viewing' },
                },
                {
                    path: 'terms-conditions',
                    name: 'terms',
                    component: TermsConditions,
                    meta: {
                        title: 'Terms & Conditions | Distinguished Real Estate',
                        bodyClass: 'terms-page',
                        policyKey: 'terms-conditions',
                    },
                },
                {
                    path: 'terms-conditions.php',
                    redirect: { name: 'terms' },
                },
                {
                    path: 'privacy-policy',
                    name: 'privacy-policy',
                    component: TermsConditions,
                    meta: {
                        title: 'Privacy Policy | Distinguished Real Estate',
                        bodyClass: 'terms-page',
                        policyKey: 'privacy-policy',
                    },
                },
                {
                    path: 'privacy-policy.php',
                    redirect: { name: 'privacy-policy' },
                },
                {
                    path: 'disclaimer',
                    name: 'disclaimer',
                    component: TermsConditions,
                    meta: {
                        title: 'Disclaimer | Distinguished Real Estate',
                        bodyClass: 'terms-page',
                        policyKey: 'disclaimer',
                    },
                },
                {
                    path: 'disclaimer.php',
                    redirect: { name: 'disclaimer' },
                },
                {
                    path: 'cookie-policy',
                    name: 'cookie-policy',
                    component: TermsConditions,
                    meta: {
                        title: 'Cookie Policy | Distinguished Real Estate',
                        bodyClass: 'terms-page',
                        policyKey: 'cookie-policy',
                    },
                },
                {
                    path: 'cookie-policy.php',
                    redirect: { name: 'cookie-policy' },
                },
                {
                    path: 'thank-you',
                    name: 'thank-you',
                    component: ThankYou,
                    meta: { title: 'Thank You | DRE', bodyClass: 'thank-you-page' },
                },
                {
                    path: 'thank-you.php',
                    redirect: { name: 'thank-you' },
                },
                {
                    path: '404.php',
                    name: 'not-found-php',
                    component: NotFound,
                    meta: { title: 'Page Not Found | Distinguished Real Estate', bodyClass: 'error-404-page' },
                },
                {
                    path: ':pathMatch(.*)*',
                    name: 'not-found',
                    component: NotFound,
                    meta: { title: 'Page Not Found | Distinguished Real Estate', bodyClass: 'error-404-page' },
                },
            ],
        },
    ],
    scrollBehavior() {
        return { top: 0 };
    },
});

router.beforeEach((to) => {
    const locale = typeof i18n.global.locale === 'object'
        ? (i18n.global.locale.value || 'en')
        : (i18n.global.locale || 'en');
    const routeName = typeof to.name === 'string' ? to.name : '';
    const pageKey = PAGE_KEY_BY_ROUTE_NAME[routeName] || '';

    applyPageSeoByKey(pageKey, locale, {
        title: 'Distinguished Real Estate',
        description: 'Distinguished Real Estate',
        canonicalUrl: new URL(to.fullPath || '/', window.location.origin).toString(),
    });
});

router.afterEach((to) => {
    const cls = typeof to.meta.bodyClass === 'string' ? to.meta.bodyClass : '';
    document.body.className = cls;

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            window.dispatchEvent(new CustomEvent('dre:page-mounted', { detail: { path: to.fullPath } }));
            applyGlobalImageAltFallback(document.title || 'DRE image');
        });
    });
});

export default router;
