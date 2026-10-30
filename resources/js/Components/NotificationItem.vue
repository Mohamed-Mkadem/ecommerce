<script setup>
const props = defineProps({
    notification: Object,
});
</script>

<template>
    <Link v-if="notification.type == 'new-order'" as="button"
        class="relative w-full rounded-xl border border-slate-200 bg-white p-4 text-start shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-neutral-600 dark:hover:bg-neutral-700/60">
    <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
        <i :class="notification.data.icon"
            class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"></i>
        <div>
            <p class="text-base font-semibold text-slate-800 dark:text-neutral-100">
                {{ $t(notification.data.title) }}
                :
                {{ `${notification.data.amount} ${$t("currency")}` }}
            </p>
            <p class="text-sm text-slate-600 dark:text-neutral-300">
                {{ $t(notification.data.message) }}
            </p>

            <p class="text-xs text-slate-500 dark:text-neutral-400">
                {{ notification.data.created_at }}
            </p>
        </div>
    </div>
    <span v-show="notification.read_at == null"
        class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"></span>
    </Link>
    <Link v-else-if="notification.type == 'new-failed-order'" as="button"
        class="relative w-full rounded-xl border border-slate-200 bg-white p-4 text-start shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-neutral-600 dark:hover:bg-neutral-700/60">
    <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
        <i :class="notification.data.icon"
            class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"></i>
        <div>
            <p class="text-base font-semibold text-slate-800 dark:text-neutral-100">
                {{ $t(notification.data.title) }}
            </p>
            <p class="text-sm text-slate-600 dark:text-neutral-300">
                {{
                    `${$t(notification.data.message)} ${$t("ID")} : ${notification.data.order_id}`
                }}
            </p>

            <p class="text-xs text-slate-500 dark:text-neutral-400">
                {{ notification.data.created_at }}
            </p>
        </div>
    </div>
    <span v-show="notification.read_at == null"
        class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"></span>
    </Link>
    <Link v-else-if="notification.type == 'shipping-report'" as="button"
        class="relative w-full rounded-xl border border-slate-200 bg-white p-4 text-start shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-neutral-600 dark:hover:bg-neutral-700/60">
    <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
        <i :class="notification.data.icon"
            class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"></i>
        <div>
            <p class="text-base font-semibold text-slate-800 dark:text-neutral-100">
                {{ notification.data.name }}
            </p>
            <p class="text-sm text-slate-600 dark:text-neutral-300">
                {{ $t(notification.data.message) }}
            </p>

            <p class="text-xs text-slate-500 dark:text-neutral-400">
                {{ notification.data.created_at }}
            </p>
        </div>
    </div>
    <span v-show="notification.read_at == null"
        class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"></span>
    </Link>

    <Link v-else-if="notification.type == 'role-changed'" as="button"
        class="relative w-full rounded-xl border border-slate-200 bg-white p-4 text-start shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-neutral-600 dark:hover:bg-neutral-700/60">
    <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
        <i :class="notification.data.icon"
            class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"></i>
        <div>
            <p class="text-base font-semibold text-slate-800 dark:text-neutral-100">
                {{ $t(notification.data.title) }}
            </p>

            <p class="text-sm text-slate-600 dark:text-neutral-300">
                {{ $t(notification.data.message) }}
            </p>

            <p class="text-xs text-slate-500 dark:text-neutral-400">
                {{ notification.data.created_at }}
            </p>
        </div>
    </div>
    <span v-show="notification.read_at == null"
        class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"></span>
    </Link>
    <Link v-else-if="notification.type == 'selling-report'" as="button"
        class="relative w-full rounded-xl border border-slate-200 bg-white p-4 text-start shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-neutral-600 dark:hover:bg-neutral-700/60">
    <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
        <i :class="notification.data.icon"
            class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"></i>
        <div>
            <p class="text-base font-semibold text-slate-800 dark:text-neutral-100">
                {{ $t("Selling Report Created") }}
            </p>

            <p class="text-sm text-slate-600 dark:text-neutral-300">
                {{ $t(notification.data.name) }} :
                {{ $t(notification.data.message) }}
            </p>

            <p class="text-xs text-slate-500 dark:text-neutral-400">
                {{ notification.data.created_at }}
            </p>
        </div>
    </div>
    <span v-show="notification.read_at == null"
        class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"></span>
    </Link>
    <Link v-else-if="notification.type == 'order-report'" as="button"
        class="relative w-full rounded-xl border border-slate-200 bg-white p-4 text-start shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-neutral-600 dark:hover:bg-neutral-700/60">
    <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
        <i :class="notification.data.icon"
            class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"></i>
        <div>
            <p class="text-base font-semibold text-slate-800 dark:text-neutral-100">
                {{ notification.data.name }}
            </p>
            <p class="text-sm text-slate-600 dark:text-neutral-300">{{ $t(notification.data.message) }}</p>
            <p class="text-xs text-slate-500 dark:text-neutral-400">{{ notification.data.created_at }}</p>
        </div>
    </div>
    <span v-show="notification.read_at == null"
        class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"></span>
    </Link>
</template>
