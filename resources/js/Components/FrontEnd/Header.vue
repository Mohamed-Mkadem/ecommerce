<script setup>
import LanguageSwitcher from "../Admin/Header/LanguageSwitcher.vue";
import AppLogo from "../AppLogo.vue";
import TopBar from "./TopBar.vue";
import Nav from "./Nav.vue";
import MobileNav from "./MobileNav.vue";
import CartDropDown from "./CartDropDown.vue";
</script>

<template>
    <header
        class="sticky top-0 z-[999] shadow-lg backdrop-blur-md bg-gradient-to-r from-yellow-100/95 via-yellow-50/95 to-gold/20 dark:bg-primary/95 transition-all duration-300 border-b border-gold/30"
    >
        <!--  top bar starts here -->
        <TopBar></TopBar>
        <!--  top bar ends here -->

        <div class="py-4 xmd:py-0 xmd:pt-4 px-4">
            <div class="max-w-[1500px] mx-auto">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <Link :href="route('FE.home')" aria-label="Home">
                            <AppLogo
                                class="xmd:hidden w-[55px] drop-shadow-md"
                                color="blue"
                            />
                        </Link>
                        <p
                            class="hidden xmd:flex text-black dark:text-white font-semibold items-center rtl:flex-row-reverse gap-3"
                        >
                            <i
                                class="ri-customer-service-2-fill text-3xl text-sky-700"
                            ></i>
                            <a
                                dir="ltr"
                                href="tel:29144980"
                                class="hover:text-sky-700"
                                >29 144 980</a
                            >
                        </p>
                    </div>

                    <Link :href="route('FE.home')" aria-label="Home">
                        <AppLogo
                            class="hidden xmd:block w-[150px] drop-shadow-xl hover:scale-105 transition-transform duration-300"
                            color="blue"
                        />
                    </Link>

                    <div class="flex items-center gap-4">
                        <CartDropDown />
                        <LanguageSwitcher :is-in-front-end="true" />
                    </div>
                </div>
                <Nav></Nav>
            </div>
        </div>
        <MobileNav />
    </header>
</template>
