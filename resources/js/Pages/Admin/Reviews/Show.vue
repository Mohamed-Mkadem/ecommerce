<script setup>
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
const props = defineProps({
    review: { type: Object, required: true },
});

const modalRef = ref(null);

function handleLink(link) {
    modalRef.value.close();

    router.get(link);
}

function deleteReview(review) {
    modalRef.value.close();
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
            router.delete(route("reviews.destroy", review), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Review.deleted"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                },
            });
        }
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        ref="modalRef"
        panel-classes="bg-white rounded mx-auto md:max-w-lg max-h-100  overflow-y-auto mt-6 md:mt-0"
    >
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Review Details") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>

        <div class="my-4">
            <h3 class="font-medium text-sky-800 text-lg mb-3">
                {{ $t("Product") }}
            </h3>
            <div class="flex items-center gap-3">
                <img
                    :src="review.data.product.main_image_url"
                    class="w-20 rounded"
                />
                <div class="font-medium">
                    <button
                        class="text-primary hover:text-slate-600 hover:underline"
                        @click="
                            handleLink(
                                route('products.show', review.data.product),
                            )
                        "
                    >
                        {{ review.data.product.name }}
                    </button>
                    <p class="text-primary flex items-center gap-1">
                        <i class="ri-star-line text-lg text-slate-400"></i>
                        <span> {{ review.data.product.rate }} - </span>
                        <span>({{ review.data.product.reviews_count }})</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="my-4">
            <h3 class="font-medium text-sky-800 text-lg mb-3">
                {{ $t("Client") }}
            </h3>
            <div class="flex items-center mb-3">
                <img
                    class="w-10 h-10 me-4 rounded-full"
                    :src="`${$page.props.base_url}storage/avatars/default.jpg`"
                    alt=""
                />
                <div class="font-medium">
                    <button
                        v-if="!review.data.client.deleted_at"
                        class="text-primary hover:text-slate-600 hover:underline"
                        @click="
                            handleLink(
                                route('clients.show', review.data.client),
                            )
                        "
                    >
                        {{ review.data.client_name }}
                    </button>
                    <p v-else class="text-primary">
                        {{ review.data.client_name }}
                        <span class="text-red-600 text-xs"
                            >({{ $t("Deleted") }})</span
                        >
                    </p>
                    <small class="block text-xs text-graydark">
                        <i class="ri-star-line text-slate-400"></i>
                        {{ review.data.client.reviews_count }}
                    </small>
                </div>
            </div>
        </div>

        <div class="my-4">
            <h3 class="font-medium text-sky-800 text-lg">
                {{ $t("Review") }}
            </h3>
            <div class="flex items-center gap-3 text-gray">
                <i
                    class="ri-star-fill"
                    v-for="index in 5"
                    :key="index"
                    :class="{
                        'text-yellow-500': index <= review.data.stars,
                        'text-slate-400': index > review.data.stars,
                    }"
                />
            </div>

            <p class="mt-2 text-primary" v-if="review.data.comment" dir="auto">
                {{ review.data.comment }}
            </p>
        </div>

        <div class="flex justify-end">
            <button
                class="text-white font-medium bg-red-600 hover:bg-opacity-80 rounded px-4 py-2"
                @click="deleteReview(review.data)"
            >
                {{ $t("Delete") }}
            </button>
        </div>
    </Modal>
</template>
