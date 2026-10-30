<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { router } from "@inertiajs/vue3";
import Paginator from "@/js/Components/Paginator.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import Avatar from "@/assets/images/user-default.webp";
import Status from "@/js/Components/Status.vue";
import Notes from "@/js/Pages/Admin/Notes/Notes.vue";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
const props = defineProps([
    "client",
    "orders",
    "spent",
    "notes",
    "deleted_orders_count",
]);

function deleteClient(client) {
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
            router.delete(route("clients.destroy", client), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Client.deleted"),
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
    <Head :title="client.name" />
    <PageHeader :pageTitle="client.name">
        <div class="flex flex-wrap items-center gap-3">
            <ModalLink
                :close-button="false"
                :href="route('clients.edit', client)"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:bg-neutral-700"
            >
                <i class="ri-edit-line"></i>
                <span>{{ $t("Edit") }}</span>
            </ModalLink>
            <button
                @click="deleteClient(client)"
                class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-100 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300 dark:hover:bg-red-500/25"
            >
                <span>{{ $t("Delete") }}</span>
                <i class="ri-delete-bin-5-fill"></i>
            </button>
        </div>
    </PageHeader>
    <div
        class="grid gap-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6 md:grid-cols-[minmax(220px,300px)_minmax(0,1fr)] lg:gap-8"
    >
        <div>
            <div>
                <img
                    :src="Avatar"
                    class="mx-auto aspect-square w-full max-w-[300px] rounded-2xl bg-slate-100 object-cover dark:bg-neutral-700 md:max-w-none"
                    alt="default user placeholder image"
                />
            </div>
        </div>
        <div
            class="min-w-0 text-base text-slate-700 dark:text-neutral-200 md:text-lg"
        >
            <h2
                class="mb-4 mt-1 text-2xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100 sm:text-3xl"
            >
                {{ client.name }}
            </h2>

            <div
                class="flex items-center gap-3 py-1 text-slate-700 dark:text-neutral-200"
            >
                <i
                    class="ri-customer-service-2-fill text-2xl text-neutral-500 dark:text-neutral-400"
                ></i>
                <span dir="ltr"
                    >{{ client.phone }}
                    <span v-if="client.phone2">
                        / {{ client.phone2 }}</span
                    ></span
                >
            </div>

            <div
                class="mb-1 flex items-center gap-3 py-1 text-slate-700 dark:text-neutral-200"
            >
                <i
                    class="ri-map-pin-line text-2xl text-neutral-500 dark:text-neutral-400"
                ></i>
                <span v-if="client.locality">
                    {{
                        `${client.locality.name} / ${client.locality.city.name} / ${client.state.name}`
                    }}
                </span>
                <span v-else>{{ client.state.name }} </span>
            </div>
            <div
                class="mb-1 flex items-center gap-3 py-1 text-slate-700 dark:text-neutral-200"
            >
                <i
                    class="ri-map-2-line text-2xl text-neutral-500 dark:text-neutral-400"
                ></i>
                <span dir="auto"> {{ client.address }}</span>
            </div>

            <div class="flex items-center gap-3 py-1">
                <i
                    class="ri-shopping-cart-line text-2xl text-slate-500 dark:text-neutral-400"
                ></i>
                <p
                    class="flex flex-wrap items-center gap-2 text-slate-700 dark:text-neutral-200"
                >
                    <span>
                        {{ `${$t("Orders")} : ${client.orders_count}` }}
                    </span>
                    <span v-if="deleted_orders_count">
                        {{
                            `(${deleted_orders_count} ${$t("client.deleted_orders")})`
                        }}
                    </span>
                </p>
            </div>
            <div class="flex items-center gap-3 py-1">
                <i
                    class="ri-money-dollar-box-line text-2xl text-slate-500 dark:text-neutral-400"
                ></i>
                <p
                    class="flex items-center gap-2 text-slate-700 dark:text-neutral-200"
                >
                    {{ `${$t("Total Spending")} : ${spent} ${$t("currency")}` }}
                </p>
            </div>
            <div class="flex items-center gap-3 py-1">
                <i
                    class="ri-truck-line text-2xl text-neutral-500 dark:text-neutral-400"
                ></i>
                <p
                    class="flex flex-wrap items-center gap-2 text-slate-700 dark:text-neutral-200"
                >
                    {{ $t("Delivery Rate") }} :
                    <span
                        v-if="
                            client.delivery_rate !== null &&
                            client.delivery_rate !== undefined
                        "
                        :title="$t('client.delivery_rate_tooltip')"
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-sm font-semibold"
                        :class="{
                            'bg-green-100 text-green-800':
                                client.delivery_rate >= 70,
                            'bg-yellow-100 text-yellow-800':
                                client.delivery_rate >= 40 &&
                                client.delivery_rate < 70,
                            'bg-red-100 text-red-800':
                                client.delivery_rate < 40,
                        }"
                    >
                        {{ client.delivery_rate }}%
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-sm font-semibold text-slate-500 dark:bg-neutral-700 dark:text-neutral-300"
                        :title="$t('client.delivery_rate_tooltip')"
                    >
                        N/A
                    </span>
                </p>
            </div>
            <div class="flex items-center gap-3 py-1">
                <i
                    class="ri-calendar-line text-2xl text-neutral-500 dark:text-neutral-400"
                ></i>
                <p class="text-slate-700 dark:text-neutral-200">
                    {{ $t("Created_at") }} - {{ client.created_at }}
                </p>
            </div>
        </div>
    </div>

    <section>
        <h2
            class="my-5 text-xl font-semibold text-slate-800 dark:text-neutral-100"
        >
            {{ $t("Orders") }}
        </h2>

        <div v-if="orders.data.length">
            <div
                class="grid grid-cols-[repeat(auto-fit,_minmax(min(350px,_100%),_1fr))] gap-4"
            >
                <div
                    class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
                    v-for="order in orders.data"
                    :key="order.id"
                >
                    <div
                        class="flex items-center justify-between gap-4 border-b border-slate-100 p-4 dark:border-neutral-700"
                    >
                        <div class="flex gap-2 items-center">
                            <component
                                :is="order.deleted_at == null ? 'Link' : 'p'"
                                :href="route('orders.show', order)"
                                class="text-lg font-semibold text-slate-800 underline decoration-slate-300 underline-offset-4 transition hover:text-teal-700 dark:text-neutral-100 dark:decoration-neutral-600 dark:hover:text-teal-300"
                            >
                                #{{ order.id }}
                            </component>

                            <Status :status="order.status" class="px-2 py-1" />
                        </div>

                        <Link
                            v-if="order.deleted_at == null"
                            :href="route('orders.show', order)"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600"
                        >
                            <i class="ri-eye-line"></i>
                        </Link>
                        <p v-else class="text-red-500">
                            {{ $t("Deleted") }}
                        </p>
                    </div>

                    <div
                        class="flex-1 px-4 py-4 text-slate-600 dark:text-neutral-300"
                    >
                        <div class="flex gap-2 items-center">
                            <p
                                class="text-xl font-semibold text-slate-800 dark:text-neutral-100"
                            >
                                {{ `${order.amount} ${$t("currency")}` }}
                            </p>
                            <p
                                v-if="order.coupon"
                                class="text-sm text-slate-500 dark:text-neutral-400"
                            >
                                {{
                                    `: "${order.coupon.code}" ${$t("applied")} (${order.coupon.value}%)`
                                }}
                            </p>
                        </div>

                        <p dir="auto">{{ order.state.name }}</p>
                        <div
                            v-if="order.shipper"
                            class="flex items-center gap-2"
                        >
                            <i
                                class="ri-truck-line text-2xl text-neutral-500 dark:text-neutral-400"
                            ></i>

                            <p class="text-slate-700 dark:text-neutral-200">
                                {{ order.shipper.name }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-3 border-t border-slate-100 bg-slate-50/70 px-4 py-3 dark:border-neutral-700 dark:bg-neutral-900/30 sm:grid-cols-2"
                    >
                        <div>
                            <p
                                class="flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-neutral-400"
                            >
                                <i
                                    class="ri-calendar-event-line"
                                    aria-hidden="true"
                                ></i>
                                {{ $t("Delivery_date") }}
                            </p>
                            <p
                                class="mt-1 text-sm font-semibold text-slate-700 dark:text-neutral-200"
                            >
                                {{ order.delivery_date }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-neutral-400"
                            >
                                <i
                                    class="ri-calendar-check-line"
                                    aria-hidden="true"
                                ></i>
                                {{ $t("Placed_at") }}
                            </p>
                            <p
                                class="mt-1 text-sm font-semibold text-slate-700 dark:text-neutral-200"
                            >
                                {{ order.created_at }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <Paginator
                :links="orders.meta.links"
                :previous="orders.links.prev"
                :next="orders.links.next"
                variant="admin"
                class="mt-4"
            />
        </div>

        <NotFound v-else message="NotFound.client_orders" />
    </section>

    <section>
        <div class="my-5 flex flex-wrap items-center justify-between gap-4">
            <h2
                class="text-xl font-semibold text-slate-800 dark:text-neutral-100"
            >
                {{ $t("Notes") }}
            </h2>

            <CreateNewModal
                href="notes.newNote"
                :route-params="{ id: client.id, type: 'client' }"
                :label="$t('New Note')"
            />
        </div>

        <div
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6"
        >
            <Notes :notes="notes.data" />
        </div>
    </section>
</template>
