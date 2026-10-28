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
            <button @click="cleanReports" class="inline-flex items-center justify-center rounded-xl border border-red-600 bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-red-700 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-800">
                {{ $t("Cleanup.orderReports.button") }}
            </button>
            <CreateNewModal :close-button="false" href="order_reports.create" :label="$t('New Order Report')" />
        </div>
    </PageHeader>

    <div v-if="reports.data.length" class="mt-4 overflow-x-auto rounded-2xl">
        <table>
            <thead>
                <tr>
                    <th scope="col">{{ $t("Name") }}</th>
                    <th scope="col">{{ $t("Created by") }}</th>
                    <th scope="col">{{ $t("Start Date") }}</th>
                    <th scope="col">{{ $t("End Date") }}</th>
                    <th scope="col">{{ $t("Actions") }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="report in reports.data" :key="report.id">
                    <td class="font-semibold">{{ report.name }}</td>
                    <td>{{ report.user?.first_name }} {{ report.user?.last_name }}</td>
                    <td class="whitespace-nowrap">{{ report.start_date }}</td>
                    <td class="whitespace-nowrap">{{ report.end_date }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <button @click="downloadReport(report)"
                                class="rounded-lg bg-emerald-600 p-2 text-white transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50"
                                :title="$t('Download Excel')" :aria-label="$t('Download Excel')"><i class="ri-file-excel-line" aria-hidden="true"></i></button>
                            <button @click="deleteReport(report)"
                                class="rounded-lg bg-red-500 p-2 text-white transition hover:bg-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50"
                                :title="$t('Delete')" :aria-label="$t('Delete')"><i class="ri-delete-bin-fill" aria-hidden="true"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Paginator variant="admin" :links="reports.links" :previous="reports.prev_page_url" :next="reports.next_page_url"
            class="mt-4" />
    </div>
    <NotFound v-else />
</template>
