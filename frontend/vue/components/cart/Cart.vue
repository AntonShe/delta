<template>
    <div class="bg-body">
        <div class="container cart">
            <div class="cart__title-wrap">
                <h1 class="cart__title">Корзина</h1>
                <div class="cart__quantity" v-if="isCartNotEmpty">{{ getQuantity }}</div>
            </div>
            <div v-if="isCartNotEmpty">
                <div class="notification">
                    <cart-notification
                        v-for="(notification, index) in getNotifications"
                        :key="index"
                        :text="notification"
                        @close="closeNotification(index)"
                    />
                </div>
                <div class="cart-content">
                    <div class="cart-content__left cart-list">
                        <product-list
                            :product-list="cart.items"
                        />
                    </div>
                    <div class="cart-content__right cart-info">
                        <cart-info :price="getCartPrice" v-if="isCartNotEmpty"/>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="empty-cart">
                    <div class="empty-cart__wrap">
                        <div class="empty-cart__img">
                            <img src="/img/empty-cart.png" alt="">
                        </div>
                        <div class="empty-cart__title_min">Ваша корзина пуста</div>
                        <div class="empty-cart__descr">Попробуйте найти интересующий вас&nbsp;товар через поиск или каталог</div>
                        <a href="/catalog/1" class="empty-cart__link button-black-big">Перейти в каталог</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ProductList from "./ProductList"
import CartInfo from "./CartInfo"
import CartNotification from "./CartNotification"
import { mapState } from 'vuex'

export default {
    name: "Cart",
    components: {
        CartNotification,
        CartInfo,
        ProductList
    },
    mounted() {
        dataLayerViewCart(Object.values(this.cart.items), this.cart.final_price, false);
    },
    computed: {
        ...mapState({
            cart: state => state.cart.cartData
        }),
		getQuantity() {
            let quantity = 0

            if (!_.isEmpty(this.cart) && !_.isEmpty(this.cart.items)) {
                _.forEach(this.cart.items, (item) => {
                    quantity += item.quantity.cart > item.quantity.available
                        ? item.quantity.available
                        : item.quantity.cart
                })
            }

            return quantity
		},
        isCartNotEmpty() {
            return (!_.isEmpty(this.cart) && !_.isEmpty(this.cart.items))
		},
        getNotifications() {
            return this.cart.notifications
        },
        getCartPrice() {
            let price = {
                withoutDiscount: this.cart.raw_price,
                discount: this.cart.discount_sum,
                total: this.cart.final_price,
            }

            return price
        }
	},
    methods: {
        closeNotification(index) {
            this.cart.notifications.splice(index, 1)
        }
    }
}
</script>

<style lang="scss">
.bg-body {
    background-color: $Zinc-50;

    @media (max-width: $screen-xl) {
        background-color: $white;
    }
}

.cart {
    padding-top: 20px;
    padding-bottom: 50px;

    @media (max-width: 1024px) {
        padding-top: 10px;
        padding-bottom: 40px;
    }

    @media (max-width: 760px) {
        padding-top: 76px;
    }

    &__title-wrap {
        margin-bottom: 20px;
        display: flex;
    }

    &__title {
        @include _typography-ext(fn, 30, 36, 600, ls, $Zinc-900);

        @media (max-width: 1024px) {
            @include _typography-ext(fn, 24, 32, 600, ls, $Zinc-900);
        }

        @media (max-width: 760px) {
            @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);
        }
    }

    &__quantity {
        margin-left: 4px;
        @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-400);

        @media(max-width: 1024px) {
            @include _typography-ext(fn, 14, 20, 600, ls, $Zinc-400);
        }
    }
}

.cart-content {
    display: flex;
    justify-content: space-between;

    &__left {
        max-width: 1068px;
        width: 100%;
        margin-right: 20px;
    }

    &__right {
        width: 388px;
    }

    @media (max-width: 1500px) {
        flex-direction: column;

        &__left,
        &__right {
            width: 100%;
            max-width: unset;
        }

        &__left {
            position: relative;
            margin-bottom: 20px;
        }
    }
}

.cart-info {
    padding: 20px;
    height: max-content;
    @include _border-radius(8px);
    border: 1px solid $Zinc-200;
    background-color: $white;

    @media (max-width: $screen-md) {
        padding: 20px 14px;
    }
}

.empty-cart {

    @media (min-width: $screen-md) {
        padding-bottom: 10px;
    }

    @media (min-width: $screen-xl-l) {
        padding-bottom: 50px;
    }

    &__wrap {
        margin: 0 auto;
        max-width: 395px;
        width: 100%;

        display: flex;
        flex-direction: column;
        align-items: center;
    }

    &__img {
        margin-bottom: 10px;
        width: 100%;
        height: 100%;
        max-width: 175px;

        @media (min-width: $screen-md) {
            max-width: 369px;
        }

        @media (min-width: $screen-xl-l) {
            margin-bottom: 20px;
        }

        img {
            width: 100%;
            height: 100%;
        }
    }

    &__title_min {
        margin-bottom: 10px;
        @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);

        @media (min-width: $screen-md) {
            font-size: 24px;
            line-height: 32px;
        }

        @media (min-width: $screen-xl-l) {
            margin-bottom: 20px;
            font-size: 30px;
            line-height: 36px;
        }
    }

    &__descr {
        margin-bottom: 20px;
        text-align: center;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);

        @media (min-width: $screen-xl-l) {
            margin-bottom: 40px;
            font-size: 18px;
            line-height: 24px;
        }
    }

    &__link {
        display: block;
        width: 100%;
        max-width: 330px;

        @media (min-width: $screen-md) {
            max-width: 271px;
        }
    }
}
</style>