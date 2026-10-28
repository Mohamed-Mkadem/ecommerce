<script setup>
const props = defineProps({
    variant: {
        type: String,
        default: "default",
    },
});

defineOptions({ inheritAttrs: false });
const model = defineModel();
</script>

<template>
    <div class="relative">
        <select
            v-model="model"
            v-bind="$attrs"
            :class="props.variant === 'admin'
                ? 'mt-1 block w-full appearance-none rounded-xl border-slate-200 bg-slate-50/50 text-neutral-800 shadow-sm focus:border-primary focus:ring-primary/20 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 dark:focus:border-neutral-400 dark:focus:ring-neutral-500/30'
                : 'text-primary rounded-md border-editor shadow-sm focus:border-primary focus:ring-primary'"
        >
            <slot />
        </select>
        <i
            v-if="props.variant === 'admin'"
            class="ri-arrow-down-s-line pointer-events-none absolute top-1/2 -translate-y-1/2 text-lg text-neutral-400 ltr:right-3 rtl:left-3 dark:text-neutral-400"
            aria-hidden="true"
        ></i>
    </div>
</template>
