<script setup lang="ts">
const props = defineProps(["label", "type", "placeholder", "required"]);
const model = defineModel({
    type: String,
    required: true,
});
</script>

<template>
    <div class="mb-1">
        <label class="mb-2.5 block font-medium text-slate-600">{{
            props.label
        }}</label>
        <div class="relative">
            <input
                :required="required"
                v-model="model"
                :type="props.type"
                :placeholder="props.placeholder"
                class="w-full rounded-lg border border-stroke bg-transparent py-4 ltr-pl-6 ltr:pr-10 rtl:pr-6 rtl:pl-10 outline-none focus:border-primary focus-visible:shadow-none text-slate-600"
            />

            <span class="absolute ltr:right-4 rtl:left-4 top-4">
                <slot></slot>
            </span>
        </div>
    </div>
</template>
