<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
    },
    href: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'button',
    },
});

const tag = computed(() => (props.href ? 'a' : 'button'));

const base =
    'inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2';
const variants = {
    primary:
        'bg-dre-accent text-white shadow-sm hover:bg-blue-600 focus-visible:outline-dre-accent',
    outline:
        'border border-white/40 bg-white/10 text-white backdrop-blur hover:bg-white/20 focus-visible:outline-white',
    outlineDark:
        'border border-slate-200 bg-white text-dre-navy hover:border-dre-accent hover:text-dre-accent focus-visible:outline-dre-accent',
    ghost: 'text-dre-navy hover:text-dre-accent',
};

const btnClass = computed(() => [base, variants[props.variant] || variants.primary]);
</script>

<template>
    <component
        :is="tag"
        :href="href || undefined"
        :type="href ? undefined : type"
        :class="btnClass"
    >
        <slot />
    </component>
</template>
