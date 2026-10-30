<script setup>
defineProps({
    wrapper: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/30">
        <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
            <p
                class="inline-flex items-center gap-1.5 rounded-lg bg-violet-50 px-3 py-1.5 text-sm font-semibold text-violet-700 dark:bg-violet-950/40 dark:text-violet-300"
            >
                <i class="ri-cake-3-line text-base"></i>
                <span>{{ wrapper.products_count }}</span>
            </p>
            <p
                class="rounded-lg px-3 py-1.5 text-sm font-semibold text-white"
                :class="{
                    'bg-red-500': !wrapper.is_active,
                    'bg-green-600': wrapper.is_active,
                }"
            >
                {{
                    wrapper.is_active
                        ? $t("Wrapper.active")
                        : $t("Wrapper.inactive")
                }}
            </p>
        </div>

        <div class="mx-4 flex h-48 items-center justify-center overflow-hidden rounded-xl bg-slate-50 p-3 dark:bg-neutral-900/50">
            <img
                :src="wrapper.main_image_url"
                :alt="wrapper.title"
                class="h-full w-full object-contain transition duration-300 group-hover:scale-[1.02]"
            />
        </div>

        <div class="mt-4 flex-1 px-4 text-center">
            <Link
                :href="route('wrappers.show', wrapper.slug)"
                class="line-clamp-2 block text-lg font-semibold text-slate-800 transition-colors hover:text-primary hover:underline dark:text-neutral-100 dark:hover:text-white"
                :title="wrapper.title"
            >
                {{ wrapper.title }}
            </Link>

        </div>

        <Link
            :href="route('wrappers.show', wrapper.slug)"
            class="mx-4 mb-4 mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-100 dark:border-neutral-700 dark:bg-neutral-700/60 dark:text-neutral-200 dark:hover:bg-neutral-700"
        >
            {{ $t("Wrapper.view_details") }}
            <i class="ri-arrow-right-line transition-transform group-hover:translate-x-0.5 rtl:rotate-180" aria-hidden="true"></i>
        </Link>
    </article>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
