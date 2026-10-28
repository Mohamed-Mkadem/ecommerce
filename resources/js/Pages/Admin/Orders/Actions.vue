<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
</script>

<template>
    <Head :title="$t('Actions')" />
    <PageHeader :page-title="$t('Actions')"> </PageHeader>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <ModalLink
            :href="route('orders.deliveryDate.import.create')"
            :close-button="false"
            class="group flex min-h-28 items-center justify-between gap-4 rounded-2xl border border-neutral-300 bg-neutral-100 p-5 text-neutral-800 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-primary/40 hover:bg-neutral-200 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:border-neutral-500 dark:hover:bg-neutral-700 dark:focus-visible:ring-neutral-500/50"
        >
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-neutral-200 text-lg text-neutral-700 transition duration-200 group-hover:scale-105 group-hover:bg-neutral-300 dark:bg-neutral-700 dark:text-neutral-100 dark:group-hover:bg-neutral-600">
                <i class="ri-upload-line" aria-hidden="true"></i>
            </span>
            <span class="flex-1 font-semibold">{{ $t("Import Delivery Date Updates") }}</span>
        </ModalLink>
        <ModalLink
            :href="route('orders.excel.import.create')"
            :close-button="false"
            class="group flex min-h-28 items-center justify-between gap-4 rounded-2xl border border-neutral-300 bg-neutral-100 p-5 text-neutral-800 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-primary/40 hover:bg-neutral-200 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:border-neutral-500 dark:hover:bg-neutral-700 dark:focus-visible:ring-neutral-500/50"
        >
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-neutral-200 text-lg text-neutral-700 transition duration-200 group-hover:scale-105 group-hover:bg-neutral-300 dark:bg-neutral-700 dark:text-neutral-100 dark:group-hover:bg-neutral-600">
                <i class="ri-upload-line" aria-hidden="true"></i>
            </span>
            <span class="flex-1 font-semibold">{{ $t("Import Orders from Excel") }}</span>
        </ModalLink>
        <ModalLink
            :href="route('orders.import.create')"
            :close-button="false"
            class="group flex min-h-28 items-center justify-between gap-4 rounded-2xl border border-neutral-300 bg-neutral-100 p-5 text-neutral-800 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-primary/40 hover:bg-neutral-200 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:border-neutral-500 dark:hover:bg-neutral-700 dark:focus-visible:ring-neutral-500/50"
        >
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-neutral-200 text-lg text-neutral-700 transition duration-200 group-hover:scale-105 group-hover:bg-neutral-300 dark:bg-neutral-700 dark:text-neutral-100 dark:group-hover:bg-neutral-600">
                <i class="ri-upload-line" aria-hidden="true"></i>
            </span>
            <span class="flex-1 font-semibold">{{ $t("Import Shipping Updates") }}</span>
        </ModalLink>
        <ModalLink
            :href="route('orders.shipperUpdate.import.create')"
            :close-button="false"
            class="group flex min-h-28 items-center justify-between gap-4 rounded-2xl border border-neutral-300 bg-neutral-100 p-5 text-neutral-800 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-primary/40 hover:bg-neutral-200 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:border-neutral-500 dark:hover:bg-neutral-700 dark:focus-visible:ring-neutral-500/50"
        >
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-neutral-200 text-lg text-neutral-700 transition duration-200 group-hover:scale-105 group-hover:bg-neutral-300 dark:bg-neutral-700 dark:text-neutral-100 dark:group-hover:bg-neutral-600">
                <i class="ri-upload-line" aria-hidden="true"></i>
            </span>
            <span class="flex-1 font-semibold">{{ $t("Import Shipper Updates") }}</span>
        </ModalLink>
        <ModalLink
            :href="route('orders.amountUpdate.import.create')"
            :close-button="false"
            class="group flex min-h-28 items-center justify-between gap-4 rounded-2xl border border-neutral-300 bg-neutral-100 p-5 text-neutral-800 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-primary/40 hover:bg-neutral-200 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:border-neutral-500 dark:hover:bg-neutral-700 dark:focus-visible:ring-neutral-500/50"
        >
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-neutral-200 text-lg text-neutral-700 transition duration-200 group-hover:scale-105 group-hover:bg-neutral-300 dark:bg-neutral-700 dark:text-neutral-100 dark:group-hover:bg-neutral-600">
                <i class="ri-upload-line" aria-hidden="true"></i>
            </span>
            <span class="flex-1 font-semibold">{{ $t("Import Amount Updates") }}</span>
        </ModalLink>
        <ModalLink
            :href="route('orders.pendingUpdate.import.create')"
            :close-button="false"
            class="group flex min-h-28 items-center justify-between gap-4 rounded-2xl border border-neutral-300 bg-neutral-100 p-5 text-neutral-800 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-primary/40 hover:bg-neutral-200 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:border-neutral-500 dark:hover:bg-neutral-700 dark:focus-visible:ring-neutral-500/50"
        >
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-neutral-200 text-lg text-neutral-700 transition duration-200 group-hover:scale-105 group-hover:bg-neutral-300 dark:bg-neutral-700 dark:text-neutral-100 dark:group-hover:bg-neutral-600">
                <i class="ri-upload-line" aria-hidden="true"></i>
            </span>
            <span class="flex-1 font-semibold">{{ $t("Mark Orders as pending") }}</span>
        </ModalLink>
    </div>
</template>
