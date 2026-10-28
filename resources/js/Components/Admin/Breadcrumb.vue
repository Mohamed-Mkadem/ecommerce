<script setup>
const props = defineProps(["pageTitle"]);
</script>

<template>
    <nav>
        <ol class="flex items-center gap-2 text-sm">
            <li>
                <Link
                    class="font-medium text-slate-500 transition-colors hover:text-primary dark:text-neutral-400 dark:hover:text-neutral-100"
                    :href="route('dashboard')"
                >
                    {{ $t("Dashboard") }}
                </Link>
            </li>
            <li aria-hidden="true" class="text-slate-300 dark:text-neutral-600">/</li>
            <li class="font-medium text-primary dark:text-neutral-200">{{ props.pageTitle }}</li>
        </ol>
    </nav>
</template>
