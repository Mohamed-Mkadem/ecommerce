<script setup>
const props = defineProps(["href"]);
</script>

<template>
    <Link :href="route(href)" class="text-justify flex items-center gap-2">
        <i class="ri-cake-3-line text-3xl"></i>
        <div class="font-semibold" dir="ltr">
            <span class="block">BOUGHANMI</span>
            <span class="block tracking-wide uppercase">Pâtisserie</span>
        </div>
    </Link>
</template>
