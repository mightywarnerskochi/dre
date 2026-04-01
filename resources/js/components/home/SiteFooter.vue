<script setup>
defineProps({
    footer: { type: Object, required: true },
});
</script>

<template>
    <footer id="contact" class="dre-footer">
        <div class="dre-container">
            <div class="dre-footer__grid">
                <div v-for="(col, idx) in footer.columns" :key="idx">
                    <h3 class="dre-footer__col-title">{{ col.title }}</h3>
                    <ul class="dre-footer__links">
                        <li v-for="link in col.links" :key="link.label">
                            <a :href="link.href">{{ link.label }}</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="dre-footer__col-title">Contact</h3>
                    <ul class="dre-footer__contact">
                        <li>
                            <a :href="'tel:' + footer.contact.phone.replace(/\s/g, '')">{{
                                footer.contact.phone
                            }}</a>
                        </li>
                        <li v-if="footer.contact.phoneAlt">
                            <a :href="'tel:' + footer.contact.phoneAlt.replace(/\s/g, '')">{{
                                footer.contact.phoneAlt
                            }}</a>
                        </li>
                        <li>
                            <a :href="'mailto:' + footer.contact.email">{{ footer.contact.email }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="dre-footer__bar">
                <p class="dre-footer__copy">{{ footer.copyright }}</p>
                <div class="dre-footer__meta">
                    <button type="button" aria-label="Language">EN</button>
                    <button type="button" aria-label="Accessibility">A</button>
                </div>
            </div>
        </div>
    </footer>
</template>
