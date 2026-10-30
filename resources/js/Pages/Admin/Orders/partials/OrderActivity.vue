<script setup>
const props = defineProps(["activity"]);
</script>

<template>
    <div class="grid grid-cols-[auto,1fr] gap-4 items-center mb-4">
        <i
            class="flex h-10 w-10 items-center justify-center rounded-full bg-sky-800 text-lg text-white"
            :class="activity.icon"
        ></i>
        <p class="text-slate-700 dark:text-neutral-200">
            <span
                class="block text-lg font-semibold capitalize"
                v-if="!activity.causer"
                >{{ $t(activity.description) }}</span
            >
            <span class="block text-lg font-semibold capitalize" v-else
                >{{ $t(activity.description) }}
                <Link
                    v-if="$page.props.auth.user.role == 'admin'"
                    :href="route('employees.show', activity.causer.id)"
                    class="text-sky-700 underline hover:text-sky-800 dark:text-sky-400 dark:hover:text-sky-300"
                >
                    ({{ activity.causer.name }})
                </Link>
                <span class="text-slate-600 dark:text-neutral-300" v-else
                    >({{ activity.causer.name }})</span
                >
            </span>

            <small class="mt-0.5 block text-sm text-slate-500 dark:text-neutral-400">{{ activity.created_at }}</small>
        </p>
    </div>
</template>
