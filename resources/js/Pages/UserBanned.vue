<script setup></script>

<template>
    <Head :title="$t('user.banned')" />
    <div
        class="w-full min-h-screen flex items-center justify-center p-4 bg-whiten"
    >
        <div class="bg-white rounded-md p-4 text-neutral-500 text-lg max-w-100">
            {{ $t("user.banned.message") }}
            <div class="flex justify-end">
                <Link
                    as="button"
                    method="post"
                    :href="route('logout')"
                    class="underline rounded-md mt-2 hover:bg-opacity-75 text-primary"
                >
                    {{ $t("dropdown.logout") }}
                </Link>
            </div>
        </div>
    </div>
</template>
