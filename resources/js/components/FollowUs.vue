<template>
    <section v-if="socialLinks.length" class="follow-us">
        <div class="container-ctn">
            <h2 class="follow-us__title">{{ t('followUs.title') }}</h2>
            <ul class="follow-us__list" :aria-label="t('followUs.socialAria')">
                <li v-for="item in socialLinks" :key="item.network">
                    <a
                        :href="item.href"
                        class="follow-us__link"
                        target="_blank"
                        rel="noopener noreferrer"
                        :aria-label="t('followUs.' + item.network)"
                    >
                        <FollowUsNetworkIcon :network="item.network" />
                    </a>
                </li>
            </ul>
        </div>
    </section>
</template>

<script setup>
import { computed, inject } from 'vue';
import { useI18n } from 'vue-i18n';
import FollowUsNetworkIcon from '@/components/FollowUsNetworkIcon.vue';
import { getPublicSiteBoot } from '@/utils/publicSite';

const { t } = useI18n();

const injected = inject('dreSite', null);
const dreSite = injected ?? computed(() => getPublicSiteBoot());

const socialLinks = computed(() => dreSite.value.social ?? []);
</script>
