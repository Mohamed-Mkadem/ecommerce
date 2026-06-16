<script setup>
import { usePage } from "@inertiajs/vue3";

const settings = usePage().props.settings;
</script>

<template>
    <div
        class="bg-sky-900 text-white font-bold py-3 px-4 shadow-md tracking-wide"
        :class="{
            hidden: !settings.is_visible,
        }"
    >
        <div class="text-center">
            <p>{{ settings.text_content }}</p>
        </div>
    </div>
</template>
