<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { ref, onUnmounted } from "vue";
import { trans } from "laravel-vue-i18n";
import ProductImagePlaceholder from "@/assets/images/product.webp";
const props = defineProps({
    form: { type: Object, required: true },
    availableProducts: { type: Array, required: true },
    isEditing: { type: Boolean, default: false },
    typeLabel: { type: Function, required: true },
});

const search = defineModel("search", { type: String, default: "" });
const imagesInput = ref(null);
const previews = ref([]);
const placeholderSvg = `
<svg xmlns='http://www.w3.org/2000/svg' width='80' height='80'>
    <rect width='100%' height='100%' fill='#e5e7eb'/>
    <text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='#9ca3af' font-size='14'>IMG</text>
</svg>`;
const placeholderSrc = `data:image/svg+xml;utf8,${encodeURIComponent(
    placeholderSvg,
)}`;

defineEmits([
    "submit",
    "add-product",
    "remove-product",
    "set-default",
    "move-product",
]);

function handleFilesChange(event) {
    const files = Array.from(event.target.files || []);
    // revoke previous previews
    previews.value.forEach((url) => URL.revokeObjectURL(url));
    previews.value = files.map((f) => URL.createObjectURL(f));
    props.form.images = files;
}

function removeImage(index) {
    props.form.images.splice(index, 1);
    if (previews.value[index]) {
        URL.revokeObjectURL(previews.value[index]);
    }
    previews.value.splice(index, 1);
    if (props.form.images.length === 0 && imagesInput.value) {
        imagesInput.value.value = null;
    }
}

function formatSize(bytes) {
    if (bytes === 0) return "0 B";
    const k = 1024;
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
}

function removeExistingImage(index) {
    const deletedImage = props.form.existing_media[index];
    props.form.deleted_media.push(deletedImage.id);
    props.form.existing_media.splice(index, 1);
}

function removeAllExistingImages() {
    props.form.existing_media.forEach((media) => {
        props.form.deleted_media.push(media.id);
    });
    props.form.existing_media = [];
}

