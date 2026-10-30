<script setup>
defineProps(["product", "period"]);

const statuses = [
    {
        key: "pending_orders_count",
        label: "pending",
        icon: "ri-hourglass-fill",
        color: "bg-amber-50 text-amber-800 dark:bg-amber-500/15 dark:text-amber-200",
    },
    {
        key: "canceled_orders_count",
        label: "canceled",
        icon: "ri-close-circle-fill",
        color: "bg-rose-50 text-rose-800 dark:bg-rose-500/15 dark:text-rose-200",
    },
    {
        key: "confirmed_orders_count",
        label: "confirmed",
        icon: "ri-checkbox-fill",
        color: "bg-emerald-50 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-200",
    },
    {
        key: "shipped_orders_count",
        label: "shipped",
        icon: "ri-truck-fill",
        color: "bg-violet-50 text-violet-800 dark:bg-violet-500/15 dark:text-violet-200",
    },
    {
        key: "delivered_orders_count",
        label: "delivered",
        icon: "ri-home-9-fill",
        color: "bg-sky-50 text-sky-800 dark:bg-sky-500/15 dark:text-sky-200",
    },
    {
        key: "returned_orders_count",
        label: "returned",
        icon: "ri-truck-fill",
        color: "bg-slate-100 text-slate-700 dark:bg-neutral-700 dark:text-neutral-200",
    },
];
</script>

<template>
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
        <div class="flex min-w-0 items-center gap-3">
            <img
                :src="product.main_image_url"
                :alt="product.name"
                class="h-14 w-14 shrink-0 rounded-xl object-cover"
            />
            <div class="min-w-0 flex-1">
                <Link
                    :href="route('products.show', product)"
                    class="block truncate font-semibold text-slate-800 transition-colors hover:text-teal-700 hover:underline dark:text-neutral-100 dark:hover:text-teal-300"
                    :title="product.name"
                >
                    {{ product.name }}
                </Link>
                <div class="mt-1 flex flex-wrap items-center gap-x-2 text-sm text-slate-500 dark:text-neutral-400">
                    <span v-if="product.wrapper_title">{{ product.wrapper_title }}</span>
                    <span v-if="product.wrapper_title" aria-hidden="true">·</span>
                    <span>{{ product.price }} {{ $t("Product.currency") }}</span>
                </div>
            </div>
            <span class="shrink-0 rounded-lg bg-teal-50 px-3 py-2 text-center text-sm font-semibold text-teal-800 dark:bg-teal-500/15 dark:text-teal-200">
                {{ product.counts[period].orders_count }}
                <span class="block text-xs font-medium">{{ $t("Orders") }}</span>
            </span>
        </div>

        <div class="mt-4 grid grid-cols-[repeat(auto-fit,minmax(115px,1fr))] gap-2">
            <div
                v-for="status in statuses"
                :key="status.key"
                class="rounded-xl px-3 py-2.5 text-center"
                :class="status.color"
            >
                <p class="text-lg font-semibold leading-tight">
                    {{ product.counts[period][status.key] }}
                </p>
                <div class="mt-1 flex items-center justify-center gap-1.5 text-xs font-medium">
                    <i :class="status.icon" aria-hidden="true"></i>
                    <span>{{ $t(status.label) }}</span>
                </div>
            </div>
        </div>
    </article>
</template>
