<script setup>
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";

defineProps({
    status: {
        type: String,
    },
});
</script>

<template>
    <Head :title="$t('profile.title')" />
    <PageHeader :page-title="$t('Profile')" />
    <div class="py-6">
        <div class="max-w-7xl space-y-5">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_8px_30px_rgba(15,23,42,0.06)] sm:p-8">
                <UpdateProfileInformationForm
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_8px_30px_rgba(15,23,42,0.06)] sm:p-8">
                <UpdatePasswordForm class="max-w-xl" />
            </div>
        </div>
    </div>
</template>
