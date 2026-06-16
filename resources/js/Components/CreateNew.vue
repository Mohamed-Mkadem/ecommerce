<script setup>
const props = defineProps(["href", "label"]);
</script>

<template>
    <Link
        :href="route(props.href)"
        class="bg-green-500 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
        >{{ props.label }}</Link
    >
</template>
