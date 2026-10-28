use App\Models\Shipper;
<script setup>
import Status from "@/js/Components/Status.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import { trans } from "laravel-vue-i18n";
import Swal from "sweetalert2";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    orders: {
        type: Object,
    },
    filters: {
        type: Object,
    },
    states: { type: Object },
    shippers: { type: Object },
    couponCodes: { type: Object },
});
const initialFormValues = {
    search: null,
    clientPhone: null,
    clientName: null,
    minAmount: null,
    maxAmount: null,

    minDeliveryDate: null,
    maxDeliveryDate: null,

    minCreationDate: null,
    maxCreationDate: null,

    minOrdersCount: null,
    maxOrdersCount: null,
    statuses: [],
    sort: null,
    shipper: null,
    state: null,
    couponCode: null,
    discounted: [],
    free_shipping: [],
    minDeliveryRate: null,
    maxDeliveryRate: null,
};
const formIsResetting = ref(false);
const form = useForm({ ...initialFormValues });
function submitForm() {
    form.get(route("orders.index"), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
function resetForm() {
    formIsResetting.value = true;

    Object.assign(form, initialFormValues);

    submitForm();

    clearFlatpickrInputs();

    formIsResetting.value = false;
}
function clearFlatpickrInputs() {
    const flatpickrInputs = document.querySelectorAll(".flatpickr-input");

    flatpickrInputs.forEach((input) => {
        if (input._flatpickr) {
            input._flatpickr.clear();
        }
    });
}
onMounted(() => {
    Object.assign(form, props.filters);
});

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
</script>

<template>

    <Head :title="$t('Orders')" />
    <PageHeader :page-title="$t('Orders')"> </PageHeader>

    <!-- Filters Start -->
    <div
        class="mb-8 rounded-2xl border border-slate-200/80 bg-white px-5 py-6 shadow-[0_8px_30px_rgba(15,23,42,0.06)] sm:px-6 sm:py-7 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/30">
        <h2 class="mb-5 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
            {{ $t("Filter.title") }}
        </h2>

        <form @submit.prevent="submitForm" @reset.prevent="resetForm" class="text-slate-700 dark:text-neutral-200">
            <div class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-center">
                <div class="w-full">
                    <InputLabel for="search" :value="$t('Filter.search_by_id')" />

                    <TextInput
                        variant="admin"
                        id="search" type="text" v-model="form.search" :required="false"
                        :placeholder="$t('Filter.search_by_id_placeholder')" />
                </div>
                <div class="w-full">
                    <InputLabel for="sort" :value="$t('Filter.sort')" />

                    <SelectInput variant="admin" v-model="form.sort" id="sort">
                        <option :value="null">
                            {{ $t("Filter.sort") }}
                        </option>
                        <option value="highest_amount">
                            {{ $t("Sort.highest_amount") }}
                        </option>
                        <option value="lowest_amount">
                            {{ $t("Sort.lowest_amount") }}
                        </option>
                        <option value="newest_delivery_date">
                            {{ $t("Sort.newest_delivery_date") }}
                        </option>
                        <option value="oldest_delivery_date">
                            {{ $t("Sort.oldest_delivery_date") }}
                        </option>
                        <option value="newest_creation_date">
                            {{ $t("Sort.newest_creation_date") }}
                        </option>
                        <option value="oldest_creation_date">
                            {{ $t("Sort.oldest_creation_date") }}
                        </option>
                        <option value="highest_delivery_rate">
                            {{ $t("Sort.highest_delivery_rate") }}
                        </option>
                        <option value="lowest_delivery_rate">
                            {{ $t("Sort.lowest_delivery_rate") }}
                        </option>
                    </SelectInput>
                </div>
            </div>

            <div class="flex gap-4 mt-4 flex-col sm:flex-row sm:justify-between sm:items-center">
                <div class="w-full">
                    <InputLabel for="clientName" :value="$t('Filter.search_by_client_name')" />

                    <TextInput
                        variant="admin"
                        id="clientName" type="text" v-model="form.clientName" :required="false" :placeholder="$t('Filter.search_by_client_name_placeholder')
                            " />
                </div>
                <div class="w-full">
                    <InputLabel for="clientPhone" :value="$t('Filter.search_by_client_phone')" />

                    <TextInput
                        variant="admin"
                        id="clientPhone" type="text" v-model="form.clientPhone" :required="false" :placeholder="$t('Filter.search_by_client_phone_placeholder')
                            " />
                </div>
            </div>
            <div class="flex gap-4 mt-4 flex-col sm:flex-row sm:justify-between sm:items-center">
                <div class="w-full">
                    <InputLabel for="state" :value="$t('State')" />

                    <SelectInput variant="admin" v-model="form.state" id="state">
                        <option :value="null">{{ $t("All") }}</option>
                        <option :value="state.id" v-for="state in props.states" :key="state.id">
                            {{ state.name }}
                        </option>
                    </SelectInput>
                </div>
                <div class="w-full">
                    <InputLabel for="shipper" :value="$t('Shipper')" />

                    <SelectInput variant="admin" v-model="form.shipper" id="shipper">
                        <option :value="null">{{ $t("All") }}</option>
                        <option :value="shipper.id" v-for="shipper in props.shippers" :key="shipper.id">
                            {{ shipper.name }}
                        </option>
                    </SelectInput>
                </div>
            </div>

            <div class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-center mt-4">
                <div class="w-full">
                    <InputLabel :value="$t('Status')" />
                    <div class="grid grid-cols-[repeat(auto-fit,_minmax(min(150px,_100%),_1fr))] mt-1 gap-2">
                        <div
                            class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="status-pending" v-model="form.statuses" value="pending" />
                                <label for="status-pending" class="cursor-pointer select-none">{{ $t("pending")
                                    }}</label>
                            </div>
                        </div>

                        <div
                            class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="status-abandoned" v-model="form.statuses"
                                    value="abandoned" />
                                <label for="status-abandoned" class="cursor-pointer select-none">{{ $t("abandoned")
                                    }}</label>
                            </div>
                        </div>

                        <div
                            class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="status-confirmed" v-model="form.statuses"
                                    value="confirmed" />
                                <label for="status-confirmed" class="cursor-pointer select-none">{{ $t("confirmed")
                                    }}</label>
                            </div>
                        </div>

                        <div
                            class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="status-canceled" v-model="form.statuses" value="canceled" />
                                <label for="status-canceled" class="cursor-pointer select-none">{{ $t("canceled")
                                    }}</label>
                            </div>
                        </div>

                        <div
                            class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="status-shipped" v-model="form.statuses" value="shipped" />
                                <label for="status-shipped" class="cursor-pointer select-none">{{ $t("shipped")
                                    }}</label>
                            </div>
                        </div>

                        <div
                            class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="status-delivered" v-model="form.statuses"
                                    value="delivered" />
                                <label for="status-delivered" class="cursor-pointer select-none">{{ $t("delivered")
                                    }}</label>
                            </div>
                        </div>
                        <div
                            class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="status-returned" v-model="form.statuses" value="returned" />
                                <label for="status-returned" class="cursor-pointer select-none">{{ $t("returned")
                                    }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4 mt-4 lg:flex-row">
                <div class="flex flex-col gap-4 sm:flex-row w-full">
                    <div class="mt-1 w-full">
                        <InputLabel for="min_amount" :value="$t('Minimum Amount')" />
                        <TextInput variant="admin" v-model="form.minAmount" id="min_amount" type="number"
                            :placeholder="$t('in DT')" step="0.01" />
                    </div>
                    <div class="mt-1 w-full">
                        <InputLabel for="max_amount" :value="$t('Maximum Amount')" />
                        <TextInput variant="admin" v-model="form.maxAmount" id="max_amount" type="number"
                            :placeholder="$t('in DT')" step="0.01" />
                    </div>
                </div>

                <div class="flex flex-col gap-4 sm:flex-row w-full">
                    <div class="w-full mt-1 gap-2">
                        <InputLabel for="" :value="$t('Discounted')" />
                        <div class="grid grid-cols-[repeat(auto-fit,_minmax(min(70px,_100%),_1fr))] gap-1 mt-1">
                            <div
                                class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                                <div class="flex w-full items-center gap-2">
                                    <input type="checkbox" id="status-true" v-model="form.discounted" :value="true" />
                                    <label for="status-true" class="cursor-pointer">{{ $t("Yes") }}</label>
                                </div>
                            </div>
                            <div
                                class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                                <div class="flex w-full items-center gap-2">
                                    <input type="checkbox" id="status-false" v-model="form.discounted" :value="false" />
                                    <label for="status-false" class="cursor-pointer">{{ $t("No") }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full mt-1 gap-2">
                        <InputLabel for="couponCode_id" :value="$t('Coupon Code')" />
                        <SelectInput variant="admin" v-model="form.couponCode" id="couponCode_id">
                            <option :value="null">{{ $t("All") }}</option>
                            <option :value="couponCode.id" v-for="couponCode in props.couponCodes" :key="couponCode.id">
                                {{
                                    `${couponCode.code} (${couponCode.value}%)`
                                }}
                            </option>
                        </SelectInput>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4 mt-4 sm:flex-row">
                <div class="w-full">
                    <InputLabel for="min_delivery_date" :value="`${$t('From')} ${$t('Delivery_date')} `" />

                    <DatePicker variant="admin" id="min_delivery_date" v-model="form.minDeliveryDate" :disabled="false" />
                </div>
                <div class="w-full">
                    <InputLabel for="max_delivery_date" :value="`${$t('To')} ${$t('Delivery_date')} `" />

                    <DatePicker variant="admin" id="max_delivery_date" v-model="form.maxDeliveryDate" :disabled="false" />
                </div>
            </div>
            <div class="flex flex-col gap-4 mt-4 sm:flex-row">
                <div class="w-full">
                    <InputLabel for="min_creationDate" :value="`${$t('From')} ${$t('Creation_date')}`" />

                    <DatePicker variant="admin" id="min_creationDate" v-model="form.minCreationDate" :disabled="false" />
                </div>
                <div class="w-full">
                    <InputLabel for="max_creationDate" :value="`${$t('To')} ${$t('Creation_date')}`" />

                    <DatePicker variant="admin" id="max_creationDate" v-model="form.maxCreationDate" :disabled="false" />
                </div>
            </div>
            <div class="flex flex-col gap-4 mt-4 sm:flex-row">
                <div class="w-full">
                    <InputLabel for="min_delivery_rate" :value="$t('Min Client Delivery Rate')" />

                    <TextInput variant="admin" v-model="form.minDeliveryRate" id="min_delivery_rate" type="number"
                        placeholder="e.g., 0" min="0" max="100" step="0.01" />
                </div>
                <div class="w-full">
                    <InputLabel for="max_delivery_rate" :value="$t('Max Client Delivery Rate')" />

                    <TextInput variant="admin" v-model="form.maxDeliveryRate" id="max_delivery_rate" type="number"
                        placeholder="e.g., 100" min="0" max="100" step="0.01" />
                </div>
            </div>
            <div class="gap-4 sm:flex-row mt-4 w-full md:w-1/2">
                <div class="w-full mt-1 gap-2">
                    <InputLabel for="" :value="$t('Free Shipping')" />
                    <div class="grid grid-cols-[repeat(auto-fit,_minmax(min(70px,_100%),_1fr))] gap-1 mt-1">
                        <div
                            class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                            <div class="flex w-full items-center gap-2">
                                <input type="checkbox" id="free-shippping-true" v-model="form.free_shipping"
                                    :value="true" />
                                <label for="free-shippping-true" class="cursor-pointer">{{ $t("Yes") }}</label>
                            </div>
                        </div>
                        <div
                            class="flex h-[42px] w-full items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-700 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200">
                            <div class="flex w-full items-center gap-2">
                                <input type="checkbox" id="free-shippping-false" v-model="form.free_shipping"
                                    :value="false" />
                                <label for="free-shippping-false" class="cursor-pointer">{{ $t("No") }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <input :disabled="formIsResetting || form.processing" :class="{
                    'opacity-25 !cursor-not-allowed':
                        formIsResetting || form.processing,
                }" type="Submit" :value="$t('Filter.value')"
                    class="w-full cursor-pointer rounded-xl border border-primary bg-primary px-5 py-2.5 font-medium text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-opacity-90 hover:shadow-md sm:w-1/4" />
                <input :disabled="formIsResetting || form.processing" :class="{
                    'opacity-75 !cursor-not-allowed':
                        formIsResetting || form.processing,
                }" type="reset" :value="$t('form.reset')"
                    class="w-full cursor-pointer rounded-xl border border-neutral-400 bg-neutral-500 px-5 py-2.5 font-medium text-white shadow-sm transition duration-200 hover:bg-neutral-600 hover:shadow-md sm:w-1/4 dark:border-neutral-600 dark:bg-neutral-700 dark:hover:bg-neutral-600" />
            </div>
        </form>
    </div>
    <!-- Filters End -->

    <h2 class="my-6 text-xl font-semibold text-primary dark:text-neutral-100">
        {{ $t("Results") }} : {{ orders.meta.total }}
    </h2>

    <div v-if="orders.data.length">
        <div class="grid grid-cols-[repeat(auto-fit,_minmax(min(350px,_100%),_1fr))] gap-4">
            <div class="flex flex-col rounded-2xl border border-slate-200/80 bg-white shadow-sm transition duration-200 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/30"
                v-for="order in orders.data" :key="order.id">
                <div class="flex justify-between gap-4 items-center p-3">
                    <div class="flex gap-2 items-center">
                        <Link :href="route('orders.show', order)"
                            class="text-xl font-bold text-primary underline decoration-slate-300 underline-offset-4 dark:text-neutral-100 dark:decoration-neutral-600">
                        #{{ order.id }}
                        </Link>
                        <Status :status="order.status" class="px-2 py-1" />
                    </div>
                    <div class="flex items-center gap-2 border-b-2 border-gray dark:border-neutral-700">
                        <Link :href="route('orders.show', order)"
                            class="rounded-lg bg-slate-500 px-2.5 py-2 text-xl text-white transition hover:bg-slate-600">
                        <i class="ri-eye-line"></i>
                        </Link>

                        <ModalLink v-if="
                            order.status == 'pending' ||
                            order.status == 'abandoned'
                        " :close-button="false" :href="route('orders.edit', order)"
                            class="rounded-lg bg-slate-700 px-2.5 py-2 text-xl text-white transition hover:bg-slate-800">
                            <i class="ri-edit-line"></i>
                        </ModalLink>

                        <button @click="deleteOrder(order)"
                            class="rounded-lg bg-red-500 px-2.5 py-2 text-xl text-white transition hover:bg-red-600">
                            <i class="ri-delete-bin-5-fill"></i>
                        </button>
                    </div>
                </div>

                <div class="flex-1 px-4 py-3 text-sky-700 dark:text-neutral-300">
                    <Link v-if="order.client.deleted_at == null" :href="route('clients.show', order.client)"
                        class="mb-1 block text-xl font-semibold text-primary hover:text-slate-500 hover:underline dark:text-neutral-100 dark:hover:text-neutral-300">
                    <span>{{ order.client_name }}</span>
                    <span
                        class="ml-2 inline-flex items-center gap-1 text-lg font-normal text-slate-500 dark:text-neutral-400">
                        (<i class="ri-shopping-cart-line"></i>
                        {{ order.client.orders_count ?? 0 }})
                        <span v-if="
                            order.client.delivery_rate !== null &&
                            order.client.delivery_rate !== undefined
                        " v-tippy="$t('client.delivery_rate_tooltip')" :title="$t('client.delivery_rate_tooltip')"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                            :class="{
                                'bg-green-100 text-green-800': order.client.delivery_rate >= 70,
                                'bg-yellow-100 text-yellow-800': order.client.delivery_rate >= 40 && order.client.delivery_rate < 70,
                                'bg-red-100 text-red-800': order.client.delivery_rate < 40,
                            }">
                            <i class="ri-truck-line"></i>
                            {{ order.client.delivery_rate }}%

                        </span>
                    </span>
                    </Link>
                    <p v-else class="mb-1 block text-xl font-semibold text-primary dark:text-neutral-100">
                        {{ order.client_name }}
                        <small class="text-red-500 font-normal text-sm">
                            ({{ $t("Deleted") }})
                        </small>
                    </p>
                    <p class="mb-1 text-lg dark:text-neutral-300">
                        {{ order.phone }}
                        <span v-if="order.phone2"> / {{ order.phone2 }}</span>
                    </p>
                    <div class="flex gap-2 items-center">
                        <p class="text-red font-semibold text-2xl">
                            {{ `${order.amount} ${$t("currency")}` }}
                        </p>
                        <p v-if="order.coupon" class="text-neutral-600 dark:text-neutral-400">
                            {{
                                `: "${order.coupon.code}" ${$t("applied")} (${order.coupon.value}%)`
                            }}
                            <small v-if="order.coupon.deleted_at != null" class="text-red-500">
                                ({{ $t("Deleted") }})
                            </small>
                        </p>
                    </div>

                    <p dir="auto">{{ order.state.name }}</p>
                    <div v-if="order.shipper" class="flex items-center gap-2">
                        <i class="ri-truck-line text-2xl text-slate-400"></i>

                        <p class="text-primary dark:text-neutral-200">
                            {{ order.shipper.name }}
                            <span v-if="order.free_shipping">({{ $t("Free") }})</span>
                            <small v-if="order.shipper.deleted_at != null" class="text-red-500">
                                ({{ $t("Deleted") }})
                            </small>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 border-t-2 border-gray p-3 dark:border-neutral-700 sm:grid-cols-2">
                    <div class="text-center">
                        <p class="flex items-center justify-center gap-2 dark:text-neutral-300">
                            <span>{{ $t("Delivery_date") }}</span>
                            <i class="ri-calendar-line text-2xl text-slate-400"></i>
                        </p>
                        <p class="text-primary dark:text-neutral-100">{{ order.delivery_date }}</p>
                    </div>
                    <div class="text-center">
                        <p class="flex items-center justify-center gap-2 dark:text-neutral-300">
                            <span>{{ $t("Placed_at") }}</span>
                            <i class="ri-calendar-line text-2xl text-slate-400"></i>
                        </p>
                        <p class="text-primary dark:text-neutral-100">{{ order.created_at }}</p>
                    </div>
                </div>
            </div>
        </div>
        <Paginator :links="orders.meta.links" :previous="orders.links.prev" :next="orders.links.next" class="mt-4" />
    </div>
    <div v-else>
        <NotFound />
    </div>
</template>
