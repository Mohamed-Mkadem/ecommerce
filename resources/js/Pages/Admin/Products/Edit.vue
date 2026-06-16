<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { usePage, router, useForm } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import Editor from "@/js/Components/Editor/Editor.vue";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import Swal from "sweetalert2";
import NotFound from "@/js/Components/NotFound.vue";
const props = defineProps(["product"]);
const product = usePage().props.product;
const media = product.media;
const toast = useToast();
const processing = ref(false);
const form = useForm({
    en: { name: "", description: "" },
    ar: { name: "", description: "" },
    fr: { name: "", description: "" },
    price: "",
    type: "product",
    status: "published",
    ends_at: null,
    shipping_name: "",
    discount_type: "percentage",
    discount: 0,
});

const mediaForm = useForm({
    images: [],
});
const isEndsAtDisabled = ref(true);

watch(
    () => form.type,
    (newType) => {
        isEndsAtDisabled.value = newType !== "pack";
    },
    { immediate: true },
);
function submitForm() {
    form.patch(route("products.update", product), {
        onSuccess: () => {
            router.get(route("products.edit", product));
            toast.success(trans("Product.updated"), getToastOptions());
        },
    });
}

function populateForm(product) {
    form.price = product.price / 1000 || "";
    form.type = product.type || "product";
    form.status = product.status || "published";
    form.ends_at = product.ends_at || null;
    form.shipping_name = product.shipping_name || "";
    form.discount_type = product.discount_type || "percentage";
    form.discount = product.discount || 0;
    product.translations.forEach((translation) => {
        if (form[translation.locale]) {
            form[translation.locale].name = translation.name || "";
            form[translation.locale].description =
                translation.description || "";
        }
    });
}
onMounted(() => {
    populateForm(product);
});

function deleteAllMedia() {
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
            processing.value = true;
            router.delete(route("products.deleteAllMedia", product), {
                onSuccess: () => {
                    toast.success(
                        trans("Media.all_deleted"),
                        getToastOptions(),
                    );
                    router.get(route("products.edit", product));
                },
                onFinish: () => {
                    processing.value = false;
                },
            });
        }
    });
}
function deleteMedia(media) {
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
            router.delete(route("products.deleteMedia", media), {
                onSuccess: () => {
                    toast.success(trans("Media.deleted"), getToastOptions());
                    router.get(route("products.edit", product));
                },
            });
        }
    });
}
function clearErrors() {
    const { errors } = usePage().props;

    Object.keys(errors).forEach((key) => {
        if (key.startsWith("images.")) {
            errors[key] = null;
        }
    });
}
function handleFilesChange(e) {
    clearErrors();

    let files = Array.from(e.target.files);
    mediaForm.images = files;
}

function removeImage(index) {
    const { errors } = usePage().props;

    errors[`images.${index}`] = null;

    mediaForm.images.splice(index, 1);
}

function truncateName(name) {
    const maxLength = 15;
    if (name.length > maxLength) {
        const extension = name.slice(name.lastIndexOf("."));
        return `${name.slice(0, maxLength)}...${extension}`;
    }
    return name;
}

