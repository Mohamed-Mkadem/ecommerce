<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import Swal from "sweetalert2";
import ProductImagePlaceholder from "@/assets/images/product.webp";
const props = defineProps({
    wrapper: {
        type: Object,
        required: true,
    },
});

const toast = useToast();
const page = usePage();

const currentImage = ref(
    props.wrapper.media?.[0]?.url ?? props.wrapper.main_image_url
);

function changeImage(url) {
    currentImage.value = url;
}

onMounted(() => {
    if (page.props.flash?.success) {
        toast.success(page.props.flash.success, getToastOptions());
    }
});

function deleteWrapper() {
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
            router.delete(route("wrappers.destroy", props.wrapper.slug), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Wrapper.deleted_successfully"),
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
    <Head :title="wrapper.title" />

    <PageHeader :page-title="wrapper.title">
        <div class="flex flex-wrap items-center gap-3">
            <Link
                :href="route('wrappers.edit', wrapper.slug)"
                class="bg-slate-500 text-white px-3 py-2 rounded-md hover:bg-opacity-75 flex items-center gap-2"
            >
                <span>{{ $t("Edit") }}</span>
                <i class="ri-edit-line"></i>
            </Link>
            <button
                type="button"
                @click="deleteWrapper"
                class="bg-red-500 text-white px-3 py-2 rounded-md hover:bg-opacity-75 flex items-center gap-2"
            >
                <span>{{ $t("Delete") }}</span>
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    </PageHeader>

    <div
        class="bg-white p-4 md:p-6 rounded-md shadow-1 md:grid md:grid-cols-[320px,_1fr] lg:grid-cols-[380px,_1fr] gap-6 lg:gap-8"
    >
        <div>
            <img
                :src="currentImage"
                :alt="wrapper.title"
                class="rounded-2xl mx-auto w-full  shadow-lg object-contain"
            />
            <div
                v-if="wrapper.media && wrapper.media.length > 0"
                class="grid grid-cols-[repeat(4,_minmax(50px,_100px))] justify-between gap-2  mt-4"
            >
                <div
                    v-for="img in wrapper.media"
                    :key="img.id"
                    class="cursor-pointer border-2 rounded p-0.5 transition-all"
                    :class="{
                        'border-primary': currentImage === img.url,
                        'border-neutral-200 hover:border-neutral-300': currentImage !== img.url
                    }"
                    @click="changeImage(img.url)"
                >
                    <img
                        :src="img.url"
                        :alt="img.name"
                        class="w-full rounded-md cursor-pointer"
                    />
                </div>
            </div>
        </div>

        <div>
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span
                    class="px-3 py-1 rounded-md text-sm font-medium text-white"
                    :class="wrapper.is_active ? 'bg-green-600' : 'bg-red-500'"
                >
                    {{
                        wrapper.is_active
                            ? $t("Wrapper.active")
                            : $t("Wrapper.inactive")
                    }}
                </span>
                <span class="text-sm text-neutral-600">
                    {{ $t("Wrapper.slug") }}: {{ wrapper.slug }}
                </span>
            </div>

            <dl class="grid gap-3 sm:grid-cols-2 mb-6">
                <div>
                    <dt class="text-sm text-neutral-500">
                        {{ $t("Wrapper.default_variant") }}
                    </dt>
                    <dd class="font-semibold text-primary">
                        <template v-if="wrapper.default_product">
                            <Link
                                :href="
                                    route(
                                        'products.show',
                                        wrapper.default_product.id,
                                    )
                                "
                                class="hover:underline"
                            >
                                {{ wrapper.default_product.name }}
                            </Link>
                        </template>
                        <span v-else>—</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-neutral-500">
                        {{ $t("Product.price") }}
                    </dt>
                    <dd class="font-semibold text-meta-1 text-xl">
                        {{ wrapper.price }}
                        {{ $t("Product.currency") }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-neutral-500">
                        {{ trans("Products") }}
                    </dt>
                    <dd class="font-semibold text-primary">
                        {{ wrapper.products_count }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-neutral-500">
                        {{ $t("Wrapper.created_at") }}
                    </dt>
                    <dd class="font-semibold text-primary">
                        {{ wrapper.created_at ?? "—" }}
                    </dd>
                </div>
            </dl>

            <div v-if="wrapper.description" class="mb-6">
                <h3 class="text-lg font-semibold text-graydark mb-2">
                    {{ $t("Wrapper.description") }}
                </h3>
                <div
                    class="text-primary prose prose-sm max-w-none"
                    v-html="wrapper.description"
                ></div>
            </div>
        </div>
    </div>

    <div class="bg-white p-4 md:p-6 rounded-md shadow-1 mt-8">
        <h2 class="text-xl font-semibold text-graydark mb-4">
            {{ $t("Wrapper.selected_products") }}
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-neutral-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-graydark">
                            {{ $t("Name") }}
                        </th>
                       
                        <th class="px-4 py-3 font-semibold text-graydark">
                            {{ $t("Product.price") }}
                        </th>
                        <th class="px-4 py-3 font-semibold text-graydark">
                            {{ $t("Wrapper.display_order") }}
                        </th>
                        <th class="px-4 py-3 font-semibold text-graydark">
                            {{ $t("Wrapper.is_default") }}
                        </th>
                        <th class="px-4 py-3 font-semibold text-graydark">
                            {{ $t("Wrapper.free_shipping") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="variant in wrapper.variants"
                        :key="variant.id"
                        class="border-b border-neutral-100 hover:bg-neutral-50"
                        :class="{
                            'bg-violet-50': variant.is_default,
                        }"
                    >
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img
                                    :src="ProductImagePlaceholder"
                                    :alt="variant.name"
                                    class="w-12 h-12 rounded object-cover flex-shrink-0"
                                />
                                <Link
                                    :href="route('products.show', variant.id)"
                                    class="font-medium text-primary hover:underline"
                                >
                                    {{ variant.name }}
                                </Link>
                            </div>
                        </td>
                       
                        <td class="px-4 py-3">
                            {{ variant.price }}
                            {{ $t("Product.currency") }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            {{ variant.display_order + 1 }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <i
                                v-if="variant.is_default"
                                class="ri-check-line text-xl text-green-600"
                            ></i>
                            <span v-else class="text-neutral-400">—</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <i
                                v-if="variant.free_shipping"
                                class="ri-truck-line text-xl text-sky-700"
                            ></i>
                            <span v-else class="text-neutral-400">—</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
