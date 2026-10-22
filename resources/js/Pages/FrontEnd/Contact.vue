<script setup>
const mapUrl = "https://maps.google.com/maps?q=Awesome%20Street%2C%20Tunis%20%E2%80%93%20Tunisia&output=embed";
</script>

<template>
    <Head :title="$t('Contact us')" />

    <main class="min-h-screen bg-lightCream text-brown">
        <header class="border-b border-line bg-offwhite px-5 py-12 text-center sm:px-8 md:py-16">
            <p class="mb-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">Sweetia</p>
            <h1 class="font-serif text-4xl leading-tight text-brown sm:text-5xl">{{ $t("Contact us") }}</h1>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-brown/70 sm:text-base">{{ $t("contactPage.intro") }}</p>
        </header>

        <section class="mx-auto grid max-w-6xl gap-5 px-5 py-10 sm:px-8 md:grid-cols-2 md:gap-7 md:py-16 lg:grid-cols-3">
            <article class="border border-line bg-white p-6 sm:p-8">
                <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-full border border-corange/30 bg-lightCream text-cOrangeDark">
                    <i class="ri-phone-line text-xl" aria-hidden="true"></i>
                </span>
                <h2 class="font-serif text-2xl text-brown">{{ $t("Phone Number") }}</h2>
                <p class="mt-2 text-sm leading-6 text-brown/65">{{ $t("contactPage.phone_note") }}</p>
                <a href="tel:56927726" class="mt-5 inline-flex items-center gap-2 font-semibold text-cOrangeDark transition-colors hover:text-brown" dir="ltr">
                    <span>56 927 726</span>
                    <i class="ri-arrow-right-up-line" aria-hidden="true"></i>
                </a>
            </article>

            <article class="border border-line bg-white p-6 sm:p-8">
                <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-full border border-corange/30 bg-lightCream text-cOrangeDark">
                    <i class="ri-mail-line text-xl" aria-hidden="true"></i>
                </span>
                <h2 class="font-serif text-2xl text-brown">{{ $t("Email") }}</h2>
                <p class="mt-2 text-sm leading-6 text-brown/65">{{ $t("contactPage.email_note") }}</p>
                <a href="mailto:hello@sweetia.tn" class="mt-5 inline-flex items-center gap-2 break-all font-semibold text-cOrangeDark transition-colors hover:text-brown" dir="ltr">
                    <span>hello@sweetia.tn</span>
                    <i class="ri-arrow-right-up-line" aria-hidden="true"></i>
                </a>
            </article>

            <article class="border border-line bg-white p-6 sm:p-8">
                <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-full border border-corange/30 bg-lightCream text-cOrangeDark">
                    <i class="ri-map-pin-line text-xl" aria-hidden="true"></i>
                </span>
                <h2 class="font-serif text-2xl text-brown">{{ $t("Our Location") }}</h2>
                <p class="mt-2 text-sm leading-6 text-brown/65">{{ $t("contactPage.address_note") }}</p>
                <p class="mt-5 font-semibold text-cOrangeDark" dir="ltr">Awesome Street, Tunis – Tunisia</p>
            </article>
        </section>

        <section class="mx-auto max-w-6xl px-5 pb-12 sm:px-8 md:pb-16">
            <div class="mb-5">
                <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">Sweetia</p>
                <h2 class="font-serif text-3xl text-brown">{{ $t("Our Location") }}</h2>
            </div>
            <div class="overflow-hidden border border-line bg-white p-2 sm:p-3">
                <iframe
                    :src="mapUrl"
                    :title="$t('contactPage.map_title')"
                    class="block h-[320px] w-full sm:h-[400px]"
                    style="border: 0"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </section>
    </main>
</template>
