<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import Paginator from "@/js/Components/Paginator.vue";
import NotFound from "@/js/Components/NotFound.vue";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
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
        <CreateNewModal
            href="shipping_reports.create"
            :close-button="false"
            :label="$t('New Report')"
        >
        </CreateNewModal>
    </PageHeader>

    <div v-if="reports.data.length">
        <div
            class="bg-white rounded shadow-1 px-5 md:grid grid-cols-[1fr,_100px,_1fr,auto] md:gap-4 md:items-center mt-4"
            v-for="report in reports.data"
            :key="report.id"
        >
            <p
                class="text-primary font-semibold text-lg border-b border-neutral-200 py-3 text-center md:text-start md:border-none"
            >
                {{ report.name }}
            </p>
            <p
                class="border-b border-neutral-200 py-3 text-center md:text-start md:border-none"
            >
                <i
                    class="ri-shopping-cart-fill text-slate-400 text-lg mx-3"
                ></i>
                <span>{{ report.orders_count }}</span>
            </p>
            <div
                class="flex items-center gap-3 py-3 justify-center flex-wrap md:justify-end"
            >
                <button
                    @click="downloadFile('excel', report.id)"
                    class="bg-sky-800 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
                >
                    <i class="ri-download-line me-2"></i>
                    <span> {{ $t("Excel File") }} </span>
                </button>
                <button
                    @click="downloadFile('pdf', report.id)"
                    class="bg-sky-800 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
                >
                    <i class="ri-download-line me-2"></i>
                    <span> {{ $t("PDF File") }} </span>
                </button>
            </div>
            <button
                @click="deleteReport(report)"
                class="bg-red-500 text-white py-2 px-3 rounded-md hover:bg-opacity-75"
            >
                <i class="ri-delete-bin-fill"></i>
            </button>
        </div>
        <Paginator
            :links="reports.links"
            :previous="reports.prev_page_url"
            :next="reports.next_page_url"
            class="mt-4"
        />
    </div>
    <NotFound v-else />
</template>
