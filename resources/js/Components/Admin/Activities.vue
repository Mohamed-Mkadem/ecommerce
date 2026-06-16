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
    <div class="bg-white rounded-lg shadow-1 p-4 mt-4">
        <h2 class="font-bold text-sky-800 mb-4 text-2xl">
            {{ $t("Activities") }}
        </h2>

        <div v-if="activities.data.length">
            <div
                v-for="(activity, index) in activities.data"
                :key="index"
                class="border-b border-slate-200 py-2"
            >
                <Activity :activity="activity" :withLinks="props.withLinks" />
            </div>
            <Paginator
                class="mt-8"
                :links="activities.meta.links"
                :previous="activities.links.prev"
                :next="activities.links.next"
            />
        </div>

        <NotFound v-else></NotFound>
    </div>
</template>
