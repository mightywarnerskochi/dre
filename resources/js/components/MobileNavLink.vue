<template>
    <RouterLink v-slot="{ href, navigate, isActive }" :to="to" custom>
        <a
            :href="href"
            :class="combinedClass(isActive)"
            :aria-current="isActive ? 'page' : undefined"
            @click.prevent="handleClick(navigate)"
        >
            <slot />
        </a>
    </RouterLink>
</template>

<script setup>
import { RouterLink } from 'vue-router';
import { computed } from 'vue';

const props = defineProps({
    to: { type: [String, Object], required: true },
    /** Classes always applied when `activeClassExtra` is set */
    activeClassExtra: { type: String, default: '' },
    /** Override default link wrapper class */
    linkClass: { type: String, default: 'mobile-menu__link' },
    /** When true, only exact path matches (for home `/`) */
    exact: { type: Boolean, default: false },
});

const normalizedTo = computed(() => props.to);

function combinedClass(routeActive) {
    const classes = [];
    if (props.linkClass) classes.push(props.linkClass);
    if (props.activeClassExtra) classes.push(props.activeClassExtra);
    if (routeActive) classes.push('is-active');
    return classes.filter(Boolean).join(' ');
}

function handleClick(navigate) {
    navigate();
    document.querySelectorAll('.offcanvas.show').forEach((el) => {
        window.bootstrap?.Offcanvas?.getInstance(el)?.hide();
    });
}
</script>
