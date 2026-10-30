<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import { ref, onUnmounted } from "vue";
import { trans } from "laravel-vue-i18n";
import ProductImagePlaceholder from "@/assets/images/product.webp";
const props = defineProps({
    form: { type: Object, required: true },
    availableProducts: { type: Array, required: true },
    localizedProductName: { type: Function, required: true },
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
    <form @submit.prevent="$emit('submit')" class="space-y-6">
        <section
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6"
        >
            <div
                class="mb-6 flex items-center gap-3 border-b border-slate-100 pb-4 dark:border-neutral-700"
            >
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200"
                >
                    <i class="ri-translate-2" aria-hidden="true"></i>
                </span>
                <h2
                    class="text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
                >
                    {{ trans("Essential Information") }}
                </h2>
            </div>

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <div
                    class="rounded-xl border border-slate-100 p-4 dark:border-neutral-700"
                >
                    <h3
                        class="font-semibold text-slate-800 dark:text-neutral-100"
                    >
                        {{ trans("English") }}
                    </h3>
                    <div class="mt-4">
                        <InputLabel
                            for="en-title"
                            :value="trans('Wrapper.title')"
                            class="text-slate-700 dark:text-neutral-200"
                        />
                        <TextInput
                            variant="admin"
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
                        <InputLabel
                            :value="trans('Wrapper.description')"
                            class="text-slate-700 dark:text-neutral-200"
                        />
                        <textarea
                            id="en-description"
                            v-model="form.en.description"
                            class="wrapper-form-control mt-1 block h-40 w-full resize-none overflow-y-auto"
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

                <div
                    class="rounded-xl border border-slate-100 p-4 dark:border-neutral-700"
                >
                    <h3
                        class="font-semibold text-slate-800 dark:text-neutral-100"
                    >
                        {{ trans("French") }}
                    </h3>
                    <div class="mt-4">
                        <InputLabel
                            for="fr-title"
                            :value="trans('Wrapper.title')"
                            class="text-slate-700 dark:text-neutral-200"
                        />
                        <TextInput
                            variant="admin"
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
                        <InputLabel
                            :value="trans('Wrapper.description')"
                            class="text-slate-700 dark:text-neutral-200"
                        />
                        <textarea
                            id="fr-description"
                            v-model="form.fr.description"
                            class="wrapper-form-control mt-1 block h-40 w-full resize-none overflow-y-auto"
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

                <div
                    class="rounded-xl border border-slate-100 p-4 dark:border-neutral-700"
                >
                    <h3
                        class="font-semibold text-slate-800 dark:text-neutral-100"
                    >
                        {{ trans("Arabic") }}
                    </h3>
                    <div class="mt-4">
                        <InputLabel
                            for="ar-title"
                            :value="trans('Wrapper.title')"
                            class="text-slate-700 dark:text-neutral-200"
                        />
                        <TextInput
                            variant="admin"
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
                        <InputLabel
                            :value="trans('Wrapper.description')"
                            class="text-slate-700 dark:text-neutral-200"
                        />
                        <textarea
                            id="ar-description"
                            v-model="form.ar.description"
                            class="wrapper-form-control mt-1 block h-40 w-full resize-none overflow-y-auto"
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

            <div class="mt-5 max-w-sm">
                <InputLabel
                    for="is_active"
                    :value="trans('Wrapper.status')"
                    class="text-slate-700 dark:text-neutral-200"
                />

                <SelectInput
                    variant="admin"
                    id="is_active"
                    v-model="form.is_active"
                    required
                >
                    <option :value="true">
                        {{ trans("Wrapper.active") }}
                    </option>
                    <option :value="false">
                        {{ trans("Wrapper.inactive") }}
                    </option>
                </SelectInput>

                <InputError class="mt-2" :message="form.errors.is_active" />
            </div>

            <div
                class="mt-6 border-t border-slate-100 pt-5 dark:border-neutral-700"
            >
                <InputLabel
                    for="images-input"
                    :value="trans('Media.title')"
                    class="text-slate-700 dark:text-neutral-200"
                />

                <!-- Existing Images -->
                <div
                    v-if="form.existing_media && form.existing_media.length"
                    class="mt-2 mb-4"
                >
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="text-sm font-semibold text-slate-700 dark:text-neutral-200"
                        >
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
                    <div
                        class="space-y-2 rounded-xl bg-slate-50 p-3 dark:bg-neutral-900/50"
                    >
                        <div
                            v-for="(media, index) in form.existing_media"
                            :key="media.id"
                            class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-slate-800 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
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
                                <div
                                    class="text-sm text-slate-500 dark:text-neutral-400"
                                >
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
                    class="flex h-52 w-full cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 text-base font-semibold text-slate-600 transition hover:border-teal-700 hover:bg-slate-100 dark:border-neutral-600 dark:bg-neutral-900/40 dark:text-neutral-300 dark:hover:border-teal-500 dark:hover:bg-neutral-700/60"
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

                <p
                    class="mt-2 text-xs font-medium text-slate-500 dark:text-neutral-400"
                >
                    {{ trans("Media.allowed_formats") }}
                </p>
                <InputError class="mt-2" :message="form.errors.images" />

                <div v-if="form.images && form.images.length" class="mt-3">
                    <div
                        class="space-y-2 rounded-xl bg-slate-50 p-3 dark:bg-neutral-900/50"
                    >
                        <div
                            v-for="(file, index) in form.images"
                            :key="index"
                            class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-slate-800 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
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
                                <div
                                    class="text-sm text-slate-500 dark:text-neutral-400"
                                >
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
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            <section
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6"
            >
                <h2
                    class="mb-4 text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
                >
                    {{ trans("Wrapper.add_products") }}
                </h2>

                <TextInput
                    variant="admin"
                    id="search"
                    type="search"
                    class="mt-2 block w-full"
                    v-model="search"
                    :placeholder="trans('Search for products')"
                />

                <div class="mt-4 space-y-3 max-h-[480px] overflow-y-auto pr-1">
                    <div
                        v-if="!availableProducts.length"
                        class="py-8 text-center text-slate-500 dark:text-neutral-400"
                    >
                        {{ trans("Wrapper.no_products_available") }}
                    </div>

                    <div
                        v-for="product in availableProducts"
                        :key="product.id"
                        class="flex items-center gap-3 rounded-xl border border-slate-200 p-3 dark:border-neutral-700"
                    >
                        <img
                            :src="ProductImagePlaceholder"
                            :alt="localizedProductName(product)"
                            class="w-14 h-14 rounded-lg object-cover flex-shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <p
                                class="truncate font-semibold text-slate-800 dark:text-neutral-100"
                            >
                                {{ localizedProductName(product) }}
                            </p>
                            <p
                                class="text-sm text-slate-500 dark:text-neutral-400"
                            >
                                {{ product.price }}
                                {{ $t("currency") }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="$emit('add-product', product)"
                            class="shrink-0 rounded-lg bg-teal-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40"
                        >
                            {{ trans("Wrapper.add") }}
                        </button>
                    </div>
                </div>
            </section>

            <section
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6"
            >
                <h2
                    class="mb-4 text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
                >
                    {{ trans("Wrapper.selected_products") }}
                </h2>

                <InputError class="mb-4" :message="form.errors.products" />

                <div
                    v-if="!form.products.length"
                    class="py-8 text-center text-slate-500 dark:text-neutral-400"
                >
                    {{ trans("Wrapper.no_products_selected") }}
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="(item, index) in form.products"
                        :key="item.product_id"
                        class="rounded-xl border border-slate-200 p-4 dark:border-neutral-700"
                    >
                        <div class="flex items-start gap-3">
                            <img
                                :src="ProductImagePlaceholder"
                                :alt="item.name"
                                class="w-14 h-14 rounded-lg object-cover flex-shrink-0"
                            />
                            <div class="flex-1 min-w-0">
                                <p
                                    class="font-semibold text-slate-800 dark:text-neutral-100"
                                >
                                    {{ localizedProductName(item) }}
                                </p>
                                <p
                                    class="text-sm text-slate-500 dark:text-neutral-400"
                                >
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
                                    class="accent-teal-700 focus:ring-teal-700"
                                />
                                <span
                                    class="text-sm text-slate-600 dark:text-neutral-300"
                                >
                                    {{ trans("Wrapper.is_default") }}
                                </span>
                            </label>

                            <label
                                class="flex items-center gap-2 cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    v-model="item.free_shipping"
                                    class="rounded accent-teal-700 focus:ring-teal-700"
                                />
                                <span
                                    class="text-sm text-slate-600 dark:text-neutral-300"
                                >
                                    {{ trans("Wrapper.free_shipping") }}
                                </span>
                            </label>

                            <div class="flex items-center gap-2">
                                <span
                                    class="text-sm text-slate-600 dark:text-neutral-300"
                                >
                                    {{ trans("Wrapper.display_order") }}:
                                </span>
                                <span
                                    class="font-semibold text-slate-800 dark:text-neutral-100"
                                >
                                    {{ item.display_order + 1 }}
                                </span>
                                <div class="flex gap-1 ms-auto">
                                    <button
                                        type="button"
                                        :disabled="index === 0"
                                        @click="
                                            $emit('move-product', index, -1)
                                        "
                                        class="h-8 w-8 rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-100 disabled:opacity-40 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-700"
                                    >
                                        ↑
                                    </button>
                                    <button
                                        type="button"
                                        :disabled="
                                            index === form.products.length - 1
                                        "
                                        @click="$emit('move-product', index, 1)"
                                        class="h-8 w-8 rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-100 disabled:opacity-40 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-700"
                                    >
                                        ↓
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label
                                class="block text-sm font-medium text-slate-700 dark:text-neutral-200"
                            >
                                {{ trans("Wrapper.update_quantity") }}
                            </label>
                            <SelectInput
                                variant="admin"
                                v-model.number="item.update_quantity"
                                class="mt-2"
                            >
                                <option :value="0.5">0.5</option>
                                <option :value="1">1</option>
                            </SelectInput>
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
            </section>
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
                    class="cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-6 py-3 font-semibold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-teal-800 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40 disabled:cursor-not-allowed disabled:opacity-60"
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

<style>
.wrapper-form-control {
    border: 1px solid #cbd5e1 !important;
    border-radius: 0.75rem;
    background-color: rgb(248 250 252 / 0.5);
    padding: 0.625rem 0.75rem;
    color: #262626;
    box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);
}

html.dark .wrapper-form-control {
    border-color: #525252 !important;
    background-color: #404040 !important;
    color: #f5f5f5;
}

.wrapper-form-control:focus {
    border-color: #94a3b8 !important;
    outline: none;
    box-shadow: 0 0 0 2px rgb(148 163 184 / 0.2) !important;
}

html.dark .wrapper-form-control:focus {
    border-color: #737373 !important;
    box-shadow: 0 0 0 2px rgb(163 163 163 / 0.2) !important;
}

html.dark textarea.wrapper-form-control::placeholder {
    color: #a3a3a3;
}
</style>
