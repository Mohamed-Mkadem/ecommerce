<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
const props = defineProps(["employees", "filters"]);

const initialFormValues = {
    search: "",
    role: "",
    status: "",
};
const form = useForm({ ...initialFormValues });

function submitForm() {
    form.get(route("employees.index"), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetForm() {
    Object.assign(form, initialFormValues);
    form.clearErrors();
    submitForm();
}

onMounted(() => {
    Object.assign(form, props.filters || {});
});

function cleanLogs() {
    Swal.fire({
        title: trans("Dialog.title"),
        text: trans("Dialog.warning"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: trans("Dialog.confirmCleaningLogButtonText"),
        cancelButtonText: trans("Dialog.cancelButtonText"),
        width: 450,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("activitylog.clean"), {
                onFinish: (visit) => {
                    Swal.fire({
                        title: trans("Dialog.logsCleanedTitle"),
                        text: trans("Logs.cleaned"),
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
    <Head :title="$t('Employees')" />

    <PageHeader :page-title="$t('Employees')">
        <div class="flex items-center gap-2 flex-wrap">
            <button
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-600 bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-red-700 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-800"
                @click="cleanLogs"
            >
                {{ $t("logs.clean") }}
            </button>

            <CreateNewModal
                :label="$t('Employee.new')"
                href="employees.create"
                :close-button="false"
            >
            </CreateNewModal>
        </div>
    </PageHeader>

    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
        <div class="mb-6 flex items-start gap-3 border-b border-slate-100 pb-4 dark:border-neutral-700">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200">
                <i class="ri-filter-3-line" aria-hidden="true"></i>
            </span>
            <h2 class="pt-1.5 text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                {{ $t("Filter.title") }}
            </h2>
        </div>

        <form @submit.prevent="submitForm" @reset.prevent="resetForm" class="space-y-5">
            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <InputLabel for="employee-search" :value="$t('Filter.search_by_client_name')" class="text-slate-700 dark:text-neutral-200" />
                    <TextInput variant="admin" id="employee-search" type="search" class="mt-1 block w-full" v-model="form.search" :placeholder="$t('Filter.search_by_client_name_placeholder')" />
                </div>
                <div>
                    <InputLabel for="employee-role" :value="$t('role')" class="text-slate-700 dark:text-neutral-200" />
                    <SelectInput variant="admin" id="employee-role" v-model="form.role">
                        <option value="">{{ $t("All") }}</option>
                        <option value="admin">{{ $t("admin") }}</option>
                        <option value="moderator">{{ $t("moderator") }}</option>
                    </SelectInput>
                </div>
                <div>
                    <InputLabel for="employee-status" :value="$t('Status')" class="text-slate-700 dark:text-neutral-200" />
                    <SelectInput variant="admin" id="employee-status" v-model="form.status">
                        <option value="">{{ $t("All") }}</option>
                        <option value="active">{{ $t("user.active") }}</option>
                        <option value="banned">{{ $t("user.banned") }}</option>
                    </SelectInput>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row dark:border-neutral-700">
                <button :disabled="form.processing" type="submit" class="w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-5 py-2.5 font-medium text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-opacity-90 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40 disabled:cursor-not-allowed disabled:opacity-60 sm:w-1/4">
                    {{ $t("Filter.value") }}
                </button>
                <button :disabled="form.processing" type="reset" class="w-full cursor-pointer rounded-xl border border-neutral-300 bg-neutral-100 px-5 py-2.5 font-medium text-neutral-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-neutral-200 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400/50 disabled:cursor-not-allowed disabled:opacity-60 sm:w-1/4 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
                    {{ $t("form.reset") }}
                </button>
            </div>
        </form>
    </section>

    <ResultsCounter :count="employees.meta.total" icon="ri-team-line" />

    <div v-if="employees.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(280px,_100%),_1fr))] gap-4"
        >
            <div
                v-for="employee in employees.data"
                :key="employee.id"
                class="group overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/30"
            >
                <div class="flex items-center justify-between gap-2">
                    <p
                        class="rounded-lg px-3 py-1.5 text-sm font-semibold capitalize text-white"
                        :class="{
                            'bg-red-500': employee.status == 'banned',
                            'bg-green-600': employee.status == 'active',
                        }"
                    >
                        {{ $t(`user.${employee.status}`) }}
                    </p>
                </div>

                <div class="flex flex-col items-center justify-center pt-5">
                    <img
                        :src="`${$page.props.base_url}storage/${employee.avatar}`"
                        :alt="employee.full_name"
                        class="mb-3 h-24 w-24 rounded-full object-cover ring-4 ring-slate-100 transition group-hover:ring-slate-200 dark:ring-neutral-700 dark:group-hover:ring-neutral-600"
                    />
                    <Link
                        class="text-center text-lg font-semibold text-slate-800 transition-colors hover:text-primary hover:underline dark:text-neutral-100 dark:hover:text-white"
                        :href="route('employees.show', employee)"
                    >
                        {{ employee.full_name }}
                    </Link>
                    <p
                        class="mt-1 text-sm font-medium text-slate-500 dark:text-neutral-400"
                    >
                        {{ $t(employee.role) }}
                    </p>
                </div>
            </div>
        </div>

        <Paginator
            class="mt-8"
            :links="employees.meta.links"
            :previous="employees.links.prev"
            :next="employees.links.next"
            variant="admin"
        />
    </div>
    <NotFound v-else />
</template>
