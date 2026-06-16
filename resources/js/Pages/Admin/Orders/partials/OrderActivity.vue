<script setup>
const props = defineProps(["activity"]);
</script>

<template>
    <div class="grid grid-cols-[auto,1fr] gap-4 items-center mb-4">
        <i
            class="text-lg text-white bg-sky-800 rounded-full w-[40px] h-[40px] flex items-center justify-center"
            :class="activity.icon"
        ></i>
        <p class="text-primary">
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
                    class="text-sky-700 hover:text-sky-600 underline"
                >
                    ({{ activity.causer.name }})
                </Link>
                <span class="text-neutral-600" v-else
                    >({{ activity.causer.name }})</span
                >
            </span>

            <small class="block -mt-1">{{ activity.created_at }}</small>
        </p>
    </div>
</template>
