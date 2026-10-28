<script setup>
const props = defineProps(["href", "label"]);
</script>

<template>
    <Link
        :href="route(props.href)"
        class="inline-flex items-center justify-center rounded-xl border border-green-600 bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-green-700 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-800"
        >{{ props.label }}</Link
    >
</template>
