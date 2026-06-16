<script setup>
import { computed } from "vue";

const props = defineProps({ status: Number });

const description = computed(() => {
    return {
        503: "error503",
        500: "error500",
        404: "error404",
        403: "error403",
    }[props.status];
});
</script>

<template>
    <Head :title="status" />
    <div
        class="min-h-screen p-4 lg-p6 flex items-center justify-center bg-zinc-100"
    >
        <div class="text-center">
            <h1 class="text-sky-600 text-[60px] mb-4 md:text-9xl font-semibold">
                {{ status }}
            </h1>
            <p class="text-neutral-700 text-lg">{{ $t(description) }}</p>
        </div>
    </div>
</template>
