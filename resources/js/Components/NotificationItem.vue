<script setup>
const props = defineProps({
    notification: Object,
});
</script>

<template>
    <Link
        v-if="notification.type == 'new-order'"
        as="button"
        class="gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 relative w-full text-start"
    >
        <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
            <i
                :class="notification.data.icon"
                class="bg-slate-500 w-12 h-12 rounded-full text-xl text-white flex items-center justify-center"
            ></i>
            <div>
                <p class="text-base text-primary font-medium">
                    {{ $t(notification.data.title) }}
                    :
                    {{ `${notification.data.amount} ${$t("currency")}` }}
                </p>
                <p class="text-sm">
                    {{ $t(notification.data.message) }}
                </p>

                <p class="text-xs">
                    {{ notification.data.created_at }}
                </p>
            </div>
        </div>
        <span
            v-show="notification.read_at == null"
            class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"
        ></span>
    </Link>
    <Link
        v-else-if="notification.type == 'new-failed-order'"
        as="button"
        class="gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 relative w-full text-start"
    >
        <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
            <i
                :class="notification.data.icon"
                class="bg-slate-500 w-12 h-12 rounded-full text-xl text-white flex items-center justify-center"
            ></i>
            <div>
                <p class="text-base text-primary font-medium">
                    {{ $t(notification.data.title) }}
                </p>
                <p class="text-sm">
                    {{
                        `${$t(notification.data.message)} ${$t("ID")} : ${notification.data.order_id}`
                    }}
                </p>

                <p class="text-xs">
                    {{ notification.data.created_at }}
                </p>
            </div>
        </div>
        <span
            v-show="notification.read_at == null"
            class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"
        ></span>
    </Link>
    <Link
        v-else-if="notification.type == 'shipping-report'"
        as="button"
        class="gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 relative w-full text-start"
    >
        <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
            <i
                :class="notification.data.icon"
                class="bg-slate-500 w-12 h-12 rounded-full text-xl text-white flex items-center justify-center"
            ></i>
            <div>
                <p class="text-base text-primary font-medium">
                    {{ notification.data.name }}
                </p>
                <p class="text-sm">
                    {{ $t(notification.data.message) }}
                </p>

                <p class="text-xs">
                    {{ notification.data.created_at }}
                </p>
            </div>
        </div>
        <span
            v-show="notification.read_at == null"
            class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"
        ></span>
    </Link>

    <Link
        v-else-if="notification.type == 'role-changed'"
        as="button"
        class="gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 relative w-full text-start"
    >
        <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
            <i
                :class="notification.data.icon"
                class="bg-slate-500 w-12 h-12 rounded-full text-xl text-white flex items-center justify-center"
            ></i>
            <div>
                <p class="text-base text-primary font-medium">
                    {{ $t(notification.data.title) }}
                </p>

                <p class="text-sm">
                    {{ $t(notification.data.message) }}
                </p>

                <p class="text-xs">
                    {{ notification.data.created_at }}
                </p>
            </div>
        </div>
        <span
            v-show="notification.read_at == null"
            class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"
        ></span>
    </Link>
    <Link
        v-else-if="notification.type == 'selling-report'"
        as="button"
        class="gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-gray-2 relative w-full text-start"
    >
        <div class="grid grid-cols-[50px,1fr] gap-2 items-center">
            <i
                :class="notification.data.icon"
                class="bg-slate-500 w-12 h-12 rounded-full text-xl text-white flex items-center justify-center"
            ></i>
            <div>
                <p class="text-base text-primary font-medium">
                    {{ $t("Selling Report Created") }}
                </p>

                <p class="text-sm">
                    {{ $t(notification.data.name) }} :
                    {{ $t(notification.data.message) }}
                </p>

                <p class="text-xs">
                    {{ notification.data.created_at }}
                </p>
            </div>
        </div>
        <span
            v-show="notification.read_at == null"
            class="w-2 h-2 bg-red-600 rounded-full absolute top-5 ltr:right-3 rtl:left-3"
        ></span>
    </Link>
</template>
