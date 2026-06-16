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
        <div class="max-w-7xl space-y-3">
            <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                <UpdateProfileInformationForm
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                <UpdatePasswordForm class="max-w-xl" />
            </div>
        </div>
    </div>
</template>
