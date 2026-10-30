<script setup>
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import Activity from "@/js/Components/Admin/Activity.vue";
const props = defineProps({
    activities: {
        type: Array,
        required: true,
    },
    withLinks: {
        type: Boolean,
        default: true,
    },
});
</script>
<template>
    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6">
        <h2 class="mb-4 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
            {{ $t("Activities") }}
        </h2>

        <div v-if="activities.data.length">
            <div
                v-for="(activity, index) in activities.data"
                :key="index"
                class="border-b border-slate-100 py-3 last:border-b-0 dark:border-neutral-700"
            >
                <Activity :activity="activity" :withLinks="props.withLinks" />
            </div>
            <Paginator
                class="mt-6"
                :links="activities.meta.links"
                :previous="activities.links.prev"
                :next="activities.links.next"
            />
        </div>

        <NotFound v-else></NotFound>
    </div>
</template>
