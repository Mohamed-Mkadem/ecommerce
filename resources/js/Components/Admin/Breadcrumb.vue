<script setup>
const props = defineProps(["pageTitle"]);
</script>

<template>
    <nav>
        <ol class="flex items-center gap-2">
            <li>
                <Link
                    class="text-slate-500 hover:underline hover:text-black text-sm"
                    :href="route('dashboard')"
                >
                    {{ $t("Dashboard") }}
                </Link>
            </li>
            <li>-</li>
            <li class="text-black text-sm">{{ props.pageTitle }}</li>
        </ol>
    </nav>
</template>
