<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item :item="item" :index="i"></app-menu-item>
        </template>
    </ul>
</template>

<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';

const model = ref([
    {
        label: 'Главная',
        items: [{ label: 'Главная', icon: 'pi pi-fw pi-home', to: '/admin' }]
    },
    {
        label: 'Заказы',
        items: [{ label: 'Заказы', icon: 'pi pi-fw pi-wallet', to: '/admin/orders' }]
    },
    {
        label: 'Каталог',
        items: [
            { label: 'Новые книги', icon: 'pi pi-fw pi-bookmark', to: '/admin/products-new' },
            { label: 'Все книги', icon: 'pi pi-fw pi-book', to: '/admin/products' },
            { label: 'Персоны', icon: 'pi pi-fw pi-id-card', to: '/admin/persons' },
            { label: 'Издательства', icon: 'pi pi-fw pi-language', to: '/admin/publishers' }
        ]
    },
    {
        label: 'Маркетинг',
        items: [
            { label: 'Жанры', icon: 'pi pi-fw pi-tags', to: '/admin/marketing-genres' },
            { label: 'Баннеры', icon: 'pi pi-fw pi-images', to: '/admin/marketing-banners' },
            { label: 'Полки на главной', icon: 'pi pi-fw pi-ticket', to: '/admin/marketing-shelves' },
            { label: 'Акции', icon: 'pi pi-fw pi-percentage', to: '/admin/marketing-promotions' },
        ]
    },
    {
        label: 'Пользователи',
        items: [{ label: 'Пользователи', icon: 'pi pi-fw pi-users', to: '/admin/user' }]
    },
]);
</script>

<style lang="scss" scoped></style>
