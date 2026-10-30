<script setup lang="ts">
const props = defineProps([
    "label",
    "type",
    "placeholder",
    "required",
    "id",
    "autocomplete",
]);
const model = defineModel({
    type: String,
    required: true,
});
</script>

<template>
    <div class="mb-1">
        <label :for="props.id" class="mb-2 block text-sm font-semibold text-slate-700">{{
            props.label
        }}</label>
        <div class="relative">
            <input
                :required="required"
                :id="props.id"
                :autocomplete="props.autocomplete"
                v-model="model"
                :type="props.type"
                :placeholder="props.placeholder"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 ltr:pl-4 ltr:pr-11 rtl:pr-4 rtl:pl-11 text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-teal-700 focus:bg-white focus:ring-4 focus:ring-teal-700/10"
            />

            <span class="absolute top-1/2 -translate-y-1/2 text-slate-400 ltr:right-4 rtl:left-4">
                <slot></slot>
            </span>
        </div>
    </div>
</template>
