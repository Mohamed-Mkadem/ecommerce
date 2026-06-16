<script setup>
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";

import NotFound from "@/js/Components/NotFound.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import Paginator from "@/js/Components/Paginator.vue";
const props = defineProps(["codes"]);
function deleteCode(code) {
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
            router.delete(route("coupons.destroy", code), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Code.deleted"),
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
    <Head :title="$t('Coupon Codes')" />
    <PageHeader :page-title="$t('Coupon Codes')">
        <CreateNewModal
            :close-button="false"
            href="coupons.create"
            :label="$t('New Coupon Code')"
        />
    </PageHeader>

    <div v-if="codes.data.length">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-left rtl:text-right text-slate-800">
                <thead class="text-gray bg-graydark p-4">
                    <tr
                        class="grid grid-cols-[100px_1fr_150px_150px_200px_200px] items-center text-center"
                    >
                        <th scope="col" class="px-6 py-4">
                            {{ $t("ID") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Code") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Discount") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Status") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Orders.count") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(code, index) in codes.data"
                        :key="index"
                        class="odd:bg-slate-200 even:bg-white grid grid-cols-[100px_1fr_150px_150px_200px_200px] items-center text-center"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-primary whitespace-nowrap"
                        >
                            {{ `#${code.id}` }}
                        </th>
                        <td class="px-6 py-4">{{ code.code }}</td>
                        <td class="px-6 py-4">{{ `${code.value}%` }}</td>
                        <td class="px-6 py-4">
                            <span
                                class="text-white rounded py-1 px-4 capitalize"
                                :class="{
                                    'bg-red-500': code.status == 'inactive',
                                    'bg-green-500': code.status == 'active',
                                }"
                                >{{ $t(code.status) }}</span
                            >
                        </td>
                        <td class="px-6 py-4">{{ code.orders_count }}</td>
                        <td
                            class="px-6 py-4 flex items-center gap-3 justify-center"
                        >
                            <ModalLink
                                :close-button="false"
                                :href="route('coupons.edit', code)"
                                class="bg-slate-700 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                            >
                                <i class="ri-edit-line"></i>
                            </ModalLink>
                            <button
                                @click="deleteCode(code)"
                                class="bg-red-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                            >
                                <i class="ri-delete-bin-5-fill"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Paginator
            class="mt-8"
            :links="codes.links"
            :previous="codes.prev_page_url"
            :next="codes.next_page_url"
        />
    </div>
    <NotFound message="No Coupon codes Found" v-else />
</template>
