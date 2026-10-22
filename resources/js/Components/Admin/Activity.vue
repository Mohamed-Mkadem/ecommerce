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
        class="grid items-center md:grid-cols-[1fr_150px] gap-2"
        v-if="activity.causer"
    >
        <p class="text-black text-lg font-normal">
            {{
                `- ${activity.causer.name} ${$t(activity.description + ".by")}`
            }}
            <template v-if="subjectRoute">
                <Link
                    :href="route(subjectRoute.name, subjectRoute.params)"
                    class="text-sky-700 underline"
                    v-if="withLinks"
                >
                    #{{ activity.subject_id }}
                </Link>
            </template>
            <template v-else> #{{ activity.subject_id }} </template>
        </p>
        <p class="text-sm text-neutral-700" dir="ltr">
            {{ activity.created_at }}
        </p>
    </div>
    <div class="grid items-center md:grid-cols-[1fr_150px] gap-2" v-else>
        <p class="text-black text-lg font-normal">
            {{ `- ${$t("someone")} ${$t(activity.description + ".by")}` }}
        </p>
        <p class="text-sm text-neutral-700" dir="ltr">
            {{ activity.created_at }}
        </p>
    </div>
</template>
