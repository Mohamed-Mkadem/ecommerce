<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
import { router } from "@inertiajs/vue3";
const props = defineProps(["employees"]);

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
                class="bg-red-700 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
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

    <div v-if="employees.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(300px,_100%),_1fr))] gap-4"
        >
            <div
                v-for="employee in employees.data"
                :key="employee.id"
                class="bg-white rounded shadow-1 p-4"
            >
                <div class="flex items-center justify-between gap-2">
                    <p
                        class="text-white px-4 py-1 font-medium capitalize rounded"
                        :class="{
                            'bg-red-500': employee.status == 'banned',
                            'bg-green-600': employee.status == 'active',
                        }"
                    >
                        {{ $t(`user.${employee.status}`) }}
                    </p>
                </div>

                <div class="pt-8 flex flex-col justify-center items-center">
                    <img
                        :src="`${$page.props.base_url}storage/${employee.avatar}`"
                        class="w-25 mx rounded-full mb-3"
                    />
                    <Link
                        class="font-semibold text-sky-700 text-xl underline hover:text-sky-800"
                        :href="route('employees.show', employee)"
                    >
                        {{ employee.full_name }}
                    </Link>
                    <p class="text-primary">
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
        />
    </div>
    <NotFound v-else />
</template>
