<script setup>
const props = defineProps({
    variant: {
        type: String,
        default: "default",
    },
});
</script>

<template>
    <input
        :class="props.variant === 'admin'
            ? 'mt-2 block w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-500 transition focus:outline-none focus:ring-2 focus:ring-primary/20 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-slate-200 file:px-4 file:py-3 file:font-medium file:text-slate-700 file:transition-colors hover:file:bg-slate-300 rtl:file:mr-0 rtl:file:ml-4 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-300 dark:file:bg-neutral-600 dark:file:text-neutral-100 dark:hover:file:bg-neutral-500'
            : 'block w-full cursor-pointer text-sm'"
    />
</template>
