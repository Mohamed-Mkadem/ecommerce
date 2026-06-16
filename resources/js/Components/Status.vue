<script setup>
const props = defineProps({
    status: { type: String },
});
</script>

<template>
    <p
        class="rounded capitalize font-medium"
        :class="{
            'bg-yellow-200	text-orange-800': status == 'pending',
            'bg-zinc-200	text-zinc-700': status == 'returned',
            'bg-green-200	text-green-800': status == 'confirmed',
            'bg-red-200	text-red-800': status == 'canceled',
            'bg-purple-200	text-purple-800': status == 'shipped',
            'bg-blue-200	text-blue-800': status == 'delivered',
            'bg-sky-800	text-slate-200': status == 'abandoned',
        }"
    >
        {{ $t(status) }}
    </p>
</template>
