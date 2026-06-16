<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
const props = defineProps(["product"]);
const toast = useToast();

const form = useForm({
    name: null,
    orderID: null,
    stars: 5,
    comment: null,
    productID: props.product.id,
});

const modalRef = ref(null);

function submitForm() {
    form.post(route("FE.reviews.store"), {
        preserveScroll: true,
        onSuccess: () => {
            let message = trans("Review.created_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("name");
            form.reset("comment");
            form.reset("stars");
            form.reset("phone");
        },
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        ref="modalRef"
        panel-classes="bg-white rounded mx-auto md:max-w-lg max-h-100 lg:max-h-125 overflow-y-auto"
    >
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Review.new") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mt-4">
                <p class="text-slate-600">
                    {{ $t("Create.review.notice") }}
                </p>
                <InputError
                    class="mt-2"
                    :message="form.errors['product.missing']"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors['product.reviewed']"
                />
            </div>

            <div class="mt-4">
                <InputLabel
                    class="text-slate-600"
                    for="orderID"
                    :value="$t('Order Number')"
                />

                <TextInput
                    class="mt-2 block w-full"
                    id="orderID"
                    type="text"
                    v-model="form['orderID']"
                    :required="true"
                    :placeholder="$t('Order Number')"
                />

                <InputError class="mt-2" :message="form.errors['orderID']" />
                <InputError
                    class="mt-2"
                    :message="form.errors['order.status']"
                />
            </div>

            <div class="mt-4">
                <InputLabel
                    class="text-slate-600"
                    for="name"
                    :value="$t('Name')"
                />

                <TextInput
                    class="mt-2 block w-full"
                    id="name"
                    type="text"
                    v-model="form['name']"
                    :required="true"
                    :placeholder="$t('Your name')"
                />

                <InputError class="mt-2" :message="form.errors['name']" />
            </div>
            <div class="mt-4">
                <InputLabel
                    class="text-slate-600"
                    for="stars"
                    :value="$t('Stars')"
                />

                <select
                    required
                    id="stars"
                    v-model="form.stars"
                    class="w-full mt-2 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                >
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>

                <InputError class="mt-2" :message="form.errors['stars']" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="comment"
                    :value="`${$t('Comment')} (${$t('optional')})`"
                    class="text-slate-600"
                />

                <textarea
                    id="comment"
                    class="mt-2 block w-full text-primary rounded-md border-editor shadow-sm focus:border-primary focus:ring-primary h-25 resize-none"
                    v-model="form.comment"
                    :placeholder="$t('comment_placeholder')"
                >
                </textarea>

                <InputError class="mt-2" :message="form.errors['comment']" />
            </div>
            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("Review.submit") }}
            </button>
        </form>
    </Modal>
</template>