function formatSize(size) {
    if (size < 1024) return `${size} B`;
    else if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`;
    return `${(size / 1024 / 1024).toFixed(1)} MB`;
}

function getImageError(index) {
    const errorKey = `images.${index}`;
    return usePage().props.errors[errorKey] || null;
}
function submitMedia() {
    mediaForm.post(route("products.media", product), {
        onSuccess: () => {
            router.get(route("products.edit", product));
            toast.success(trans("Media.uploaded"));
        },
    });
}
</script>

<template>
    <Head :title="trans('Edit Product') + ` - ${product.name}`" />

    <PageHeader :page-title="product.name"> </PageHeader>
    <form @submit.prevent="submitForm" class="space-y-8">
        <div class="bg-white px-4 py-8 rounded-md shadow-1">
            <h2 class="text-2xl mb-4 font-semibold text-graydark">
                {{ trans("Essential Information") }}
            </h2>

            <div class="mt">
                <div class="mt-4">
                    <InputLabel for="en-name" :value="trans('English.name')" />

                    <TextInput
                        class="mt-1 block w-full"
                        id="en-name"
                        type="text"
                        v-model="form.en['name']"
                        :required="true"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors['en.name']"
                    />
                </div>

                <div class="mt-4">
                    <InputLabel :value="trans('English.description')" />
                    <Editor
                        v-model="form.en.description"
                        :label="trans('English.description')"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors['en.description']"
                    />
                </div>
            </div>

            <div class="mt-4">
                <div class="mt-4">
                    <InputLabel for="fr-name" :value="trans('French.name')" />

                    <TextInput
                        id="fr-name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.fr.name"
                        :required="true"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors['fr.name']"
                    />
                </div>
                <div class="mt-4">
                    <InputLabel :value="trans('French.description')" />

                    <Editor
                        v-model="form.fr.description"
                        :label="trans('French.description')"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors['fr.description']"
                    />
                </div>
            </div>

            <div class="mt-4">
                <div class="mt-4">
                    <InputLabel for="ar-name" :value="trans('Arabic.name')" />

                    <TextInput
                        id="ar-name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.ar['name']"
                        :required="true"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors['ar.name']"
                    />
                </div>
                <div class="mt-4">
                    <InputLabel :value="trans('Arabic.description')" />

                    <Editor
                        v-model="form.ar.description"
                        :label="trans('Arabic.description')"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors['ar.description']"
                    />
                </div>
            </div>

            <div class="mt-4">
                <InputLabel
                    for="shipping_name"
                    :value="trans('shipping.name')"
                />

                <TextInput
                    id="shipping_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.shipping_name"
                    :required="true"
                    :placeholder="trans('shipping_name_placeholder')"
                />

                <InputError
                    class="mt-2"
                    :message="$page.props.errors['shipping_name']"
                />
            </div>
        </div>

        <div class="bg-white px-4 py-8 rounded-md shadow-1">
            <h2 class="text-2xl mb-4 font-semibold text-graydark">
                {{ trans("Additional Information") }}
            </h2>

            <div class="flex sm:gap-4 flex-col sm:flex-row">
                <div class="mt-4 w-full">
                    <InputLabel
                        for="product-status"
                        :value="trans('Product.status')"
                    />

                    <select
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                        v-model="form.status"
                        id="product-status"
                        required
                    >
                        <option value="published">
                            {{ $t("Product.published") }}
                        </option>
                        <option value="hidden">
                            {{ trans("Product.hidden") }}
                        </option>
                    </select>

                    <InputError class="mt-2" :message="form.errors.status" />
                </div>

                <div class="mt-4 w-full">
                    <InputLabel
                        for="product-type"
                        :value="trans('Product.type')"
                    />

                    <select
                        required
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                        v-model="form.type"
                        id="product-type"
                    >
                        <option value="product">
                            {{ trans("Product.product") }}
                        </option>
                        <option value="pack">
                            {{ trans("Product.pack") }}
                        </option>
                    </select>

                    <InputError class="mt-2" :message="form.errors.type" />
                </div>
            </div>

            <div class="flex sm:gap-4 flex-col sm:flex-row mb-4">
                <div class="mt-4 w-full">
                    <InputLabel
                        for="product-price"
                        :value="trans('Product.price')"
                    />

                    <TextInput
                        id="product-price"
                        type="number"
                        class="mt-1 block w-full"
                        v-model="form.price"
                        placeholder="eg: 50000"
                        :required="true"
                        step="0.01"
                    />

                    <InputError class="mt-2" :message="form.errors.price" />
                </div>
                <div class="mt-4 w-full">
                    <div class="flex items-center gap-2">
                        <InputLabel :value="trans('Pack.validity')" />
                        <small
                            class="text-sm"
                            v-if="form.ends_at && form.type == 'pack'"
                            >{{ ` - (${form.ends_at})` }}</small
                        >
                    </div>

                    <DatePicker
                        v-model="form.ends_at"
                        :disabled="isEndsAtDisabled"
                    />

                    <InputError class="mt-2" :message="form.errors.ends_at" />
                </div>
            </div>

            <div class="flex sm:gap-4 flex-col sm:flex-row">
                <div class="mt-4 w-full">
                    <InputLabel
                        for="discount-type"
                        :value="trans('Discount.type')"
                    />

                    <select
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                        v-model="form.discount_type"
                        id="discount-type"
                        required
                    >
                        <option value="percentage">
                            {{ $t("Percentage") }}
                        </option>
                        <option value="fixed">
                            {{ trans("Fixed") }}
                        </option>
                    </select>

                    <InputError
                        class="mt-2"
                        :message="$page.props.errors.discount_type"
                    />
                </div>

                <div class="mt-4 w-full">
                    <InputLabel
                        for="discount"
                        :value="trans('Discount.value')"
                    />

                    <TextInput
                        id="discount"
                        type="number"
                        class="mt-1 block w-full"
                        v-model="form.discount"
                        :placeholder="$t('Discount.value_placeholder')"
                        :required="false"
                        step="0.01"
                    />

                    <InputError
                        class="mt-2"
                        :message="$page.props.errors.discount"
                    />
                </div>
            </div>
            <input
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                :value="trans('form.save_changes')"
                class="w-full sm:w-1/4 cursor-pointer rounded-lg border border-primary bg-primary py-2 px-4 font-medium text-white transition hover:bg-opacity-90 mt-4"
            />
        </div>
    </form>

    <div class="bg-white px-4 py-8 rounded-md shadow-1 mt-8">
        <h2 class="text-2xl mb-4 font-semibold text-graydark">
            {{ trans("Media.title") }}
        </h2>

        <div class="flex items-center gap-4 justify-between flex-wrap">
            <h3 class="text-slate-500 font-semibold text-xl">
                {{ $t("Media.current_media") }}
            </h3>

            <button
                :disabled="processing"
                :class="{
                    'bg-opacity-50 cursor-not-allowed': processing,
                }"
                v-if="media.length >= 2"
                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                @click="deleteAllMedia"
            >
                {{ $t("Media.delete_all") }}
            </button>
        </div>

        <div
            v-if="media.length"
            class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4"
        >
            <div
                v-for="image in media"
                :key="image.id"
                class="relative group border border-gray-200 rounded overflow-hidden"
            >
                <!-- Image -->
                <img
                    :src="image.original_url"
                    :alt="image.name"
                    class="w-full h-32 object-cover"
                />

                <!-- Delete Button -->
                <button
                    class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white p-1 rounded-sm opacity-0 group-hover:opacity-100 transition"
                    @click="deleteMedia(image)"
                >
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
        <div v-else>
            <NotFound :message="$t('NotFound.media')" />
        </div>
        <h3 class="text-slate-500 font-semibold mt-6 text-xl">
            {{ $t("Media.add_media") }}
        </h3>
        <form @submit.prevent="submitMedia">
            <div class="mt-4">
                <label
                    for="images-input"
                    class="bg-white text-gray-500 font-semibold text-base rounded w-full h-52 flex flex-col items-center justify-center cursor-pointer border-2 border-gray-300 border-dashed mx-auto f hover:bg-gray"
                >
                    <i class="ri-upload-cloud-line text-3xl"></i>
                    {{ trans("Media.upload_images") }}

                    <input
                        type="file"
                        id="images-input"
                        class="hidden"
                        multiple
                        @input="handleFilesChange"
                    />
                    <p class="text-xs font-medium text-gray-400 mt-2">
                        {{ trans("Media.allowed_formats") }}
                    </p>
                    <p class="text-xs font-medium text-gray-400 mt-1">
                        {{ trans("Media.image_dimensions") }}
                    </p>
                </label>
                <InputError
                    class="mt-2"
                    :message="mediaForm.errors['images']"
                />
            </div>

            <div v-if="mediaForm.images" class="mt-8">
                <div v-for="(image, index) in mediaForm.images" :key="index">
                    <div
                        class="flex justify-between items-center mt-3 bg-gray rounded-md p-4"
                    >
                        <div class="flex gap-2 items-center">
                            <i
                                class="ri-image-line text-5xl text-slate-300"
                            ></i>
                            <div class="text-slate-800">
                                <p>{{ truncateName(image.name) }}</p>
                                <p class="text-sm">
                                    {{ formatSize(image.size) }} ·
                                    {{ image.type }}
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="removeImage(index)"
                            class="bg-red-500 rounded-md px-4 py-2 text-white hover:bg-opacity-75"
                        >
                            <i class="ri-delete-bin-fill"></i>
                        </button>
                    </div>
                    <InputError class="mt-2" :message="getImageError(index)" />
                </div>
            </div>

            <input
                :class="{
                    'opacity-25 cursor-not-allowed': mediaForm.processing,
                }"
                :disabled="mediaForm.processing"
                type="Submit"
                :value="trans('Media.upload_images')"
                class="w-full sm:w-1/4 cursor-pointer rounded-lg border border-primary bg-primary py-2 px-4 font-medium text-white transition hover:bg-opacity-90 mt-4"
            />
        </form>
    </div>
</template>
