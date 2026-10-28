<script setup>
import Status from "@/js/Components/Status.vue";

defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["delete"]);
</script>

<template>
    <article
        class="flex flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/30"
    >
        <header class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 dark:border-neutral-700">
            <div class="flex min-w-0 items-center gap-3">
                <Link :href="route('orders.show', order)"
                    class="shrink-0 text-lg font-bold text-primary underline decoration-slate-300 underline-offset-4 dark:text-neutral-100 dark:decoration-neutral-600">
                    #{{ order.id }}
                </Link>
                <Status :status="order.status" class="px-2 py-1" />
            </div>
            <div class="flex shrink-0 items-center gap-1.5">
                <Link :href="route('orders.show', order)"
                    class="rounded-lg bg-slate-100 p-2 text-lg text-slate-600 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
                    <i class="ri-eye-line" aria-hidden="true"></i>
                </Link>
                <ModalLink v-if="order.status == 'pending' || order.status == 'abandoned'"
                    :close-button="false" :href="route('orders.edit', order)"
                    class="rounded-lg bg-slate-100 p-2 text-lg text-slate-600 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
                    <i class="ri-edit-line" aria-hidden="true"></i>
                </ModalLink>
                <button @click="emit('delete', order)"
                    class="rounded-lg bg-red-50 p-2 text-lg text-red-600 transition hover:bg-red-100 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/70">
                    <i class="ri-delete-bin-5-fill" aria-hidden="true"></i>
                </button>
            </div>
        </header>

        <div class="flex-1 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <Link v-if="order.client.deleted_at == null" :href="route('clients.show', order.client)"
                        class="block truncate text-lg font-semibold text-slate-800 hover:text-primary hover:underline dark:text-neutral-100 dark:hover:text-white">
                        {{ order.client_name }}
                    </Link>
                    <p v-else class="text-lg font-semibold text-slate-800 dark:text-neutral-100">
                        {{ order.client_name }}
                        <small class="font-normal text-red-500">({{ $t("Deleted") }})</small>
                    </p>
                    <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500 dark:text-neutral-400">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="ri-phone-line" aria-hidden="true"></i>
                            {{ order.phone }}<template v-if="order.phone2"> / {{ order.phone2 }}</template>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="ri-shopping-cart-line" aria-hidden="true"></i>
                            {{ order.client.orders_count ?? 0 }}
                        </span>
                        <span v-if="order.client.delivery_rate !== null && order.client.delivery_rate !== undefined"
                            v-tippy="$t('client.delivery_rate_tooltip')" :title="$t('client.delivery_rate_tooltip')"
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                            :class="{
                                'bg-green-100 text-green-800': order.client.delivery_rate >= 70,
                                'bg-yellow-100 text-yellow-800': order.client.delivery_rate >= 40 && order.client.delivery_rate < 70,
                                'bg-red-100 text-red-800': order.client.delivery_rate < 40,
                            }">
                            <i class="ri-truck-line" aria-hidden="true"></i>
                            {{ order.client.delivery_rate }}%
                        </span>
                    </div>
                </div>
                <div class="shrink-0 sm:text-end">
                    <p class="text-xl font-bold text-primary dark:text-neutral-100">
                        {{ `${order.amount} ${$t("currency")}` }}
                    </p>
                    <p v-if="order.coupon" class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                        {{ `"${order.coupon.code}" ${$t("applied")} (${order.coupon.value}%)` }}
                        <small v-if="order.coupon.deleted_at != null" class="text-red-500">({{ $t("Deleted") }})</small>
                    </p>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-2">
                <div class="rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-neutral-700/60">
                    <p class="text-xs font-medium text-slate-500 dark:text-neutral-400">{{ $t("State") }}</p>
                    <p class="mt-1 truncate text-sm font-semibold text-slate-700 dark:text-neutral-200" dir="auto">{{ order.state.name }}</p>
                </div>
                <div v-if="order.shipper" class="rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-neutral-700/60">
                    <p class="text-xs font-medium text-slate-500 dark:text-neutral-400">{{ $t("Shipper") }}</p>
                    <p class="mt-1 truncate text-sm font-semibold text-slate-700 dark:text-neutral-200">
                        {{ order.shipper.name }}
                        <span v-if="order.free_shipping">({{ $t("Free") }})</span>
                        <small v-if="order.shipper.deleted_at != null" class="text-red-500">({{ $t("Deleted") }})</small>
                    </p>
                </div>
            </div>
        </div>

        <footer class="grid grid-cols-2 gap-3 border-t border-slate-100 bg-slate-50/70 px-4 py-3 dark:border-neutral-700 dark:bg-neutral-900/30">
            <div>
                <p class="flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-neutral-400">
                    <i class="ri-calendar-event-line" aria-hidden="true"></i>
                    {{ $t("Delivery_date") }}
                </p>
                <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-neutral-200">{{ order.delivery_date }}</p>
            </div>
            <div>
                <p class="flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-neutral-400">
                    <i class="ri-calendar-check-line" aria-hidden="true"></i>
                    {{ $t("Placed_at") }}
                </p>
                <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-neutral-200">{{ order.created_at }}</p>
            </div>
        </footer>
    </article>
</template>
