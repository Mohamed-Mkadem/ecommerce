<script setup>
import DefaultAuthCard from "@/js/Components/Auths/DefaultAuthCard.vue";
</script>

<template>
    <Head :title="$t('user.banned')" />

    <DefaultAuthCard :title="$t('user.banned')" icon="ri-user-forbid-line">
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-900">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-xl text-rose-700">
                    <i class="ri-error-warning-line" aria-hidden="true"></i>
                </span>
                <p class="pt-1 text-sm leading-6">
                    {{ $t("user.banned.message") }}
                </p>
            </div>
        </div>

        <Link
            as="button"
            method="post"
            :href="route('logout')"
            class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-slate-200"
        >
            <i class="ri-logout-box-r-line" aria-hidden="true"></i>
            {{ $t("dropdown.logout") }}
        </Link>
    </DefaultAuthCard>
</template>
