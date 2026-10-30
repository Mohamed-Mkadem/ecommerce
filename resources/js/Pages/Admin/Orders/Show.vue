<script setup>
import Status from "@/js/Components/Status.vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { trans } from "laravel-vue-i18n";
import Swal from "sweetalert2";
import { router, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Notes from "@/js/Pages/Admin/Notes/Notes.vue";
import OrderActivity from "@/js/Pages/Admin/Orders/partials/OrderActivity.vue";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
const toast = useToast();
const props = defineProps(["order"]);

function deleteOrder(order) {
    Swal.fire({
        title: trans("Dialog.title"),
        text: trans("Dialog.warning"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: trans("Dialog.confirmDeletionButtonText"),
        cancelButtonText: trans("Dialog.cancelButtonText"),
        width: 450,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("orders.destroy", order), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Order.deleted"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                },
            });
        }
    });
}
const form = useForm({
    status: props.order.data.status,
});
function updateStatus(status) {
    Swal.fire({
        title: trans("Dialog.title"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#6b7280",
        confirmButtonText: trans("Yes"),
        cancelButtonText: trans("Dialog.cancelButtonText"),
        width: 450,
    }).then((result) => {
        if (result.isConfirmed) {
            form.status = status;
            form.patch(route("orders.updateStatus", props.order.data), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Updated"),
                        text: trans("Order.updated_successfully"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                },
            });
        }
    });
}

function markAsNrp() {
    Swal.fire({
        title: trans("Dialog.title"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#6b7280",
        confirmButtonText: trans("Dialog.MarkThisAsNrp"),
        cancelButtonText: trans("Dialog.cancelButtonText"),
        width: 450,
    }).then((result) => {
        if (result.isConfirmed) {
            form.post(route("nrp.store", props.order.data), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Updated"),
                        text: trans("Order.marked_as_nrp_successfully"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                },
            });
        }
    });
}
</script>

