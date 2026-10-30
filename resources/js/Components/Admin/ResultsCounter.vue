<script setup>
const props = defineProps({
    count: {
        type: [Number, String],
        required: true,
    },
    label: {
        type: String,
        default: "Results",
    },
    icon: {
        type: String,
        default: "ri-box-3-line",
    },
});
</script>

<template>
    <div class="mb-4 flex items-center gap-2" aria-live="polite">
        <i :class="[props.icon, 'text-lg text-slate-500 dark:text-neutral-400']" aria-hidden="true"></i>
        <h2 class="text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
            {{ $t(props.label) }}:
        </h2>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600 dark:bg-neutral-800 dark:text-neutral-300">
            {{ props.count }}
        </span>
    </div>
</template>
