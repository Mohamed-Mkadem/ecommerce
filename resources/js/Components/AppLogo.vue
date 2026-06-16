<script setup>
import { usePage } from "@inertiajs/vue3";

const props = defineProps(["color"]);
import whiteLogo from "@/assets/images/logo-white.webp";
import goldLogo from "@/assets/images/logo-gold.webp";
import blueLogo from "@/assets/images/logo-blue.webp";
import blackLogo from "@/assets/images/logo-black.webp";
</script>

<template>
    <img
        class=""
        :src="blackLogo"
        alt="Logo patisserie boughanmi"
        v-if="props.color == 'black'"
    />
    <img
        class=""
        :src="whiteLogo"
        alt="Logo patisserie boughanmi"
        v-else-if="props.color == 'white'"
    />
    <img
        class=""
        :src="blueLogo"
        alt="Logo patisserie boughanmi"
        v-else-if="props.color == 'blue'"
    />

    <img class="" :src="goldLogo" alt="Logo patisserie boughanmi" v-else />
</template>
