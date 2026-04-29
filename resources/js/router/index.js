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
                        title: 'Terms & Conditions | DRE',
                        bodyClass: 'terms-page',
                    },
                },
                {
                    path: 'terms-conditions.php',
                    redirect: { name: 'terms' },
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
                    redirect: { name: 'not-found' },
                },
                {
                    path: ':pathMatch(.*)*',
                    name: 'not-found',
                    component: NotFound,
                    meta: { title: 'Page Not Found | DRE', bodyClass: 'error-404' },
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
