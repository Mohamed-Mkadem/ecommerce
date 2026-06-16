<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
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

    <div v-if="shippers.data.length">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-left rtl:text-right text-slate-800">
                <thead class="text-gray bg-graydark p-4">
                    <tr
                        class="grid grid-cols-[150px_1fr_200px_200px_200px] items-center text-center"
                    >
                        <th scope="col" class="px-6 py-4">
                            {{ $t("ID") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Name") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Orders.count") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Created_at") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(shipper, index) in shippers.data"
                        :key="index"
                        class="odd:bg-slate-200 even:bg-white grid grid-cols-[150px_1fr_200px_200px_200px] items-center text-center"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-primary whitespace-nowrap"
                        >
                            {{ `#${shipper.id}` }}
                        </th>
                        <td class="px-6 py-4">{{ shipper.name }}</td>
                        <td class="px-6 py-4">{{ shipper.orders_count }}</td>
                        <td class="px-6 py-4">{{ shipper.created_at }}</td>
                        <td
                            class="px-6 py-4 flex items-center gap-3 justify-center"
                        >
                            <ModalLink
                                :close-button="false"
                                :href="route('shippers.edit', shipper)"
                                class="bg-slate-700 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                            >
                                <i class="ri-edit-line"></i>
                            </ModalLink>
                            <button
                                @click="deleteShipper(shipper)"
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
            :links="shippers.meta.links"
            :previous="shippers.links.prev"
            :next="shippers.links.next"
        />
    </div>
    <NotFound v-else />
</template>
