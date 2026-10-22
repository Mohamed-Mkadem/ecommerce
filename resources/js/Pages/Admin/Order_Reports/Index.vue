<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
import Paginator from "@/js/Components/Paginator.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";

defineProps({ reports: { type: Object, required: true } });

function cleanReports() {
    Swal.fire({
        title: trans("Dialog.title"),
        text: trans("Cleanup.orderReports.confirm"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6b7280",
        confirmButtonText: trans("Cleanup.confirm"),
        cancelButtonText: trans("Dialog.cancelButtonText"),
        width: 450,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("order_reports.clean"), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Done"),
                        text: trans("Cleanup.orderReports.success"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                },
            });
        }
    });
}

function deleteReport(report) {
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
            router.delete(route("order_reports.destroy", report), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("report.deleted"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                },
            });
        }
    });
}

function downloadReport(report) {
    window.location.href = route("order_reports.download", report.id);
}
</script>

<template>

    <Head :title="$t('Order Reports')" />
    <PageHeader :page-title="$t('Order Reports')">
        <div class="flex flex-wrap gap-2">
            <button @click="cleanReports" class="rounded-md bg-red-700 px-3 py-2 text-white hover:bg-opacity-75">
                {{ $t("Cleanup.orderReports.button") }}
            </button>
            <CreateNewModal :close-button="false" href="order_reports.create" :label="$t('New Order Report')" />
        </div>
    </PageHeader>

    <div v-if="reports.data.length" class="mt-4 overflow-x-auto">
        <table class="min-w-full overflow-hidden rounded-lg bg-white shadow-1">
            <thead class="bg-slate-300 text-slate-700">
                <tr>
                    <th class="px-4 py-3 text-center">{{ $t("Name") }}</th>
                    <th class="px-4 py-3 text-center">{{ $t("Created by") }}</th>
                    <th class="px-4 py-3 text-center">{{ $t("Start Date") }}</th>
                    <th class="px-4 py-3 text-center">{{ $t("End Date") }}</th>
                    <th class="px-4 py-3 text-center">{{ $t("Actions") }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="report in reports.data" :key="report.id" class="border-t border-slate-200">
                    <td class="px-4 py-3 text-center font-semibold text-primary">{{ report.name }}</td>
                    <td class="px-4 py-3 text-center">{{ report.user?.first_name }} {{ report.user?.last_name }}</td>
                    <td class="px-4 py-3 text-center">{{ report.start_date }}</td>
                    <td class="px-4 py-3 text-center">{{ report.end_date }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button @click="downloadReport(report)"
                                class="rounded-md bg-emerald-700 p-2 text-white hover:bg-opacity-75"
                                :title="$t('Download Excel')"><i class="ri-file-excel-line"></i></button>
                            <button @click="deleteReport(report)"
                                class="rounded-md bg-red-500 p-2 text-white hover:bg-opacity-75"
                                :title="$t('Delete')"><i class="ri-delete-bin-fill"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Paginator :links="reports.links" :previous="reports.prev_page_url" :next="reports.next_page_url"
            class="mt-4" />
    </div>
    <NotFound v-else />
</template>
