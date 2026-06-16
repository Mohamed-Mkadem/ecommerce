<script setup>
import AppLogo from "@/js/Components/AppLogo.vue";

const props = defineProps({
    text: String,
    color: String,
});
</script>

<template>
    <h2
        class="text-sky-900 text-center text-3xl font-semibold tracking-wider px-2 sm:px-0 sm:flex sm:items-center sm:gap-5 sm:justify-center"
    >
        <span>
            <AppLogo
                class="w-[40px] hidden sm:block"
                :color="props.color ? props.color : ''"
            />
        </span>
        <span>{{ text }}</span>
        <span>
            <AppLogo
                class="w-[40px] hidden sm:block"
                :color="props.color ? props.color : ''"
            />
        </span>
    </h2>
</template>
