<template>
    <div class="layout-topbar">
        <router-link to="/admin" class="layout-topbar-logo">
            <img src="/img/logo-header.svg" alt="logo"/>
        </router-link>

        <button class="p-link layout-menu-button layout-topbar-button" @click="onMenuToggle()">
            <i class="pi pi-bars"></i>
        </button>

        <a href="/admin/logout" class="p-link layout-out-button layout-topbar-button">
            <i class="pi pi-fw pi-sign-out"></i>
            <span>Выйти</span>
        </a>
    </div>
</template>

<script setup>
import {useLayout} from './composables/layout';

const {onMenuToggle} = useLayout();

</script>

<style lang="scss" scoped></style>
