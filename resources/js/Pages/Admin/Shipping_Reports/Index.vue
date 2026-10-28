<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import Paginator from "@/js/Components/Paginator.vue";
import NotFound from "@/js/Components/NotFound.vue";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router, usePage } from "@inertiajs/vue3";
const props = defineProps({
    reports: {
        type: Object,
    },
});
const isAdmin = usePage().props.auth.user.role === "admin";

function cleanReports() {
    Swal.fire({
        title: trans("Dialog.title"),
        text: trans("Cleanup.shippingReports.confirm"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6b7280",
        confirmButtonText: trans("Cleanup.confirm"),
        cancelButtonText: trans("Dialog.cancelButtonText"),
        width: 450,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("shipping_reports.clean"), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Done"),
                        text: trans("Cleanup.shippingReports.success"),
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
            router.delete(route("shipping_reports.destroy", report), {
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

function downloadFile(type, id) {
    let routeName =
        type === "excel"
            ? "shipping-reports.download-excel"
            : "shipping-reports.download-pdf";
    window.location.href = route(routeName, id);
}
</script>

<template>

    <Head :title="$t('Shipping Reports')" />
    <PageHeader :page-title="$t('Shipping Reports')">
        <div class="flex flex-wrap gap-2">
            <button v-if="isAdmin" @click="cleanReports"
                class="inline-flex items-center justify-center rounded-xl border border-red-600 bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-red-700 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-800">
                {{ $t("Cleanup.shippingReports.button") }}
            </button>
            <CreateNewModal href="shipping_reports.create" :close-button="false" :label="$t('New Report')" />
        </div>
    </PageHeader>

    <div v-if="reports.data.length" class="mt-4 overflow-x-auto rounded-2xl">
        <table class="table-fixed">
            <colgroup>
                <col class="w-[60%]" />
                <col class="w-[15%]" />
                <col class="w-[300px] max-w-[300px]" />
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">{{ $t("Name") }}</th>
                    <th scope="col">{{ $t("Orders") }}</th>
                    <th scope="col">{{ $t("Actions") }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="report in reports.data" :key="report.id">
                    <td class="truncate font-semibold">{{ report.name }}</td>
                    <td>
                        <span class="inline-flex items-center gap-2">
                            <i class="ri-shopping-cart-fill text-slate-400 dark:text-neutral-500"
                                aria-hidden="true"></i>
                            {{ report.orders_count }}
                        </span>
                    </td>
                    <td>
                        <div class="flex flex-wrap items-center gap-2">
                            <button @click="downloadFile('excel', report.id)"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50"
                                :title="$t('Excel File')" :aria-label="$t('Excel File')">
                                <i class="ri-file-excel-line" aria-hidden="true"></i>
                                <span>{{ $t("Excel File") }}</span>
                            </button>
                            <button @click="downloadFile('pdf', report.id)"
                                class="inline-flex items-center gap-2 rounded-lg bg-sky-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-sky-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500/50"
                                :title="$t('PDF File')" :aria-label="$t('PDF File')">
                                <i class="ri-file-pdf-line" aria-hidden="true"></i>
                                <span>{{ $t("PDF File") }}</span>
                            </button>
                            <button @click="deleteReport(report)"
                                class="rounded-lg bg-red-50 p-2 text-red-600 transition hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/70"
                                :title="$t('Delete')" :aria-label="$t('Delete')">
                                <i class="ri-delete-bin-fill" aria-hidden="true"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Paginator variant="admin" :links="reports.links" :previous="reports.prev_page_url"
            :next="reports.next_page_url" class="mt-4" />
    </div>
    <NotFound v-else />
</template>
