<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
import Paginator from "@/js/Components/Paginator.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    reports: {
        type: Object,
    },
});

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
            router.delete(route("selling_reports.destroy", report), {
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
    const routeName =
        type === "pdf"
            ? "selling-reports.download-pdf"
            : "selling-reports.download-excel";
    window.location.href = route(routeName, id);
}
</script>

<template>
    <Head :title="$t('Selling Reports')" />
    <PageHeader :page-title="$t('Selling Reports')">
        <CreateNewModal
            :close-button="false"
            href="selling_reports.create"
            :label="$t('New Selling Report')"
        />
    </PageHeader>

    <div v-if="reports.data.length" class="mt-4 overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-1 overflow-hidden">
            <thead class="bg-slate-300 text-slate-700">
                <tr>
                    <th class="px-4 py-3 text-center">{{ $t("Name") }}</th>
                    <th class="px-4 py-3 text-center">
                        {{ $t("Created by") }}
                    </th>
                    <th class="px-4 py-3 text-center">
                        {{ $t("Start date") }}
                    </th>
                    <th class="px-4 py-3 text-center">{{ $t("End date") }}</th>
                    <th class="px-4 py-3 text-center">{{ $t("Actions") }}</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="report in reports.data"
                    :key="report.id"
                    class="border-t border-slate-200"
                >
                    <td
                        class="text-center px-4 py-3 font-semibold text-primary"
                    >
                        {{ report.name }}
                    </td>
                    <td class="text-center px-4 py-3">
                        {{ report.user?.first_name }}
                        {{ report.user?.last_name }}
                    </td>
                    <td class="text-center px-4 py-3">
                        {{ report.start_date }}
                    </td>
                    <td class="text-center px-4 py-3">{{ report.end_date }}</td>
                    <td class="text-center px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <button
                                @click="downloadFile('pdf', report.id)"
                                class="bg-sky-800 text-white p-2 rounded-md hover:bg-opacity-75"
                                :title="$t('Download PDF')"
                            >
                                <i class="ri-file-pdf-line"></i>
                            </button>
                            <button
                                @click="downloadFile('excel', report.id)"
                                class="bg-emerald-700 text-white p-2 rounded-md hover:bg-opacity-75"
                                :title="$t('Download Excel')"
                            >
                                <i class="ri-file-excel-line"></i>
                            </button>
                            <button
                                @click="deleteReport(report)"
                                class="bg-red-500 text-white p-2 rounded-md hover:bg-opacity-75"
                                :title="$t('Delete')"
                            >
                                <i class="ri-delete-bin-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <Paginator
            :links="reports.links"
            :previous="reports.prev_page_url"
            :next="reports.next_page_url"
            class="mt-4"
        />
    </div>
    <NotFound v-else />
</template>
