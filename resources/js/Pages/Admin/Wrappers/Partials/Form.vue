<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { trans } from "laravel-vue-i18n";

defineProps({
    form: { type: Object, required: true },
    availableProducts: { type: Array, required: true },
    isEditing: { type: Boolean, default: false },
    typeLabel: { type: Function, required: true },
});

const search = defineModel("search", { type: String, default: "" });

defineEmits([
    "submit",
    "add-product",
    "remove-product",
    "set-default",
    "move-product",
]);
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-8">
        <div class="bg-white px-4 py-8 rounded-md shadow-1">
            <h2 class="text-2xl mb-4 font-semibold text-graydark">
                {{ trans("Essential Information") }}
            </h2>

            <div class="mt-4">
                <InputLabel for="title" :value="trans('Wrapper.title')" />

                <TextInput
                    id="title"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.title"
                    :required="true"
                    :placeholder="trans('Wrapper.title_placeholder')"
                />

                <InputError class="mt-2" :message="form.errors.title" />
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

            <div class="mt-4">
                <InputLabel
                    for="description"
                    :value="trans('Wrapper.description')"
                />

                <textarea
                    id="description"
                    v-model="form.description"
                    class="mt-1 block w-full min-h-28 resize-y rounded-md border-editor focus:border-primary focus:ring-primary text-primary"
                    :placeholder="trans('Wrapper.description_placeholder')"
                ></textarea>

                <InputError class="mt-2" :message="form.errors.description" />
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
                            :src="product.main_image_url"
                            :alt="product.name"
                            class="w-14 h-14 rounded-lg object-cover flex-shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-primary truncate">
                                {{ product.name }}
                            </p>
                            <p class="text-sm text-neutral-600">
                                {{ typeLabel(product.type) }} ·
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
                                :src="item.main_image_url"
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
