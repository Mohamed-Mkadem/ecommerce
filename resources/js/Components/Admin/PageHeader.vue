<script setup lang="ts">
import Breadcrumb from "./Breadcrumb.vue";
const props = defineProps(["pageTitle"]);
</script>

<template>
    <div class="mb-6 flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
        <div>
            <h1
                class="text-3xl font-semibold tracking-tight text-primary text-center dark:text-neutral-100 sm:text-start">
                {{ props.pageTitle }}
            </h1>
            <Breadcrumb :page-title="props.pageTitle"></Breadcrumb>
        </div>

        <slot></slot>
    </div>
</template>
