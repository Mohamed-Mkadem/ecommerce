<script setup>
import { usePage } from "@inertiajs/vue3";

const props = defineProps(["color"]);

import logo from "@/assets/images/logo.png";
</script>

<template>
    <img class="" :src="logo" alt="Sweetia Logo" v-if="props.color == 'white'" />

    <img class="" :src="logo" alt="Sweetia Logo" v-else />
</template>
