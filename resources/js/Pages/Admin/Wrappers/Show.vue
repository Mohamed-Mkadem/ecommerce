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
        <div v-if="page.props.auth.user.role === 'admin'" class="flex flex-wrap items-center gap-3">
            <Link
                :href="route('wrappers.edit', wrapper.slug)"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900"
            >
                <i class="ri-edit-line" aria-hidden="true"></i>
                <span>{{ $t("Edit") }}</span>
            </Link>
            <button
                type="button"
                @click="deleteWrapper"
                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900"
            >
                <span>{{ $t("Delete") }}</span>
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    </PageHeader>

    <div
        class="grid gap-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6 lg:grid-cols-[minmax(280px,380px)_minmax(0,1fr)] lg:gap-8"
    >
        <div>
            <img
                :src="currentImage"
                :alt="wrapper.title"
                class="mx-auto max-h-[520px] w-full rounded-xl bg-slate-50 object-contain p-3 dark:bg-neutral-900/50"
            />
            <div
                v-if="wrapper.media && wrapper.media.length > 0"
                class="mt-4 grid grid-cols-4 gap-2 sm:grid-cols-5"
            >
                <div
                    v-for="img in wrapper.media"
                    :key="img.id"
                    class="cursor-pointer rounded-lg border-2 p-1 transition-all"
                    :class="{
                        'border-teal-700 ring-2 ring-teal-700/15 dark:border-teal-500 dark:ring-teal-500/20': currentImage === img.url,
                        'border-slate-200 hover:border-slate-400 dark:border-neutral-700 dark:hover:border-neutral-500': currentImage !== img.url
                    }"
                    @click="changeImage(img.url)"
                >
                    <img
                        :src="img.url"
                        :alt="img.name"
                        class="aspect-square w-full rounded-md object-cover"
                    />
                </div>
            </div>
        </div>

        <div>
            <div class="mb-5 flex flex-wrap items-center gap-3 border-b border-slate-100 pb-5 dark:border-neutral-700">
                <span
                    class="rounded-lg px-3 py-1.5 text-sm font-semibold text-white"
                    :class="wrapper.is_active ? 'bg-green-600' : 'bg-red-500'"
                >
                    {{
                        wrapper.is_active
                            ? $t("Wrapper.active")
                            : $t("Wrapper.inactive")
                    }}
                </span>
                <span class="text-sm text-slate-500 dark:text-neutral-400">
                    {{ $t("Wrapper.slug") }}: {{ wrapper.slug }}
                </span>
            </div>

            <dl class="mb-6 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-neutral-700/60">
                    <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                        {{ $t("Wrapper.default_variant") }}
                    </dt>
                    <dd class="mt-1 font-semibold text-slate-800 dark:text-neutral-100">
                        <template v-if="wrapper.default_product">
                            <Link
                                :href="
                                    route(
                                        'products.show',
                                        wrapper.default_product.id,
                                    )
                                "
                                class="text-sky-700 hover:underline dark:text-sky-400"
                            >
                                {{ wrapper.default_product.name }}
                            </Link>
                        </template>
                        <span v-else>—</span>
                    </dd>
                </div>
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-neutral-700/60">
                    <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                        {{ trans("Products") }}
                    </dt>
                    <dd class="mt-1 font-semibold text-slate-800 dark:text-neutral-100">
                        {{ wrapper.products_count }}
                    </dd>
                </div>
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-neutral-700/60">
                    <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                        {{ $t("Wrapper.created_at") }}
                    </dt>
                    <dd class="mt-1 font-semibold text-slate-800 dark:text-neutral-100">
                        {{ wrapper.created_at ?? "—" }}
                    </dd>
                </div>
            </dl>

            <div v-if="wrapper.description" class="mb-6">
                <h3 class="mb-2 text-lg font-semibold text-slate-800 dark:text-neutral-100">
                    {{ $t("Wrapper.description") }}
                </h3>
                <div
                    class="prose prose-sm max-w-none text-slate-600 dark:prose-invert dark:text-neutral-300"
                    v-html="wrapper.description"
                ></div>
            </div>
        </div>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6">
        <h2 class="mb-4 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
            {{ $t("Wrapper.selected_products") }}
        </h2>

        <div class="overflow-x-auto rounded-2xl">
            <table>
                <thead>
                    <tr>
                        <th scope="col">
                            {{ $t("Name") }}
                        </th>
                        <th scope="col">
                            {{ $t("Product.price") }}
                        </th>
                        <th scope="col">
                            {{ $t("Wrapper.display_order") }}
                        </th>
                        <th scope="col">
                            {{ $t("Wrapper.is_default") }}
                        </th>
                        <th scope="col">
                            {{ $t("Wrapper.free_shipping") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="variant in wrapper.variants"
                        :key="variant.id"
                        :class="{
                            'bg-slate-50 dark:bg-neutral-700/40': variant.is_default,
                        }"
                    >
                        <td>
                            <div class="flex items-center gap-3">
                                <img
                                    :src="ProductImagePlaceholder"
                                    :alt="variant.name"
                                    class="h-12 w-12 shrink-0 rounded-lg object-cover"
                                />
                                <Link
                                    :href="route('products.show', variant.id)"
                                    class="font-medium text-sky-700 hover:underline dark:text-sky-400"
                                >
                                    {{ variant.name }}
                                </Link>
                            </div>
                        </td>
                        <td>
                            {{ variant.price }}
                            {{ $t("Product.currency") }}
                        </td>
                        <td class="text-center">
                            {{ variant.display_order + 1 }}
                        </td>
                        <td class="text-center">
                            <i
                                v-if="variant.is_default"
                                class="ri-check-line text-xl text-green-600"
                            ></i>
                            <span v-else class="text-neutral-400">—</span>
                        </td>
                        <td class="text-center">
                            <i
                                v-if="variant.free_shipping"
                                class="ri-truck-line text-xl text-sky-700 dark:text-sky-400"
                            ></i>
                            <span v-else class="text-neutral-400">—</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
