<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import CreateNew from "@/js/Components/CreateNew.vue";
import Card from "./Partials/Card.vue";
import NotFound from "@/js/Components/NotFound.vue";
import { usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";

defineProps({
    wrappers: {
        type: Array,
        required: true,
    },
});

const toast = useToast();
const page = usePage();

onMounted(() => {
    if (page.props.flash?.success) {
        toast.success(page.props.flash.success, getToastOptions());
    }
});
</script>

<template>
    <Head :title="$t('Wrappers')" />

    <PageHeader :page-title="$t('Wrappers')">
        <CreateNew href="wrappers.create" :label="$t('New Wrapper')" />
    </PageHeader>

    <h2 class="my-2 font-medium text-sky-900 text-xl">
        {{ $t("Results") }} : {{ wrappers.length }}
    </h2>

    <div v-if="wrappers.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(280px,_100%),_1fr))] gap-4"
        >
            <Card
                v-for="wrapper in wrappers"
                :key="wrapper.id"
                :wrapper="wrapper"
            />
        </div>
    </div>
    <NotFound v-else message="Wrapper.no_wrappers" />
</template>
