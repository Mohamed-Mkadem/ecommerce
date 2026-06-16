<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import Activities from "@/js/Components/Admin/Activities.vue";
const props = defineProps(["employee", "activities"]);

function deleteEmployee(employee) {
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
            router.delete(route("employees.destroy", employee), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Employee.deleted"),
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
    <Head :title="employee.data.full_name" />

    <PageHeader :page-title="employee.data.full_name">
        <div
            class="flex rtl:justify-end items-center gap-2"
            v-if="employee.data.id != $page.props.auth.user.id"
        >
            <ModalLink
                :close-button="false"
                :href="route('employees.edit', employee.data)"
                class="bg-slate-700 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
            >
                <i class="ri-edit-line"></i>
            </ModalLink>
            <button
                @click="deleteEmployee(employee.data)"
                class="bg-red-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
            >
                <i class="ri-delete-bin-5-fill"></i>
            </button>
        </div>
    </PageHeader>

    <div
        class="bg-white rounded-lg shadow-1 p-4 grid justify-center gap-8 md:grid-cols-[200px_1fr] md:gap-4 md:items-center"
    >
        <img
            :src="`${$page.props.base_url}storage/${employee.data.avatar}`"
            class="max-w-75 md:w-50 rounded-full"
        />
        <div class="text-center md:text-start">
            <p
                class="text-white px-3 py-1 font-medium capitalize rounded text-base w-max mx-auto mb-2 md:mx-0"
                :class="{
                    'bg-red-500': employee.data.status == 'banned',
                    'bg-green-600': employee.data.status == 'active',
                }"
            >
                {{ $t(`user.${employee.data.status}`) }}
            </p>
            <h2 class="text-2xl font-bold text-sky-800">
                {{ employee.data.full_name }}
            </h2>

            <div
                class="text-slate-600 flex items-center justify-center gap-1 md:justify-start md:text-xl text-lg"
            >
                <i class="ri-admin-fill"></i>
                <span>{{ $t(employee.data.role) }}</span>
            </div>
            <div
                class="text-slate-600 flex items-center justify-center gap-1 md:justify-start md:text-xl text-lg"
            >
                <i class="ri-mail-fill"></i>
                <span>{{ employee.data.email }}</span>
            </div>
            <div
                class="text-slate-600 flex items-center justify-center gap-1 md:justify-start md:text-xl text-lg"
            >
                <i class="ri-calendar-fill"></i>
                <span>{{ employee.data.created_at }}</span>
            </div>
        </div>
    </div>

    <Activities :activities="activities" />
</template>
