<script setup>
import { getActivitySubjectRoute } from "@/js/Utils/activityRoutes";

const props = defineProps({
    activity: {
        type: Object,
        required: true,
    },
    withLinks: {
        type: Boolean,
        default: true,
    },
});
const subjectRoute = getActivitySubjectRoute(
    props.activity.subject_type,
    props.activity.subject_id,
);
</script>
<template>
    <div
        class="grid items-center gap-2 md:grid-cols-[minmax(0,1fr)_180px]"
        v-if="activity.causer"
    >
        <p class="text-base font-normal text-slate-700 dark:text-neutral-200">
            {{
                `- ${activity.causer.name} ${$t(activity.description + ".by")}`
            }}
            <template v-if="subjectRoute">
                <Link
                    :href="route(subjectRoute.name, subjectRoute.params)"
                    class="text-sky-700 underline hover:text-sky-800 dark:text-sky-400 dark:hover:text-sky-300"
                    v-if="withLinks"
                >
                    #{{ activity.subject_id }}
                </Link>
            </template>
            <template v-else> #{{ activity.subject_id }} </template>
        </p>
        <p class="text-sm text-slate-500 dark:text-neutral-400" dir="ltr">
            {{ activity.created_at }}
        </p>
    </div>
    <div class="grid items-center gap-2 md:grid-cols-[minmax(0,1fr)_180px]" v-else>
        <p class="text-base font-normal text-slate-700 dark:text-neutral-200">
            {{ `- ${$t("someone")} ${$t(activity.description + ".by")}` }}
        </p>
        <p class="text-sm text-slate-500 dark:text-neutral-400" dir="ltr">
            {{ activity.created_at }}
        </p>
    </div>
</template>
