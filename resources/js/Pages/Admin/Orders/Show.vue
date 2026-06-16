<script setup>
import Status from "@/js/Components/Status.vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { trans } from "laravel-vue-i18n";
import Swal from "sweetalert2";
import { router, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Notes from "@/js/Pages/Admin/Notes/Notes.vue";
import OrderActivity from "@/js/Pages/Admin/Orders/partials/OrderActivity.vue";
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
                class="bg-sky-800 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
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
                class="bg-slate-500 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
            >
                <i class="ri-edit-line"></i>
                {{ $t("Edit") }}
            </ModalLink>

            <button
                @click="deleteOrder(order.data)"
                class="bg-red-500 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
            >
                {{ $t("Delete") }}
            </button>
        </div>
    </PageHeader>

    <div class="flex items-center justify-center gap-3 mb-8">
        <button
            v-if="
                order.data.status == 'pending' ||
                order.data.status == 'abandoned'
            "
            @click="markAsNrp()"
            class="bg-sky-800 text-white px-3 py-2 rounded-md hover:bg-opacity-75 flex items-center gap-1"
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
            class="bg-green-600 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
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
            class="bg-yellow-600 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
        >
            {{ $t("Ship Order") }}
        </button>
        <button
            v-if="order.data.status == 'shipped'"
            @click="updateStatus('delivered')"
            class="bg-green-600 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
        >
            {{ $t("Deliver Order") }}
        </button>
        <button
            v-if="order.data.status == 'shipped'"
            @click="updateStatus('returned')"
            class="bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
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
            class="bg-red-600 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
        >
            {{ $t("Cancel Order") }}
        </button>
    </div>

    <div class="bg-white rounded-md shadow-3 p-4">
        <h2 class="text-neutral-600 text-2xl mb-2 font-semibold">
            {{ $t("Order Information") }}
        </h2>

        <div class="text-sky-700">
            <Status :status="order.data.status" class="px-2 py-1 w-max mb-2" />
            <div class="flex gap-2 items-center">
                <i class="ri-user-line text-2xl text-slate-400"></i>
                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        v-if="order.data.client.deleted_at == null"
                        :href="route('clients.show', order.data.client)"
                        class="text-lg font-medium block hover:text-slate-500 hover:underline"
                    >
                        {{ order.data.client_name }}
                    </Link>
                    <p v-else class="text-lg font-medium block">
                        {{ order.data.client_name }}
                        <small class="text-red-500">
                            ({{ $t("Deleted") }})
                        </small>
                    </p>
                    <span class="text-sm text-slate-500">
                        ({{ order.data.client.orders_count ?? 0 }}
                        {{ $t("orders") }})
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
                <p class="text-red font-semibold text-2xl">
                    {{ `${order.data.amount} ${$t("currency")}` }}
                </p>
                <p v-if="order.data.coupon" class="text-neutral-600">
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

    <div class="bg-white rounded-md shadow-3 p-4 mt-12">
        <div class="flex justify-between items-center gap-3 flex-wrap">
            <h2 class="text-neutral-600 text-2xl mb-2 font-semibold">
                {{ $t("Products") }}
            </h2>
            <Link
                v-if="
                    order.data.status == 'pending' ||
                    order.data.status == 'abandoned'
                "
                class="bg-slate-500 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
                :href="route('orders.editProducts', order.data)"
            >
                {{ $t("Edit") }}
            </Link>
        </div>
        <div
            class="grid grid-cols-1 :gap-3 border-b border-neutral-200 py-6"
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
                            class="font-semibold text-xl min-[550px]-leading-8"
                            :class="{
                                'hover:underline text-primary hover:text-sky-600':
                                    !product.deleted_at,
                                'text-neutral-500': product.deleted_at,
                            }"
                            :href="route('products.show', product)"
                            v-text="product.name"
                        />

                        <p
                            class="font-medium text-lg min-[550px]-leading-8 text-sky-700"
                        >
                            {{ `${product.pivot.price} ${$t("currency")}` }}

                            <span class="text-primary">
                                x{{ product.pivot.quantity }}</span
                            >
                        </p>
                    </div>
                </div>
                <div class="min-[600px]:justify-self-end">
                    <p
                        class="text-sky-800 min-[600px]:text-end font-bold text-2xl leading-9 w-full"
                    >
                        {{ `${product.pivot.sub_total} ${$t("currency")}` }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-md shadow-3 p-4 mt-12">
        <h2 class="text-neutral-600 text-2xl mb-4 font-semibold">
            {{ $t("Latest client orders") }}
        </h2>

        <div
            v-if="order.data.client.recent_orders?.length"
            class="rounded-md border border-slate-200 bg-slate-50 p-4 text-lg text-slate-700"
        >
            <ul class="space-y-3">
                <li
                    v-for="clientOrder in order.data.client.recent_orders"
                    :key="clientOrder.id"
                    class="flex flex-wrap items-center justify-between gap-3 rounded border border-slate-200 bg-white px-4 py-3"
                >
                    <span class="flex flex-wrap items-center gap-2">
                        <strong>#{{ clientOrder.id }}</strong>
                        <span class="flex items-center gap-1 text-slate-500">
                            <i class="ri-calendar-2-line"></i>
                            {{ clientOrder.created_at }}
                        </span>
                        <span class="flex items-center gap-1 text-slate-500">
                            <i class="ri-truck-line"></i>
                            {{ clientOrder.delivery_date }}
                        </span>
                        <Status
                            :status="clientOrder.status"
                            class="px-2 py-0.5 text-xs"
                        />
                    </span>
                    <Link
                        :href="clientOrder.url"
                        class="text-sky-700 hover:text-sky-900 hover:underline"
                    >
                        {{ $t("View order") }}
                    </Link>
                </li>
            </ul>
        </div>
        <p v-else class="text-slate-500">{{ $t("No other orders found") }}</p>
    </div>

    <div class="mt-12 grid lg:grid-cols-2 gap-4 lg:gap-6">
        <div class="bg-white rounded-md p-4 shadow-3">
            <h2 class="text-neutral-600 text-2xl mb-6 font-semibold">
                {{ $t("Order History") }}
            </h2>

            <OrderActivity
                v-for="(activity, index) in order.data.activities"
                :key="index"
                :activity="activity"
            >
            </OrderActivity>
        </div>

        <div class="bg-white rounded-md p-4 shadow-3">
            <div class="flex items-center flex-wrap gap-2 justify-between mb-6">
                <h2 class="text-neutral-600 text-2xl font-semibold">
                    {{ $t("Order Notes") }}
                </h2>

                <ModalLink
                    :href="
                        route('notes.newNote', {
                            id: order.data.id,
                            type: 'order',
                        })
                    "
                    class="bg-green-500 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
                    >{{ $t("New Note") }}</ModalLink
                >
            </div>

            <Notes :notes="order.data.notes" scrollable="true" />
        </div>
    </div>
</template>
