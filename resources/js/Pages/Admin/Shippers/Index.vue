<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
const props = defineProps(["shippers"]);

function deleteShipper(shipper) {
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
            router.delete(route("shippers.destroy", shipper), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Shipper.deleted"),
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
    <Head :title="$t('Shippers')" />

    <PageHeader :page-title="$t('Shippers')">
        <CreateNewModal
            :label="$t('Shipper.new')"
            href="shippers.create"
            :close-button="false"
        >
        </CreateNewModal>
    </PageHeader>

    <ResultsCounter :count="shippers.meta.total" icon="ri-truck-line" />

    <div v-if="shippers.data.length" class="mt-4 overflow-x-auto rounded-2xl">
            <table>
                <thead>
                    <tr>
                        <th scope="col" class="!text-center">{{ $t("ID") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Name") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Orders.count") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Created_at") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Actions") }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="shipper in shippers.data" :key="shipper.id">
                        <td class="!text-center whitespace-nowrap font-medium">{{ `#${shipper.id}` }}</td>
                        <td class="!text-center font-semibold">{{ shipper.name }}</td>
                        <td class="!text-center">
                            <span class="inline-flex items-center justify-center gap-2">
                                <i class="ri-shopping-cart-line text-slate-500 dark:text-neutral-400" aria-hidden="true"></i>
                                {{ shipper.orders_count }}
                            </span>
                        </td>
                        <td class="!text-center whitespace-nowrap">{{ shipper.created_at }}</td>
                        <td class="!text-center">
                            <div class="flex items-center justify-center gap-2">
                            <ModalLink
                                :close-button="false"
                                :href="route('shippers.edit', shipper)"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600"
                                :aria-label="$t('Edit')"
                                :title="$t('Edit')"
                            >
                                <i class="ri-edit-line"></i>
                            </ModalLink>
                            <button
                                @click="deleteShipper(shipper)"
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
            :links="shippers.meta.links"
            :previous="shippers.links.prev"
            :next="shippers.links.next"
            variant="admin"
        />
    </div>
    <NotFound v-else />
</template>
