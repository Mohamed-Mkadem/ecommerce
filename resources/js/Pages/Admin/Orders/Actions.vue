<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
</script>

<template>
    <Head :title="$t('Actions')" />
    <PageHeader :page-title="$t('Actions')"> </PageHeader>

    <div class="grid gap-4 items-center justify-center">
        <ModalLink
            :href="route('orders.deliveryDate.import.create')"
            :close-button="false"
            class="bg-yellow-500 text-black font-semibold text-center px-4 py-2 rounded-md hover:bg-opacity-75"
        >
            <i class="ri-upload-line"> </i>
            {{ $t("Import Delivery Date Updates") }}
        </ModalLink>
        <ModalLink
            :href="route('orders.excel.import.create')"
            :close-button="false"
            class="bg-emerald-600 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
        >
            <i class="ri-upload-line"> </i>
            {{ $t("Import Orders from Excel") }}
        </ModalLink>
        <ModalLink
            :href="route('orders.import.create')"
            :close-button="false"
            class="bg-sky-800 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
        >
            <i class="ri-upload-line"> </i>
            {{ $t("Import Shipping Updates") }}
        </ModalLink>
        <ModalLink
            :href="route('orders.shipperUpdate.import.create')"
            :close-button="false"
            class="bg-slate-700 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
        >
            <i class="ri-upload-line"> </i>
            {{ $t("Import Shipper Updates") }}
        </ModalLink>
        <ModalLink
            :href="route('orders.amountUpdate.import.create')"
            :close-button="false"
            class="bg-teal-800 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
        >
            <i class="ri-upload-line"> </i>
            {{ $t("Import Amount Updates") }}
        </ModalLink>
        <ModalLink
            :href="route('orders.pendingUpdate.import.create')"
            :close-button="false"
            class="bg-slate-800 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
        >
            <i class="ri-upload-line"> </i>
            {{ $t("Mark Orders as pending") }}
        </ModalLink>
    </div>
</template>
