<template>
    <div class="offcanvas offcanvas-end mobile_left_menu" tabindex="-1" id="burgerMenu" aria-labelledby="mobileOffcanvasLabel" aria-modal="true" role="dialog">
        <div class="offcanvas-header mobile-menu__header">
            <h2 class="visually-hidden" id="mobileOffcanvasLabel">{{ t('nav.mobileMenu') }}</h2>
            <RouterLink :to="{ name: 'home' }" class="mobile-menu__brand" :aria-label="t('brand.logoAria')">
                <img :src="mobileBrandLogo" :alt="mobileBrandLogoAlt" width="160" height="48" loading="lazy" class="img-fluid mobile-menu__logo" />
            </RouterLink>
            <button type="button" class="mobile-menu__close" data-bs-dismiss="offcanvas" :aria-label="t('nav.closeMenu')">
                <span class="mobile-menu__close-label">{{ t('nav.close') }}</span>
                <span class="mobile-menu__close-x" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M1 13L13 1M13 13L1 1" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
                    </svg>
                </span>
            </button>
        </div>

        <div class="offcanvas-body mobile-menu__body">
            <nav class="mobile-menu__nav" :aria-label="t('nav.mobileNavAria')">
                <ul class="mobile-menu__list">
                    <li>
                        <MobileNavLink :to="{ name: 'home' }" active-class-extra="mobile-menu__link" :exact="true">{{ t('footer.navHome') }}</MobileNavLink>
                    </li>
                    <li>
                        <MobileNavLink :to="{ name: 'about' }" active-class-extra="mobile-menu__link">{{ t('footer.navAbout') }}</MobileNavLink>
                    </li>
                    <li class="mobile-menu__item has-sub">
                        <button
                            class="mobile-menu__link mobile-menu__link--toggle"
                            type="button"
                            :aria-expanded="isPropertiesMenuOpen ? 'true' : 'false'"
                            aria-controls="mobileMenuProperties"
                            @click="isPropertiesMenuOpen = !isPropertiesMenuOpen"
                        >
                            {{ t('footer.navOurProperties') }}
                            <svg class="mobile-menu__chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <ul v-show="isPropertiesMenuOpen" class="mobile-menu__sub" id="mobileMenuProperties">
                            <li>
                                <MobileNavLink :to="{ name: 'our-property' }">{{ t('nav.allListings') }}</MobileNavLink>
                            </li>
                            <li>
                                <MobileNavLink :to="{ name: 'map' }">{{ t('nav.mapView') }}</MobileNavLink>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <MobileNavLink :to="{ name: 'insights' }" active-class-extra="mobile-menu__link">{{ t('footer.navInsights') }}</MobileNavLink>
                    </li>
                    <li>
                        <MobileNavLink :to="{ name: 'career' }" active-class-extra="mobile-menu__link">{{ t('footer.navCareers') }}</MobileNavLink>
                    </li>
                    <li>
                        <MobileNavLink :to="{ name: 'contact' }" active-class-extra="mobile-menu__link">{{ t('footer.navContact') }}</MobileNavLink>
                    </li>
                </ul>
            </nav>
            <div v-if="showMobileMenuFooter" class="footer">
                <div class="mobile-menu__divider" role="presentation"></div>

                <div v-if="showMobileMenuContact" class="mobile-menu__contact">
                    <p v-if="dreSiteRef.phone1 || dreSiteRef.phone2" class="mobile-menu__line">
                        <svg class="mobile-menu__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"
                                fill="currentColor"
                            />
                        </svg>
                        <span>
                            <template v-if="dreSiteRef.phone1">
                                <a :href="telHref(dreSiteRef.phone1)">{{ dreSiteRef.phone1 }}</a>
                            </template>
                            <span v-if="dreSiteRef.phone1 && dreSiteRef.phone2" class="mobile-menu__sep">|</span>
                            <template v-if="dreSiteRef.phone2">
                                <a :href="telHref(dreSiteRef.phone2)">{{ dreSiteRef.phone2 }}</a>
                            </template>
                        </span>
                    </p>
                    <p v-if="dreSiteRef.email" class="mobile-menu__line">
                        <svg class="mobile-menu__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"
                                fill="currentColor"
                            />
                        </svg>
                        <span>
                            <a :href="'mailto:' + dreSiteRef.email">{{ dreSiteRef.email }}</a>
                        </span>
                    </p>
                </div>

                <ul v-if="menuSocialItems.length" class="mobile-menu__social" :aria-label="t('followUs.socialAria')">
                    <li v-for="item in menuSocialItems" :key="item.network">
                        <a
                            :href="item.href"
                            :aria-label="socialAriaLabel(item.network)"
                            class="mobile-menu__social-link"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <FollowUsNetworkIcon class="mobile-menu__social-icon" :network="item.network" />
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, inject, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { asset } from '@/utils/asset';
import MobileNavLink from '@/components/MobileNavLink.vue';
import FollowUsNetworkIcon from '@/components/FollowUsNetworkIcon.vue';
import { getPublicSiteBoot, telHref } from '@/utils/publicSite';
import { siteWhatsappUrl } from '@/utils/siteContact';

const { t } = useI18n({ useScope: 'global' });
const isPropertiesMenuOpen = ref(false);
const injectedSite = inject('dreSite', null);
const dreSiteRef = injectedSite ?? computed(() => getPublicSiteBoot());

const MOBILE_MENU_SOCIAL_NETWORKS = new Set(['facebook', 'twitter', 'linkedin', 'instagram', 'youtube', 'whatsapp']);

const menuSocialItems = computed(() => {
    const site = dreSiteRef.value;
    const rows = Array.isArray(site?.social)
        ? site.social
              .map((row) => ({
                  network: typeof row?.network === 'string' ? row.network.trim().toLowerCase() : '',
                  href: typeof row?.href === 'string' ? row.href.trim() : '',
              }))
              .filter((row) => row.network && row.href && MOBILE_MENU_SOCIAL_NETWORKS.has(row.network))
        : [];

    const seen = new Set();
    const unique = rows.filter((row) => {
        if (seen.has(row.network)) {
            return false;
        }
        seen.add(row.network);

        return true;
    });

    const hasWhatsapp = unique.some((r) => r.network === 'whatsapp');
    const waFromNumber = siteWhatsappUrl(site?.whatsappNumber);
    if (!hasWhatsapp && waFromNumber) {
        unique.push({ network: 'whatsapp', href: waFromNumber });
    }

    return unique;
});

const showMobileMenuContact = computed(() => {
    const s = dreSiteRef.value;

    return Boolean(s?.phone1 || s?.phone2 || s?.email);
});

const showMobileMenuFooter = computed(() => showMobileMenuContact.value || menuSocialItems.value.length > 0);

function socialAriaLabel(network) {
    const key = `followUs.${network}`;

    return t(key);
}

const mobileBrandLogo = computed(
    () => dreSiteRef.value?.colourLogoUrl || dreSiteRef.value?.logoUrl || asset('public/images/logo-blue.png'),
);
const mobileBrandLogoAlt = computed(() => dreSiteRef.value?.logoAlt || t('brand.logoAlt'));
</script>

<style scoped>
.mobile-menu__social :deep(svg) {
    width: 20px;
    height: 20px;
}
</style>
