<script setup>
defineProps({
    message: { type: String, required: true },
});
</script>

<template>
    <aside
        role="note"
        class="mb-4 flex items-center gap-3 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-950"
    >
        <i
            class="ri-information-line shrink-0 text-lg text-sky-700"
            aria-hidden="true"
        ></i>
        <p>{{ $t(message) }}</p>
    </aside>
</template>
