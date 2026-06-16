<script setup>
import { ref } from "vue";
const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
});

const modalRef = ref(null);
</script>

<template>
    <Modal
        v-slot="{ close }"
        ref="modalRef"
        panel-classes="bg-white rounded mx-auto md:max-w-lg max-h-100 overflow-y-auto"
    >
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Invoice.details") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>

        <p
            class="text-white px-4 capitalize rounded mt-4 py-2 font-medium w-max leading-none"
            :class="{
                'bg-red-500': invoice.data.type == 'expense',
                'bg-green-600': invoice.data.type == 'revenue',
            }"
        >
            {{ $t(invoice.data.type) }}
        </p>
        <div class="mt-4 text-primary text-xl">
            <p dir="auto" class="font-medium">{{ invoice.data.title }}</p>
            <p class="">{{ $t(invoice.data.category) }}</p>
            <p class="text-sky-700 font-semibold">
                {{ `${invoice.data.amount} ${$t("currency")}` }}
            </p>
            <div class="flex gap-2 items-center text-base mt-2">
                <i class="ri-calendar-line text-xl text-slate-400"></i>

                <p class="text-primary">{{ invoice.data.created_at }}</p>
            </div>
        </div>

        <p
            v-if="
                invoice.data.description &&
                invoice.data.invoiceable_type != 'App\\Models\\Order'
            "
            dir="auto"
            class="text-primary mt-2"
        >
            {{ invoice.data.description }}
        </p>
    </Modal>
</template>
