<template>
    <div class="cart-info__item">
        <div class="cart-info__item-text">Товары на сумму</div>
        <div class="cart-info__price">
            {{ $filters.numberFormat(price.withoutDiscount) }}&nbsp;&#8381;
        </div>
    </div>

    <div class="cart-info__item">
        <div class="cart-info__item-text">Скидка</div>
        <div class="cart-info__discount">
            -{{ $filters.numberFormat(price.discount) }}&nbsp;&#8381;
        </div>
    </div>

    <div class="cart-info__item">
        <div class="cart-info__item-text">Итого</div>
        <div class="cart-info__total">
            {{ $filters.numberFormat(price.total) }}&nbsp;&#8381;
        </div>
    </div>

    <div class="cart-info__wrap-button">
        <button class="cart-info__button button-black" :class="{'disabled': isCreateOrderBtnDisabled}" @click="createOrder">
            {{ isUserLoggedIn ? 'Оформить заказ' : 'Войти и оформить заказ' }}
        </button>
    </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
export default {
    name: "CartInfo",
	props: {
        price: {
            type: Object,
			default: null
		}
	},
    beforeMount() {
        if (localStorage.getItem('notAuthFromOrder')) {
            this.loadAuthModal()
            localStorage.removeItem('notAuthFromOrder')
        }
        else this.getUserInfo()
    },
    computed: {
        ...mapState({
            userInfo: state => state.user.userInfo
        }),
        isUserLoggedIn() {
            return !_.isEmpty(this.userInfo) && this.userInfo.hasOwnProperty('isLegal')
        },
        isCreateOrderBtnDisabled() {
            return this.userInfo === null
        }
    },
	methods: {
        ...mapActions({
            getUserInfo: 'user/getUserInfo'
        }),
        createOrder() {
            if (this.isCreateOrderBtnDisabled) return

            if (this.isUserLoggedIn) {
                this.$router.push({
                    name: 'Order'
                })
            } else {
                this.loadAuthModal()
            }
		},
        loadAuthModal() {
            const regModalBtn = document.querySelector('.js-login-button')

            if (!regModalBtn) return

            localStorage.setItem('fromCart', 1);
            regModalBtn.click()
        }
	}
}
</script>

<style lang="scss" scoped>
.cart-info {
    &__item {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-900);

        @media (max-width: $screen-xl) {
            margin-bottom: 10px;
        }

        &-text {
            color: $Zinc-500;
            font-weight: 400;
        }
    }

    &__total {
        color: $Red-400;
    }
    
    &__wrap-button {
        padding-top: 20px;
        border-top: 2px solid $Zinc-200;

        @media (max-width: $screen-xl) {
            padding-top: 10px;
            text-align: end;
        }
    }
    
    &__button {
        width: 100%;

        @media (max-width: $screen-xl) {
            max-width: 240px;
        }

        @media (max-width: $screen-md) {
            max-width: unset;
        }

        &.disabled {
            color: $Zinc-400;
            background-color: $Zinc-100;
            cursor: default;
        }
    }
}
</style>