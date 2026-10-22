<script setup>
import AppLogo from "../AppLogo.vue";
import TopBar from "./TopBar.vue";
import Nav from "./Nav.vue";
import MobileNav from "./MobileNav.vue";
import CartDropDown from "./CartDropDown.vue";
import LanguageSwitcher from "../Admin/Header/LanguageSwitcher.vue";

</script>

<template>
    <header class=" z-[999] shadow-lg bg-offwhite  border-b border-line">
        <TopBar></TopBar>

        <div class="p-4 lg:p-8">

            <div class="flex items-center justify-between gap-2 sm:gap-4">
                <div class="flex min-w-0 items-center gap-1 sm:gap-3">
                    <MobileNav />
                    <Link :href="route('FE.home')" aria-label="Home" class="shrink-0">
                    <AppLogo
                        class="w-[100px]  xsm:w-[clamp(100px,36vw,150px)] drop-shadow-xl transition-transform duration-300 hover:scale-105" />
                    </Link>
                </div>


                <Nav></Nav>

                <div class="flex items-center gap-4">
                    <CartDropDown />
                    <LanguageSwitcher :is-in-front-end="true" />

                </div>
            </div>

        </div>
    </header>
</template>
