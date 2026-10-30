<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { usePage, router } from "@inertiajs/vue3";
import { computed } from "vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import Activities from "@/js/Components/Admin/Activities.vue";
const props = defineProps(["product", "activities"]);
const product = computed(() => usePage().props.product.data);

const deleteProduct = () => {
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
            router.delete(route("products.destroy", product.value), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Product.deleted"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                },
            });
        }
    });
};
</script>

<template>
    <Head :title="product.name" />
    <PageHeader :page-title="product.name">
        <div v-if="$page.props.auth.user.role === 'admin'" class="flex flex-wrap items-center gap-3">
            <Link
                :href="route('products.edit', product)"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900"
            >
                <i class="ri-edit-line" aria-hidden="true"></i>
                {{ $t("Edit") }}
            </Link>
            <button
                @click="deleteProduct(product.id)"
                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900"
            >
                <i class="ri-delete-bin-line" aria-hidden="true"></i>
                {{ $t("Delete") }}
            </button>
        </div>
    </PageHeader>
    <div class="grid gap-6 lg:grid-cols-[minmax(280px,380px)_minmax(0,1fr)]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
            <div class="flex min-h-72 items-center justify-center overflow-hidden rounded-xl bg-slate-50 p-4 dark:bg-neutral-900/50">
                <img
                    :src="product.main_image_url"
                    :alt="product.name"
                    class="max-h-[480px] w-full rounded-lg object-contain"
                />
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
            <div class="border-b border-slate-100 pb-5 dark:border-neutral-700">
                <h2 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100 sm:text-3xl">
                    {{ product.name }}
                </h2>
                <div v-if="product.wrapper" class="mt-3 flex items-center gap-2 text-slate-500 dark:text-neutral-400">
                    <i class="ri-archive-line text-lg" aria-hidden="true"></i>
                    <Link
                        :href="product.wrapper.url"
                        class="inline-flex items-center gap-1 font-medium text-sky-700 transition-colors hover:text-sky-800 hover:underline dark:text-sky-400 dark:hover:text-sky-300"
                    >
                        {{ product.wrapper.title }}
                        <i class="ri-external-link-line" aria-hidden="true"></i>
                    </Link>
                </div>
            </div>

            <div class="mt-5 rounded-xl bg-slate-50 p-4 dark:bg-neutral-700/60">
                <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                    {{ $t("Product.price") }}
                </p>
                <p class="mt-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-neutral-100">
                    {{ product.price }} {{ $t("Product.currency") }}
                </p>
            </div>

            <dl class="mt-5 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-100 p-4 dark:border-neutral-700">
                    <dt class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-neutral-400">
                        <i class="ri-shopping-cart-line" aria-hidden="true"></i>
                        {{ $t("Orders") }}
                    </dt>
                    <dd class="mt-1 text-lg font-semibold text-slate-800 dark:text-neutral-100">
                        {{ product.orders_count }}
                    </dd>
                </div>
                <div class="rounded-xl border border-slate-100 p-4 dark:border-neutral-700">
                    <dt class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-neutral-400">
                        <i class="ri-discount-percent-line" aria-hidden="true"></i>
                        {{ $t("Discount.type") }}
                    </dt>
                    <dd class="mt-1 text-lg font-semibold text-slate-800 dark:text-neutral-100">
                        {{ $t(product.discount_type) }}
                    </dd>
                </div>
                <div class="rounded-xl border border-slate-100 p-4 dark:border-neutral-700 sm:col-span-2">
                    <dt class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-neutral-400">
                        <i class="ri-price-tag-3-line" aria-hidden="true"></i>
                        {{ $t("Discount.value") }}
                    </dt>
                    <dd class="mt-1 text-lg font-semibold text-slate-800 dark:text-neutral-100">
                        {{ `${product.discount} ${product.discount_type == "percentage" ? "%" : $t("currency")}` }}
                    </dd>
                </div>
            </dl>
        </section>
    </div>

    <Activities :activities="activities" :with-links="false" class="mt-6" />
</template>

<style>
#description-holder {
    h3 {
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 20px;
    }

    ul,
    ol {
        margin-block: 16px;
        list-style: inside disc;
    }

    p {
        margin-block: 10px;
    }
}

#description-holder ol li p,
#description-holder ul li p {
    display: inline;
}
</style>
