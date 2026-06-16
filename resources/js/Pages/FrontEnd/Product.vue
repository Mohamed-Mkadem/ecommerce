<script setup>
import PageTitle from "@/js/Components/FrontEnd/PageTitle.vue";
import { usePage } from "@inertiajs/vue3";
import { useCartStore } from "@/js/stores/Cart";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import { ref } from "vue";
import NotFound from "@/js/Components/NotFound.vue";
import Review from "@/js/Components/Review.vue";
import TextInput from "@/js/Components/TextInput.vue";
import Paginator from "@/js/Components/Paginator.vue";

const toast = useToast();
const cartStore = useCartStore();

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    reviews: {
        type: Object,
        required: true,
    },
});
const media = usePage().props.product.data.media;
let currentImage = ref(media[0]);
function changeImage(image) {
    currentImage.value = image;
}

const loadingState = ref({});
const totalQuantity = ref(1);
const increaseQuantity = (product) => {
    if (product.type === "product")
        totalQuantity.value = totalQuantity.value + 0.5;
    if (product.type === "pack") {
        totalQuantity.value = totalQuantity.value + 1;
    }
};
const decreaseQuantity = (product) => {
    if (product.type === "product" && totalQuantity.value > 0.5)
        totalQuantity.value = totalQuantity.value - 0.5;
    if (product.type === "pack" && totalQuantity.value > 1)
        totalQuantity.value = totalQuantity.value - 1;
};
function handleAddToCart(product, quantity = 1) {
    loadingState.value[product.id] = true;
    cartStore.addToCart(product, quantity);

    toast.success(trans("Cart.added"), { ...getToastOptions(), timeout: 1000 });

    loadingState.value[product.id] = false;
    totalQuantity.value = 1;
}
</script>

