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
        <div class="flex items-center gap-4">
            <ModalLink
                :close-button="false"
                :href="route('clients.edit', client)"
                class="bg-slate-700 text-white rounded-md px-2 py-1 hover:bg-opacity-90 flex gap-2"
            >
                <span>{{ $t("Edit") }}</span>
                <i class="ri-edit-line"></i>
            </ModalLink>
            <button
                @click="deleteClient(client)"
                class="bg-red-500 text-white rounded-md px-2 py-1 hover:bg-opacity-90 flex gap-2"
            >
                <span>{{ $t("Delete") }}</span>
                <i class="ri-delete-bin-5-fill"></i>
            </button>
        </div>
    </PageHeader>
    <div
        class="bg-white p-4 rounded-md shadow-1 md:grid md:grid-cols-[350px,_1fr] md:gap-4 lg:gap-8"
    >
        <div>
            <div>
                <img
                    :src="Avatar"
                    class="rounded-lg mx-auto md:w-full"
                    alt="default user placeholder image"
                />
            </div>
        </div>
        <div class="mt-4 md:mt-0 text-lg">
            <h2 class="text-3xl text-primary font-semibold mt-2 mb-4">
                {{ client.name }}
            </h2>

            <div class="flex text-primary items-center gap-3">
                <i
                    class="ri-customer-service-2-fill text-slate-400 text-2xl"
                ></i>
                <span dir="ltr"
                    >{{ client.phone }}
                    <span v-if="client.phone2">
                        / {{ client.phone2 }}</span
                    ></span
                >
            </div>

            <div class="mb-1 text-primary flex items-center gap-3">
                <i class="ri-map-pin-line text-2xl text-slate-400"></i>
                <span v-if="client.locality">
                    {{
                        `${client.locality.name} / ${client.locality.city.name} / ${client.state.name}`
                    }}
                </span>
                <span v-else>{{ client.state.name }} </span>
            </div>
            <div class="mb-1 text-primary flex items-center gap-3">
                <i class="ri-map-2-line text-2xl text-slate-400"></i>
                <span dir="auto"> {{ client.address }}</span>
            </div>
            <div class="flex items-center gap-3">
                <i class="ri-star-line text-2xl text-slate-400"></i>
                <p class="text-primary flex items-center gap-2">
                    {{ `${$t("Reviews")} : ${client.reviews_count}` }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <i class="ri-shopping-cart-line text-2xl text-slate-400"></i>
                <p class="text-primary flex items-center gap-2">
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
            <div class="flex items-center gap-3">
                <i class="ri-money-dollar-box-line text-2xl text-slate-400"></i>
                <p class="text-primary flex items-center gap-2">
                    {{ `${$t("Total Spending")} : ${spent} ${$t("currency")}` }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <i class="ri-calendar-line text-2xl text-slate-400"></i>
                <p class="text-primary">
                    {{ $t("Created_at") }} - {{ client.created_at }}
                </p>
            </div>
        </div>
    </div>

    <section>
        <h2 class="text-2xl text-primary my-6 font-semibold">
            {{ $t("Orders") }}
        </h2>

        <div v-if="orders.data.length">
            <div
                class="grid grid-cols-[repeat(auto-fit,_minmax(min(350px,_100%),_1fr))] gap-4"
            >
                <div
                    class="bg-white rounded-md shadow-1 flex flex-col"
                    v-for="order in orders.data"
                    :key="order.id"
                >
                    <div class="flex justify-between gap-4 items-center p-3">
                        <div class="flex gap-2 items-center">
                            <component
                                :is="order.deleted_at == null ? 'Link' : 'p'"
                                :href="route('orders.show', order)"
                                class="text-xl underline font-bold"
                            >
                                #{{ order.id }}
                            </component>

                            <Status :status="order.status" class="px-2 py-1" />
                        </div>

                        <Link
                            v-if="order.deleted_at == null"
                            :href="route('orders.show', order)"
                            class="bg-slate-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                        >
                            <i class="ri-eye-line"></i>
                        </Link>
                        <p v-else class="text-red-500">
                            {{ $t("Deleted") }}
                        </p>
                    </div>

                    <div class="px-4 py-3 text-sky-700 flex-1">
                        <div class="flex gap-2 items-center">
                            <p class="text-red font-semibold text-2xl">
                                {{ `${order.amount} ${$t("currency")}` }}
                            </p>
                            <p v-if="order.coupon" class="text-neutral-600">
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
                                class="ri-truck-line text-2xl text-slate-400"
                            ></i>

                            <p class="text-primary">
                                {{ order.shipper.name }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 p-2 border-t-2 border-gray"
                    >
                        <div class="text-center">
                            <p class="flex gap-2 items-center justify-center">
                                <span>{{ $t("Delivery_date") }}</span>
                                <i
                                    class="ri-calendar-line text-2xl text-slate-400"
                                ></i>
                            </p>
                            <p class="text-primary">
                                {{ order.delivery_date }}
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="flex gap-2 items-center justify-center">
                                <span>{{ $t("Placed_at") }}</span>
                                <i
                                    class="ri-calendar-line text-2xl text-slate-400"
                                ></i>
                            </p>
                            <p class="text-primary">
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
                class="mt-4"
            />
        </div>

        <NotFound v-else message="NotFound.client_orders" />
    </section>

    <section>
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <h2 class="text-2xl text-primary my-6 font-semibold">
                {{ $t("Notes") }}
            </h2>

            <ModalLink
                :href="
                    route('notes.newNote', {
                        id: client.id,
                        type: 'client',
                    })
                "
                class="bg-green-500 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
                >{{ $t("New Note") }}</ModalLink
            >
        </div>

        <Notes :notes="notes.data" class="bg-white p-4 rounded-lg" />
    </section>
</template>
