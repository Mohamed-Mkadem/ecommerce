<script setup lang="ts">
import Breadcrumb from "./Breadcrumb.vue";
const props = defineProps(["pageTitle"]);
</script>

<template>
    <div
        class="mb-6 flex flex-col gap-3 sm:justify-between sm:flex-row items-center"
    >
        <div>
            <h1
                class="text-3xl font-semibold text-slate-500 text-center sm:text-start"
            >
                {{ props.pageTitle }}
            </h1>
            <Breadcrumb :page-title="props.pageTitle"></Breadcrumb>
        </div>

        <slot></slot>
    </div>
</template>