<template>
    <Head :title="$t('Order Details')" />

    <PageHeader :page-title="`${$t('Order Details')} : #${order.data.id}`">
        <div class="flex items-center gap-4">
            <a
                class="inline-flex items-center gap-2 rounded-xl bg-sky-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900"
                v-if="
                    order.data.status == 'shipped' ||
                    order.data.status == 'confirmed'
                "
                :href="`/orders/${order.data.id}/print-invoice`"
                target="_blank"
                rel="noopener noreferrer"
            >
                {{ $t("Print Invoice") }}
            </a>
            <ModalLink
                v-if="
                    order.data.status == 'pending' ||
                    order.data.status == 'abandoned'
                "
                :close-button="false"
                :href="route('orders.edit', order.data)"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900"
            >
                <i class="ri-edit-line"></i>
                {{ $t("Edit") }}
            </ModalLink>

            <button
                @click="deleteOrder(order.data)"
                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900"
            >
                {{ $t("Delete") }}
            </button>
        </div>
    </PageHeader>

    <div
        class="mb-6 flex flex-wrap items-center justify-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
    >
        <button
            v-if="
                order.data.status == 'pending' ||
                order.data.status == 'abandoned'
            "
            @click="markAsNrp()"
            class="inline-flex items-center gap-2 rounded-xl bg-sky-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-800"
        >
            {{ $t("Mark as NRP") }}
            <span v-if="order.data.nrp && order.data.nrp.tries > 0">
                {{ order.data.nrp.tries + 1 }}
            </span>
        </button>
        <button
            v-if="
                order.data.status == 'pending' ||
                order.data.status == 'abandoned'
            "
            @click="updateStatus('confirmed')"
            class="rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="!order.data.locality || !order.data.shipper"
            :class="{
                'bg-opacity-65': !order.data.locality || !order.data.shipper,
            }"
        >
            {{ $t("Confirm Order") }}
        </button>
        <button
            v-if="order.data.status == 'confirmed'"
            @click="updateStatus('shipped')"
            class="rounded-xl bg-yellow-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-yellow-700"
        >
            {{ $t("Ship Order") }}
        </button>
        <button
            v-if="order.data.status == 'shipped'"
            @click="updateStatus('delivered')"
            class="rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700"
        >
            {{ $t("Deliver Order") }}
        </button>
        <button
            v-if="order.data.status == 'shipped'"
            @click="updateStatus('returned')"
            class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
        >
            {{ $t("Return Order") }}
        </button>

        <button
            v-if="
                order.data.status != 'canceled' &&
                order.data.status != 'returned' &&
                order.data.status != 'delivered'
            "
            @click="updateStatus('canceled')"
            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700"
        >
            {{ $t("Cancel Order") }}
        </button>
    </div>

    <div
        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
    >
        <h2
            class="mb-4 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
        >
            {{ $t("Order Information") }}
        </h2>

        <div class="space-y-4 text-slate-700 dark:text-neutral-200">
            <Status :status="order.data.status" class="px-2 py-1 w-max mb-2" />
            <div class="flex gap-2 items-center">
                <i class="ri-user-line text-2xl text-slate-400"></i>
                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        v-if="order.data.client.deleted_at == null"
                        :href="route('clients.show', order.data.client)"
                        class="block text-base font-semibold text-sky-700 transition hover:text-sky-800 hover:underline dark:text-sky-400 dark:hover:text-sky-300 sm:text-lg"
                    >
                        {{ order.data.client_name }}
                    </Link>
                    <p v-else class="text-lg font-medium block">
                        {{ order.data.client_name }}
                        <small class="text-red-500">
                            ({{ $t("Deleted") }})
                        </small>
                    </p>
                    <span class="text-sm text-slate-500 dark:text-neutral-400">
                        ({{ order.data.client.orders_count ?? 0 }}
                        {{ $t("orders") }})
                    </span>
                    <span
                        v-if="
                            order.data.client.delivery_rate !== null &&
                            order.data.client.delivery_rate !== undefined
                        "
                        :title="$t('client.delivery_rate_tooltip')"
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                        :class="{
                            'bg-green-100 text-green-800':
                                order.data.client.delivery_rate >= 70,
                            'bg-yellow-100 text-yellow-800':
                                order.data.client.delivery_rate >= 40 &&
                                order.data.client.delivery_rate < 70,
                            'bg-red-100 text-red-800':
                                order.data.client.delivery_rate < 40,
                        }"
                    >
                        <i class="ri-truck-line"></i>
                        {{ order.data.client.delivery_rate }}%
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500 dark:bg-neutral-700 dark:text-neutral-300"
                        :title="$t('client.delivery_rate_tooltip')"
                    >
                        <i class="ri-truck-line"></i>
                        N/A
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <i
                    class="ri-customer-service-2-fill text-slate-400 text-2xl"
                ></i>

                <p dir="ltr" class="text-lg mb-1">
                    {{ order.data.phone }}
                    <span v-if="order.data.phone2">
                        / {{ order.data.phone2 }}</span
                    >
                </p>
            </div>

            <div class="flex gap-2 items-center">
                <i class="ri-money-dollar-box-line text-2xl text-slate-400"></i>
                <p
                    class="text-2xl font-bold tracking-tight text-rose-600 dark:text-rose-400"
                >
                    {{ `${order.data.amount} ${$t("currency")}` }}
                </p>
                <p
                    v-if="order.data.coupon"
                    class="text-sm text-slate-600 dark:text-neutral-300"
                >
                    {{
                        `: "${order.data.coupon.code}" ${$t("applied")} (${order.data.coupon.value}%)`
                    }}
                    <small
                        v-if="order.data.coupon.deleted_at != null"
                        class="text-red-500"
                    >
                        ({{ $t("Deleted") }})
                    </small>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <i class="ri-map-pin-line text-2xl text-slate-400"></i>
                <p dir="auto" v-if="order.data.locality">
                    {{
                        `${order.data.locality.name} / ${order.data.city.name} / ${order.data.state.name} : (${order.data.shipping_cost} ${$t("currency")})`
                    }}
                </p>
                <p dir="auto" v-else>
                    {{
                        `${order.data.state.name} : (${order.data.shipping_cost} ${$t("currency")})`
                    }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <i class="ri-map-2-line text-2xl text-slate-400"></i>
                <p dir="auto">
                    {{ order.data.address }}
                </p>
            </div>
            <div v-if="order.data.shipper" class="flex items-center gap-2">
                <i class="ri-truck-line text-2xl text-slate-400"></i>

                <p>
                    {{ order.data.shipper.name }}
                    <small
                        v-if="order.data.shipper.deleted_at != null"
                        class="text-red-500"
                    >
                        ({{ $t("Deleted") }})
                    </small>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <p class="flex gap-2 items-center justify-center">
                    <i class="ri-truck-line text-2xl text-slate-400"></i>
                    <span>{{ $t("Delivery_date") }}</span>
                </p>
                <p class="">{{ order.data.delivery_date }}</p>
            </div>
            <div class="flex items-center gap-3">
                <p class="flex gap-2 items-center justify-center">
                    <i class="ri-calendar-2-line text-2xl text-slate-400"></i>
                    <span>{{ $t("Placed_at") }}</span>
                </p>
                <p class="">{{ order.data.created_at }}</p>
            </div>

            <div v-if="order.data.note" dir="auto">
                <div class="flex items-center gap-2">
                    <i
                        class="ri-sticky-note-add-line text-2xl text-slate-400"
                    ></i>
                    <p>{{ $t("Client Note") }} :</p>
                </div>
                {{ order.data.note }}
            </div>
        </div>
    </div>

    <div
        class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
    >
        <div class="flex justify-between items-center gap-3 flex-wrap">
            <h2
                class="mb-2 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
            >
                {{ $t("Products") }}
            </h2>
            <Link
                v-if="
                    order.data.status == 'pending' ||
                    order.data.status == 'abandoned'
                "
                class="inline-flex items-center gap-2 rounded-xl bg-slate-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-800"
                :href="route('orders.editProducts', order.data)"
            >
                {{ $t("Edit") }}
            </Link>
        </div>
        <div
            class="grid grid-cols-1 gap-3 border-b border-slate-200 py-5 last:border-b-0 dark:border-neutral-700"
            v-for="(product, index) in order.data.products"
            :key="index"
        >
            <div
                class="flex items-center flex-col min-[600px]:flex-row gap-3 min-[600px]:gap-3 w-full max-[600px]:justify-center max-[600px]:mb-4"
            >
                <div class="img-box w-70px min-[600px]:w-[140px]">
                    <img
                        :src="product.main_image_url"
                        :alt="product.name"
                        class="xl:w-[140px] rounded-xl object-cover"
                    />
                </div>
                <div
                    class="pro-data w-full px-3 min-[600px]:flex-1 max-[600px]:text-center"
                >
                    <div>
                        <component
                            :is="product.deleted_at ? 'h2' : 'Link'"
                            class="text-lg font-semibold leading-snug sm:text-xl"
                            :class="{
                                'text-sky-700 hover:text-sky-800 hover:underline dark:text-sky-400 dark:hover:text-sky-300':
                                    !product.deleted_at,
                                'text-neutral-500 dark:text-neutral-400':
                                    product.deleted_at,
                            }"
                            :href="route('products.show', product)"
                            v-text="product.name"
                        />

                        <p
                            class="mt-1 text-base font-medium text-slate-700 dark:text-neutral-200 sm:text-lg"
                        >
                            {{ `${product.pivot.price} ${$t("currency")}` }}

                            <span class="text-slate-500 dark:text-neutral-400">
                                x{{ product.pivot.quantity }}</span
                            >
                        </p>
                    </div>
                </div>
                <div class="min-[600px]:justify-self-end">
                    <p
                        class="w-full text-2xl font-bold leading-9 text-slate-800 dark:text-neutral-100 min-[600px]:text-end"
                    >
                        {{ `${product.pivot.sub_total} ${$t("currency")}` }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div
        class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
    >
        <h2
            class="mb-4 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
        >
            {{ $t("Latest client orders") }}
        </h2>

        <div
            v-if="order.data.client.recent_orders?.length"
            class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-base text-slate-700 dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-200"
        >
            <ul class="space-y-3">
                <li
                    v-for="clientOrder in order.data.client.recent_orders"
                    :key="clientOrder.id"
                    class="flex flex-wrap items-center justify-between gap-3 rounded-xl border px-4 py-3 transition-colors"
                    :class="
                        clientOrder.deleted_at
                            ? 'border-red-200 bg-red-50 dark:border-red-900/60 dark:bg-red-950/30'
                            : 'border-slate-200 bg-white dark:border-neutral-700 dark:bg-neutral-800'
                    "
                >
                    <span class="flex flex-wrap items-center gap-2">
                        <strong>#{{ clientOrder.id }}</strong>
                        <span
                            class="flex items-center gap-1 text-sm text-slate-500 dark:text-neutral-400"
                        >
                            <i class="ri-calendar-2-line"></i>
                            {{ clientOrder.created_at }}
                        </span>
                        <span
                            class="flex items-center gap-1 text-sm text-slate-500 dark:text-neutral-400"
                        >
                            <i class="ri-truck-line"></i>
                            {{ clientOrder.delivery_date }}
                        </span>
                        <Status
                            :status="clientOrder.status"
                            class="px-2 py-0.5 text-xs"
                        />
                    </span>
                    <span
                        v-if="clientOrder.deleted_at"
                        class="ms-auto rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300"
                    >
                        {{ $t("Deleted") }}
                    </span>
                    <span
                        v-else-if="clientOrder.is_current"
                        class="ms-auto rounded-full bg-sky-100 px-2 py-0.5 text-xs font-semibold text-sky-800 dark:bg-sky-950/50 dark:text-sky-300"
                    >
                        {{ $t("Current order") }}
                    </span>
                    <Link
                        v-else
                        :href="clientOrder.url"
                        class="ms-auto font-medium text-sky-700 hover:text-sky-800 hover:underline dark:text-sky-400 dark:hover:text-sky-300"
                    >
                        {{ $t("View order") }}
                    </Link>
                </li>
            </ul>
        </div>
        <p v-else class="text-sm text-slate-500 dark:text-neutral-400">
            {{ $t("No other orders found") }}
        </p>
    </div>

    <div class="mt-12 grid lg:grid-cols-2 gap-4 lg:gap-6">
        <div
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
        >
            <h2
                class="mb-6 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
            >
                {{ $t("Order History") }}
            </h2>

            <OrderActivity
                v-for="(activity, index) in order.data.activities"
                :key="index"
                :activity="activity"
            >
            </OrderActivity>
        </div>

        <div
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
        >
            <div class="flex items-center flex-wrap gap-2 justify-between mb-6">
                <h2
                    class="text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
                >
                    {{ $t("Order Notes") }}
                </h2>

                <CreateNewModal
                    href="notes.newNote"
                    :route-params="{ id: order.data.id, type: 'order' }"
                    :label="$t('New Note')"
                />
            </div>

            <Notes :notes="order.data.notes" scrollable="true" />
        </div>
    </div>
</template>
