<script setup>
import { onMounted, ref } from "vue";

const model = defineModel({
    type: [String, Number],
    required: true,
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
        class="text-primary rounded-md border-editor shadow-sm focus:border-primary focus:ring-primary"
        v-model="model"
        ref="input"
    />
</template>
