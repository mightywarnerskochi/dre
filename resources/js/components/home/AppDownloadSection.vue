<script setup>
defineProps({
    appCta: { type: Object, required: true },
});
</script>

<template>
    <section class="dre-app">
        <div class="dre-container dre-app__grid">
            <div class="dre-app__copy">
                <h2 class="dre-app__title">{{ appCta.title }}</h2>
                <p v-if="appCta.subtitle" class="dre-section__desc" style="margin-top: 0.75rem">
                    {{ appCta.subtitle }}
                </p>
                <div class="dre-app__stores">
                    <a :href="appCta.playStoreUrl" class="dre-btn dre-btn--store">Google Play</a>
                    <a :href="appCta.appStoreUrl" class="dre-btn dre-btn--store-outline">App Store</a>
                </div>
            </div>
            <div class="dre-app__aside">
                <div class="dre-app__qr">
                    <img
                        :src="appCta.qrCodeUrl"
                        alt="App download QR code"
                        width="140"
                        height="140"
                    />
                </div>
                <div class="dre-app__phone" aria-hidden="true">
                    <div class="dre-app__phone-inner">
                        <div class="dre-app__phone-notch">DRE</div>
                        <div class="dre-app__phone-body">
                            <div class="dre-app__phone-line dre-app__phone-line--short" />
                            <div class="dre-app__phone-line" />
                            <div class="dre-app__phone-line dre-app__phone-line--short" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
