<script setup>
import { usePage } from "@inertiajs/vue3";

const props = defineProps(["color"]);
import whiteLogo from "@/assets/images/logo-white.webp";
import blueLogo from "@/assets/images/logo-blue.webp";
</script>

<template>
    <img
        class=""
        :src="whiteLogo"
        alt="Logo patisserie boughanmi"
        v-if="props.color == 'white'"
    />

    <img class="" :src="blueLogo" alt="Logo patisserie boughanmi" v-else />
</template>
