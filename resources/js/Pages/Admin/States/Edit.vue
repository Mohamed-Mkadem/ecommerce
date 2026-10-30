<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";

import { trans } from "laravel-vue-i18n";
import { useAppearanceStore } from "@/js/stores/appearance";
const props = defineProps({
    state: {
        required: true,
        type: Object,
    },
    shippers: {
        required: true,
        type: Array,
    },
});
const toast = useToast();
const appearanceStore = useAppearanceStore();

const form = useForm({
    price: props.state.shipping_cost / 1000,
    default_shipper_id: props.state.default_shipper_id || null,
});
import { ref } from "vue";

const modalRef = ref(null);

function submitForm() {
    form.put(route("states.update", props.state), {
        onSuccess: () => {
            let message = trans("ShippingCost.updated_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("price");
        },
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        max-width="md"
        ref="modalRef"
        position="center"
        padding-classes="p-0"
        :panel-classes="
            appearanceStore.isDark
                ? 'dark h-[355px] max-h-[calc(100vh-2rem)] overflow-hidden rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl'
                : 'h-[355px] max-h-[calc(100vh-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl'
        "
    >
        <div class="h-full overflow-y-auto p-5 sm:p-6">
            <div
                class="mb-5 flex items-center justify-between gap-4 border-b border-slate-100 pb-4 dark:border-neutral-700"
            >
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"
                        ><i class="ri-map-pin-line" aria-hidden="true"></i
                    ></span>
                    <h1
                        class="text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
                    >
                        {{ $t("State.edit") }}
                    </h1>
                </div>
                <button
                    type="button"
                    :aria-label="$t('Close')"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white"
                    @click="close"
                >
                    <i class="ri-close-large-line text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <InputLabel
                        class="text-slate-700 dark:text-neutral-200"
                        for="price"
                        :value="$t('Shipping Cost : (in DT)')"
                    />
                    <TextInput
                        variant="admin"
                        id="price"
                        type="number"
                        v-model="form.price"
                        :required="true"
                        :placeholder="$t('Shipping.cost')"
                        step="0.5"
                    />
                    <InputError class="mt-2" :message="form.errors.price" />
                </div>
                <div>
                    <InputLabel
                        class="text-slate-700 dark:text-neutral-200"
                        for="default_shipper_id"
                        :value="$t('Default Shipper')"
                    />
                    <SelectInput
                        variant="admin"
                        id="default_shipper_id"
                        v-model="form.default_shipper_id"
                    >
                        <option :value="null">-- Select a shipper --</option>
                        <option
                            v-for="shipper in shippers"
                            :key="shipper.id"
                            :value="shipper.id"
                        >
                            {{ shipper.name }}
                        </option>
                    </SelectInput>
                    <InputError
                        class="mt-2"
                        :message="form.errors.default_shipper_id"
                    />
                </div>
                <button
                    :class="{
                        'opacity-60 !cursor-not-allowed': form.processing,
                    }"
                    :disabled="form.processing"
                    type="submit"
                    class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white transition hover:border-teal-800 hover:bg-teal-800"
                >
                    <i class="ri-save-3-line" aria-hidden="true"></i>
                    {{ $t("form.save") }}
                </button>
            </form>
        </div>
    </Modal>
</template>
