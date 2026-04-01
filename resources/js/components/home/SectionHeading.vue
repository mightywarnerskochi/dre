<script setup>
defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
    light: { type: Boolean, default: false },
});
</script>

<template>
    <div class="mx-auto max-w-3xl text-center">
        <h2
            :class="[
                'text-3xl font-bold tracking-tight sm:text-4xl',
                light ? 'text-white' : 'text-dre-navy',
            ]"
        >
            {{ title }}
        </h2>
        <p
            v-if="description"
            :class="[
                'mt-4 text-base leading-relaxed',
                light ? 'text-white/80' : 'text-slate-600',
            ]"
        >
            {{ description }}
        </p>
    </div>
</template>
