<script setup>
import AppLogo from "../AppLogo.vue";
import TopBar from "./TopBar.vue";
import Nav from "./Nav.vue";
import MobileNav from "./MobileNav.vue";
import CartDropDown from "./CartDropDown.vue";
import LanguageSwitcher from "../Admin/Header/LanguageSwitcher.vue";

</script>

<template>
    <header class=" z-[999] shadow-lg bg-offwhite  border-b border-graydark">
        <TopBar></TopBar>

        <div class="p-4 lg:p-8">

            <div class="flex items-center justify-between gap-4">
                <Link :href="route('FE.home')" aria-label="Home">
                <AppLogo class=" w-[150px] drop-shadow-xl hover:scale-105 transition-transform duration-300" />
                </Link>


                <Nav></Nav>

                <div class="flex items-center gap-4">
                    <CartDropDown />
                    <LanguageSwitcher :is-in-front-end="true" />

                </div>
            </div>

        </div>
        <MobileNav />
    </header>
</template>
