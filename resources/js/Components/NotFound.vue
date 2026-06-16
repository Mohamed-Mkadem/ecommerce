<script setup>
defineProps({
    message: {
        type: String,
        default: "There is no results",
    },
});
</script>

<template>
    <div
        class="flex flex-col items-center justify-center text-center gap-4 py-16"
    >
        <!-- Icon -->
        <i class="ri-information-line text-red-500 text-6xl"></i>

        <!-- Message -->
        <p class="text-gray-700 text-lg font-medium">{{ $t(message) }}</p>
    </div>
</template>
