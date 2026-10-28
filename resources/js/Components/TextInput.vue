<script setup>
import { onMounted, ref } from "vue";

const props = defineProps({
    variant: {
        type: String,
        default: "default",
    },
});

const model = defineModel({
    type: [String, Number],
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        :class="props.variant === 'admin'
            ? 'mt-1 block w-full rounded-xl border-slate-200 bg-slate-50/50 text-neutral-800 shadow-sm placeholder:text-neutral-400 focus:border-primary focus:ring-primary/20 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 dark:placeholder:text-neutral-400 dark:focus:border-neutral-400 dark:focus:ring-neutral-500/30'
            : 'text-primary rounded-md border-editor shadow-sm focus:border-primary focus:ring-primary'"
        v-model="model"
        ref="input"
    />
</template>
