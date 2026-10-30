<script setup>
defineProps({
    message: { type: String, required: true },
});
</script>

<template>
    <aside
        role="note"
        class="mb-4 flex items-center gap-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-950 dark:border-sky-900/70 dark:bg-sky-950/30 dark:text-sky-100"
    >
        <i
            class="ri-information-line shrink-0 text-lg text-sky-700 dark:text-sky-300"
            aria-hidden="true"
        ></i>
        <p>{{ $t(message) }}</p>
    </aside>
</template>
