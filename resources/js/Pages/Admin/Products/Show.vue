<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { usePage, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import ProductImagePlaceholder from "@/assets/images/product.webp";
import Activities from "@/js/Components/Admin/Activities.vue";
const props = defineProps(["product", "activities"]);
const product = usePage().props.product.data;

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
            router.delete(route("products.destroy", product), {
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
    <PageHeader :pageTitle="product.name">
        <div class="flex items-center gap-4">
            <Link
                :href="route('products.edit', product)"
                class="bg-slate-500 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
                >{{ $t("Edit") }}</Link
            >
            <button
                @click="deleteProduct(product.id)"
                class="bg-red-500 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
            >
                {{ $t("Delete") }}
            </button>
        </div>
    </PageHeader>
    <div
        class="bg-white p-4 rounded-md shadow-1 md:grid md:grid-cols-[350px,_1fr] md:gap-4 lg:gap-8"
    >
        <div>
            <div>
                <img
                    :src="ProductImagePlaceholder"
                    :alt="
                        currentImage
                            ? currentImage.file_name
                            : 'Default Product Image'
                    "
                    class="rounded-lg mx-auto"
                />
            </div>
        </div>
        <div class="mt-4 md:mt-0">
            <h2 class="text-3xl text-primary font-semibold mt-2 mb-4">
                {{ product.name }}
            </h2>
            <p class="text-2xl font-semibold text-meta-1 mb-4">
                {{ product.price }} {{ $t("Product.currency") }}
            </p>
            <div class="flex items-center gap-3">
                <i class="ri-star-line text-2xl text-slate-400"></i>
                <p
                    class="text-primary flex items-center gap-2"
                    v-if="product.rate"
                >
                    <span class="font-semibold text-xl">{{
                        product.rate
                    }}</span>
                    <span> - ({{ product.reviews_count }}) </span>
                </p>
                <p v-else class="text-primary">
                    {{ $t("Product.notRated") }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <i class="ri-shopping-cart-line text-2xl text-slate-400"></i>
                <p class="text-primary">
                    {{ product.orders_count }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <i class="ri-discount-percent-line text-2xl text-slate-400"></i>
                <p class="text-primary">
                    {{ $t("Discount.type") }} :
                    {{ $t(product.discount_type) }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <i class="ri-discount-percent-line text-2xl text-slate-400"></i>
                <p class="text-primary">
                    {{ $t("Discount.value") }} :
                    {{
                        `${product.discount} ${product.discount_type == "percentage" ? "%" : $t("currency")}`
                    }}
                </p>
            </div>
        </div>
    </div>

    <section>
        <Activities :activities="activities" :withLinks="false" class="mt-8" />
    </section>
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