<template>
    <Head :title="product.data.name" />
    <div class="py-12 px-4 md:px-6 lg:px-12 max-w-screen-3xl mx-auto">
        <PageTitle class="mb-8">{{ product.data.name }}</PageTitle>

        <div
            class="md:grid md:grid-cols-[350px,_1fr] lg:grid-cols-[450px,_1fr] md:gap-8 items-start relative py-8"
        >
            <div
                id="product-images"
                class="md:sticky md:top-8 ltr:md:left-0 rtl:md:right-0 md:overflow-visible md:max-h-screen"
            >
                <div>
                    <img
                        :src="
                            currentImage
                                ? currentImage.original_url
                                : product.data.main_image_url
                        "
                        :alt="
                            currentImage
                                ? currentImage.file_name
                                : 'Default Product Image'
                        "
                        class="rounded-lg mx-auto"
                    />
                </div>
                <div
                    class="grid grid-cols-[repeat(4,_minmax(50px,_100px))] justify-between gap-2 md:gap-4 mt-4"
                >
                    <div
                        v-if="media"
                        v-for="(image, index) in media"
                        :key="index"
                        class="rounded-lg"
                        :class="{
                            ' border-primary border-[3px] ':
                                currentImage.id == image.id,
                        }"
                    >
                        <img
                            @click="changeImage(image)"
                            :src="image.original_url"
                            :alt="image.file_name"
                            class="w-full rounded-md"
                        />
                    </div>
                </div>
            </div>

            <div class="mt-4 md:mt-0">
                <h2 class="text-3xl text-primary font-semibold mt-2 mb-4">
                    {{ product.data.name }}
                </h2>
                <div class="flex items-center gap-4 mb-4">
                    <span
                        v-if="
                            product.data.discount_type == 'fixed' &&
                            product.data.discount
                        "
                        class="text-primary text-xl line-through border-dashed"
                    >
                        {{ product.data.discount }} {{ $t("currency") }}
                    </span>
                    <span
                        v-if="
                            product.data.discount_type == 'percentage' &&
                            product.data.discount
                        "
                        class="bg-red-500 font-semibold text-white px-4 text-center py-1 rounded-2xl"
                    >
                        {{ $t("discount.save") }} {{ product.data.discount }} %
                    </span>
                    <p class="text-2xl font-semibold text-meta-1">
                        {{ product.data.price }} {{ $t("Product.currency") }}
                    </p>
                </div>
                <!-- <div class="flex items-center gap-3">
                    <i class="ri-star-line text-2xl text-slate-400"></i>
                    <p
                        class="text-primary flex items-center gap-2"
                        v-if="product.data.rate"
                    >
                        <span class="font-semibold text-xl">{{
                            product.data.rate
                        }}</span>
                        <span> - ({{ product.data.reviews_count }}) </span>
                    </p>
                    <p v-else class="text-primary">
                        {{ $t("Product.notRated") }}
                    </p>
                </div> -->

                <div
                    class="flex items-center gap-3"
                    v-if="product.data.type == 'pack'"
                >
                    <i class="ri-calendar-line text-2xl text-slate-400"></i>
                    <p class="text-primary">
                        {{ $t("Pack.validity") }} -
                        {{ product.data.ends_at }}
                    </p>
                </div>

                <div
                    class="mt-4 grid grid-cols-1 min-[450px]:grid-cols-2 md:grid-cols-1 min-[800px]:grid-cols-2 items-center gap-4"
                >
                    <div class="flex items-center w-full justify-between">
                        <button
                            class="border border-neutral-200 min-w-12 h-12 text-primary font-semibold hover:bg-neutral-100"
                            @click="decreaseQuantity(product.data)"
                        >
                            -
                        </button>

                        <TextInput
                            :required="false"
                            v-model="totalQuantity"
                            readonly
                            type="text"
                            class="h-12 text-center border border-neutral-200 font-semibold min-w-[50px]"
                        />

                        <button
                            class="border border-neutral-200 min-w-12 h-12 text-primary font-semibold hover:bg-neutral-100"
                            @click="increaseQuantity(product.data)"
                        >
                            +
                        </button>
                    </div>
                    <div>
                        <button
                            @click="
                                handleAddToCart(product.data, totalQuantity)
                            "
                            :class="[
                                ' bg-sky-900 text-white hover:bg-opacity-90 px-4 py-3 text-center block font-semibold rounded w-full',
                                {
                                    'opacity-50 cursor-not-allowed':
                                        loadingState[product.data.id],
                                },
                            ]"
                            :disabled="loadingState[product.data.id]"
                        >
                            {{
                                loadingState[product.data.id]
                                    ? $t("Cart.loading")
                                    : $t("Cart.add")
                            }}
                        </button>
                    </div>
                </div>

                <div
                    v-html="product.data.description"
                    
                    class="text-xl text-primary mt-4"
                    id="description-holder"
                ></div>
            </div>
        </div>

        <div class="mt-20">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <h2
                    class="text-2xl md:text-3xl text-primary mt-6 mb-4 font-semibold"
                >
                    {{ $t("Reviews") }}
                </h2>

                <ModalLink
                    class="bg-green-500 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
                    :href="route('FE.reviews.create', product.data)"
                    :close-button="false"
                >
                    {{ $t("Add New Review") }}
                </ModalLink>
            </div>
            <div v-if="reviews.data.length">
                <div class="py-6 mt-2">
                    <Review
                        v-for="(review, index) in reviews.data"
                        :review="review"
                        :key="index"
                        class="mb-4 bg-zinc-100 p-4 rounded-md shadow-1"
                    />
                </div>
                <Paginator
                    :links="reviews.meta.links"
                    :previous="reviews.links.prev"
                    :next="reviews.links.next"
                    class="mt-4"
                />
            </div>

            <NotFound v-else message="NotFound.product_reviews.FE" />
        </div>
    </div>
</template>

<style>
#description-holder {
    h3 {
        font-weight: 600;
        margin-bottom: 10px;
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
