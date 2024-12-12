<template>
    <nav>
        <ul class="menu__list">
            <li v-for="item in list" class="menu__item">
                <div  v-if="item.sub !== undefined"
                    class="menu__link">
                    {{ item.text }}
                </div>
                <a v-else
                   :href="item.route" class="menu__link">
                    {{ item.text }}
                </a>
                <ul class="menu__list menu__list_sub">
                    <li class="menu__item menu__item_sub" v-for="subItem in item.sub">
                        <a :href="subItem.route" class="menu__link">
                            {{ subItem.text }}
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    name: "BaseMenu",
    data() {
        return {
            list: [
                {
                    text: 'Каталог',
                    sub: [
                        {
                            text: 'Новые книги',
                            route: '/admin/products-new'
                        },
                        {
                            text: 'Все книги',
                            route: '/admin/products'
                        },
                    ],
                },
                {
                    text: 'Заказы',
                    sub: [
                        {
                            text: 'Заказы',
                            route: '/admin/orders',
                            parent: 1
                        },
                    ],
                },
                {
                    text: 'Маркетинг',
                    sub: [
                        {
                            text: 'Жанры',
                            route: '/admin/marketing-genres',
                        },
                        {
                            text: 'Баннеры',
                            route: '/admin/marketing-banners',
                        },
                        {
                            text: 'Полки на главной',
                            route: '/admin/marketing-shelves',
                        },
                        {
                            text: 'Акции',
                            route: '/admin/marketing-promotions',
                        },
                    ],
                },
                {
                    text: 'Настройки',
                    sub: [
                        {
                            text: 'Пользователи',
                            route: '/admin/user',
                        },
                    ],
                },
                {
                    text: 'ВЫЙТИ',
                    route: '/admin/logout',
                },
            ]
        }
    }
}
</script>

<style lang="scss">
.menu {
    $root: menu;

    &__list {
        background-color: $white;

        &_sub {
            position: absolute;
            display: none;
            right: -150px;
            top: 0;
            z-index: 1;

            &:hover {
                display: block;
            }
        }
    }

    &__item {
        position: relative;
        width: 150px;
        height: 80px;
        border: solid 1px $gray;

        &_sub {
            height: 40px;
        }

        &:hover {
            & > .#{$root}__list_sub {
                display: block;
            }
        }
    }

    &__link {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;

        &.router-link-active{
            background-color: lightgrey;
        }
    }
}

</style>