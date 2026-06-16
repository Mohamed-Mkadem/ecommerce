<script setup>
const props = defineProps({
    href: String,
    label: String,
    theme: String,
});
</script>

<template>
    <Link
        :href="route(props.href)"
        class="font-semibold px-6 py-3 rounded-md transition"
        :class="{
            'bg-gold text-primary hover:bg-opacity-80 ':
                theme == 'gold-primary',
            'bg-gold text-white hover:bg-opacity-80 ': theme == 'gold-white',
            'bg-primary text-gold hover:bg-opacity-80 ':
                theme == 'primary-gold',
            'bg-primary text-white hover:bg-opacity-70 ':
                theme == 'primary-white',
        }"
    >
        {{ props.label }}
    </Link>
</template>