onUnmounted(() => {
    previews.value.forEach((url) => URL.revokeObjectURL(url));
    previews.value = [];
});
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-8">
        <div class="bg-white px-4 py-8 rounded-md shadow-1">
            <h2 class="text-2xl mb-4 font-semibold text-graydark">
                {{ trans("Essential Information") }}
            </h2>

            <div class="mt-4 grid gap-6">
                <div>
                    <h3 class="font-semibold text-graydark">
                        {{ trans("English") }}
                    </h3>
                    <div class="mt-4">
                        <InputLabel
                            for="en-title"
                            :value="trans('Wrapper.title')"
                        />
                        <TextInput
                            id="en-title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.en.title"
                            :required="true"
                            :placeholder="trans('Wrapper.title_placeholder')"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors['en.title']"
                        />
                    </div>
                    <div class="mt-4">
                        <InputLabel :value="trans('Wrapper.description')" />
                        <textarea
                            id="en-description"
                            v-model="form.en.description"
                            class="mt-1 block w-full min-h-28 resize-y rounded-md border-editor focus:border-primary focus:ring-primary text-primary"
                            :placeholder="
                                trans('Wrapper.description_placeholder')
                            "
                        ></textarea>
                        <InputError
                            class="mt-2"
                            :message="form.errors['en.description']"
                        />
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-graydark">
                        {{ trans("French") }}
                    </h3>
                    <div class="mt-4">
                        <InputLabel
                            for="fr-title"
                            :value="trans('Wrapper.title')"
                        />
                        <TextInput
                            id="fr-title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.fr.title"
                            :placeholder="trans('Wrapper.title_placeholder')"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors['fr.title']"
                        />
                    </div>
                    <div class="mt-4">
                        <InputLabel :value="trans('Wrapper.description')" />
                        <textarea
                            id="fr-description"
                            v-model="form.fr.description"
                            class="mt-1 block w-full min-h-28 resize-y rounded-md border-editor focus:border-primary focus:ring-primary text-primary"
                            :placeholder="
                                trans('Wrapper.description_placeholder')
                            "
                        ></textarea>
                        <InputError
                            class="mt-2"
                            :message="form.errors['fr.description']"
                        />
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-graydark">
                        {{ trans("Arabic") }}
                    </h3>
                    <div class="mt-4">
                        <InputLabel
                            for="ar-title"
                            :value="trans('Wrapper.title')"
                        />
                        <TextInput
                            id="ar-title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.ar.title"
                            :placeholder="trans('Wrapper.title_placeholder')"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors['ar.title']"
                        />
                    </div>
                    <div class="mt-4">
                        <InputLabel :value="trans('Wrapper.description')" />
                        <textarea
                            id="ar-description"
                            v-model="form.ar.description"
                            class="mt-1 block w-full min-h-28 resize-y rounded-md border-editor focus:border-primary focus:ring-primary text-primary"
                            :placeholder="
                                trans('Wrapper.description_placeholder')
                            "
                        ></textarea>
                        <InputError
                            class="mt-2"
                            :message="form.errors['ar.description']"
                        />
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="caption" :value="trans('Wrapper.caption')" />

                <TextInput
                    id="caption"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.caption"
                    :placeholder="trans('Wrapper.caption_placeholder')"
                />

                <InputError class="mt-2" :message="form.errors.caption" />
            </div>

            <div class="mt-4 max-w-xs">
                <InputLabel for="is_active" :value="trans('Wrapper.status')" />

                <select
                    id="is_active"
                    v-model="form.is_active"
                    class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                    required
                >
                    <option :value="true">
                        {{ trans("Wrapper.active") }}
                    </option>
                    <option :value="false">
                        {{ trans("Wrapper.inactive") }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.is_active" />
            </div>

            <div class="mt-4">
                <InputLabel for="images-input" :value="trans('Media.title')" />

                <!-- Existing Images -->
                <div v-if="form.existing_media && form.existing_media.length" class="mt-2 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-graydark">
                            {{ trans("Existing Images") }}
                        </span>
                        <button
                            type="button"
                            @click="removeAllExistingImages"
                            class="text-sm font-medium text-red-600 hover:text-red-500 flex items-center gap-1"
                        >
                            <i class="ri-delete-bin-line"></i>
                            {{ trans("Remove All") }}
                        </button>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2 space-y-2">
                        <div
                            v-for="(media, index) in form.existing_media"
                            :key="media.id"
                            class="flex items-center justify-between gap-3 text-gray-800 bg-white rounded-md px-3 py-2"
                        >
                            <img
                                :src="media.url"
                                :alt="media.name"
                                class="w-14 h-14 rounded object-cover flex-shrink-0 me-4"
                            />

                            <div class="flex-1 min-w-0">
                                <div class="text-lg font-medium truncate">
                                    {{ media.name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ formatSize(media.size) }}
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="removeExistingImage(index)"
                                class="text-red-600 hover:text-red-500 ms-4 flex items-center gap-2"
                                :title="trans('Remove')"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <label
                    for="images-input"
                    class="bg-white text-gray-500 font-semibold text-base rounded w-full h-52 flex flex-col items-center justify-center cursor-pointer border-2 border-gray-300 border-dashed mx-auto hover:bg-gray"
                >
                    <i class="ri-upload-cloud-line text-3xl"></i>
                    {{ trans("Media.upload_images") }}

                    <input
                        ref="imagesInput"
                        type="file"
                        id="images-input"
                        class="hidden"
                        multiple
                        @change="handleFilesChange"
                    />
                </label>

                <p class="text-xs font-medium text-gray-400 mt-2">
                    {{ trans("Media.allowed_formats") }}
                </p>
                <InputError class="mt-2" :message="form.errors.images" />

                <div v-if="form.images && form.images.length" class="mt-3">
                    <div class="bg-gray-50 rounded-lg p-2 space-y-2">
                        <div
                            v-for="(file, index) in form.images"
                            :key="index"
                            class="flex items-center justify-between gap-3 text-gray-800 bg-white rounded-md px-3 py-2"
                        >
                            <img
                                :src="previews[index] || placeholderSrc"
                                :alt="file.name"
                                class="w-14 h-14 rounded object-cover flex-shrink-0 me-4"
                            />

                            <div class="flex-1 min-w-0">
                                <div class="text-lg font-medium truncate">
                                    {{ file.name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ formatSize(file.size) }}
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="removeImage(index)"
                                class="text-red-600 hover:text-red-500 ms-4 flex items-center gap-2"
                                :title="trans('Remove')"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white px-4 py-8 rounded-md shadow-1">
                <h2 class="text-2xl mb-4 font-semibold text-graydark">
                    {{ trans("Wrapper.add_products") }}
                </h2>

                <TextInput
                    id="search"
                    type="search"
                    class="mt-2 block w-full"
                    v-model="search"
                    :placeholder="trans('Search for products')"
                />

                <div class="mt-4 space-y-3 max-h-[480px] overflow-y-auto pr-1">
                    <div
                        v-if="!availableProducts.length"
                        class="text-center text-neutral-500 py-8"
                    >
                        {{ trans("Wrapper.no_products_available") }}
                    </div>

                    <div
                        v-for="product in availableProducts"
                        :key="product.id"
                        class="flex items-center gap-3 border border-neutral-200 rounded-lg p-3"
                    >
                        <img
                            :src="ProductImagePlaceholder"
                            :alt="product.name"
                            class="w-14 h-14 rounded-lg object-cover flex-shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-primary truncate">
                                {{ product.name }}
                            </p>
                            <p class="text-sm text-neutral-600">
                                {{ product.price }}
                                {{ $t("currency") }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="$emit('add-product', product)"
                            class="flex-shrink-0 rounded-lg border border-primary bg-primary px-3 py-2 text-sm font-medium text-white hover:bg-opacity-90"
                        >
                            {{ trans("Wrapper.add") }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white px-4 py-8 rounded-md shadow-1">
                <h2 class="text-2xl mb-4 font-semibold text-graydark">
                    {{ trans("Wrapper.selected_products") }}
                </h2>

                <InputError class="mb-4" :message="form.errors.products" />

                <div
                    v-if="!form.products.length"
                    class="text-center text-neutral-500 py-8"
                >
                    {{ trans("Wrapper.no_products_selected") }}
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="(item, index) in form.products"
                        :key="item.product_id"
                        class="border border-neutral-200 rounded-lg p-4"
                    >
                        <div class="flex items-start gap-3">
                            <img
                                :src="ProductImagePlaceholder"
                                :alt="item.name"
                                class="w-14 h-14 rounded-lg object-cover flex-shrink-0"
                            />
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-primary">
                                    {{ item.name }}
                                </p>
                                <p class="text-sm text-neutral-600">
                                    {{ typeLabel(item.type) }} ·
                                    {{ item.price }}
                                    {{ $t("currency") }}
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="$emit('remove-product', index)"
                                class="text-red-600 hover:text-red-500"
                                :title="trans('Remove')"
                            >
                                <i class="ri-delete-bin-line text-xl"></i>
                            </button>
                        </div>

                        <div
                            class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                        >
                            <label
                                class="flex items-center gap-2 cursor-pointer"
                            >
                                <input
                                    type="radio"
                                    name="default_variant"
                                    :checked="item.is_default"
                                    @change="$emit('set-default', index)"
                                    class="text-primary focus:ring-primary"
                                />
                                <span class="text-sm text-graydark">
                                    {{ trans("Wrapper.is_default") }}
                                </span>
                            </label>

                            <label
                                class="flex items-center gap-2 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    v-model="item.free_shipping"
                                    class="rounded text-primary focus:ring-primary"
                                />
                                <span class="text-sm text-graydark">
                                    {{ trans("Wrapper.free_shipping") }}
                                </span>
                            </label>

                            <div class="flex items-center gap-2">
                                <span class="text-sm text-graydark">
                                    {{ trans("Wrapper.display_order") }}:
                                </span>
                                <span class="font-semibold text-primary">
                                    {{ item.display_order + 1 }}
                                </span>
                                <div class="flex gap-1 ms-auto">
                                    <button
                                        type="button"
                                        :disabled="index === 0"
                                        @click="
                                            $emit('move-product', index, -1)
                                        "
                                        class="w-8 h-8 border border-neutral-200 rounded disabled:opacity-40"
                                    >
                                        ↑
                                    </button>
                                    <button
                                        type="button"
                                        :disabled="
                                            index === form.products.length - 1
                                        "
                                        @click="$emit('move-product', index, 1)"
                                        class="w-8 h-8 border border-neutral-200 rounded disabled:opacity-40"
                                    >
                                        ↓
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label
                                class="block text-sm font-medium text-graydark"
                            >
                                {{ trans("Wrapper.update_quantity") }}
                            </label>
                            <select
                                v-model.number="item.update_quantity"
                                class="mt-2 w-full rounded-md border-editor focus:border-primary focus:ring-primary text-primary"
                            >
                                <option :value="0.5">0.5</option>
                                <option :value="1">1</option>
                            </select>
                            <InputError
                                class="mt-2"
                                :message="
                                    form.errors[
                                        `products.${index}.update_quantity`
                                    ]
                                "
                            />
                        </div>

                        <InputError
                            class="mt-2"
                            :message="
                                form.errors[`products.${index}.product_id`]
                            "
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <slot name="actions">
                <button
                    type="submit"
                    :disabled="form.processing || !form.products.length"
                    :class="{
                        'opacity-25 cursor-not-allowed':
                            form.processing || !form.products.length,
                    }"
                    class="cursor-pointer rounded-lg border border-primary bg-primary px-6 py-3 font-medium text-white transition hover:bg-opacity-90"
                >
                    {{
                        isEditing
                            ? trans("Wrapper.save")
                            : trans("Wrapper.create")
                    }}
                </button>
            </slot>
        </div>
    </form>
</template>
