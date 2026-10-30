<script setup>
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";

import NotFound from "@/js/Components/NotFound.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import Paginator from "@/js/Components/Paginator.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
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

    <ResultsCounter :count="codes.total" icon="ri-coupon-3-line" />

    <div v-if="codes.data.length" class="mt-4 overflow-x-auto rounded-2xl">
            <table>
                <thead>
                    <tr>
                        <th scope="col" class="!text-center">{{ $t("ID") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Code") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Discount") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Status") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Orders.count") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Actions") }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="code in codes.data" :key="code.id">
                        <td class="!text-center whitespace-nowrap font-medium">{{ `#${code.id}` }}</td>
                        <td class="!text-center font-semibold">{{ code.code }}</td>
                        <td class="!text-center">{{ `${code.value}%` }}</td>
                        <td class="!text-center">
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold capitalize text-white"
                                :class="{
                                    'bg-red-500': code.status == 'inactive',
                                    'bg-green-500': code.status == 'active',
                                }"
                                >{{ $t(code.status) }}</span
                            >
                        </td>
                        <td class="!text-center">{{ code.orders_count }}</td>
                        <td class="!text-center">
                            <div class="flex items-center justify-center gap-2">
                            <ModalLink
                                :close-button="false"
                                :href="route('coupons.edit', code)"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600"
                                :aria-label="$t('Edit')"
                                :title="$t('Edit')"
                            >
                                <i class="ri-edit-line"></i>
                            </ModalLink>
                            <button
                                @click="deleteCode(code)"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/15 dark:text-red-300 dark:hover:bg-red-500/25"
                                :aria-label="$t('Delete')"
                                :title="$t('Delete')"
                            >
                                <i class="ri-delete-bin-5-fill"></i>
                            </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        <Paginator
            class="mt-8"
            :links="codes.links"
            :previous="codes.prev_page_url"
            :next="codes.next_page_url"
            variant="admin"
        />
    </div>
    <NotFound message="No Coupon codes Found" v-else />
</template>
