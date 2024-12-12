<template>
    <div class="order-info">
        <div class="order-info__top">
            <div class="order-info__top-title">
                Ваш заказ <span class="order-info__top-title_size"> / {{ getQuantity }}</span>
            </div>
            <div class="order-info__top-images">
                <div v-for="product in cart.items" class="order-info__top-img" :key="product.product_id">
                    <img :key="product.product_id"
                         :src="product.productInfo.cover"
                         :alt="product.productInfo.title"
                    >
                </div>
            </div>
        </div>
        <div class="order-info__bottom">
            <div class="order-info__item">
                <div class="order-info__item-text">Сумма заказа</div>
                <div class="order-info__price">{{ $filters.numberFormat(getFullPrice) }} &#8381;</div>
            </div>
            <div class="order-info__item">
                <div class="order-info__item-text">Скидка</div>
                <div class="order-info__discount">-{{ $filters.numberFormat(cart.discount_sum) }} &#8381;</div>
            </div>
            <div class="order-info__item">
                <div class="order-info__item-text">Доставка</div>
                <div class="order-info__delivery">{{ getDeliveryPrice }} &#8381;</div>
            </div>
            <div class="order-info__item">
                <div class="order-info__item-text">Итого</div>
                <div class="order-info__total">{{ $filters.numberFormat(cart.final_price + getDeliveryPriceForSum) }} &#8381;</div>
            </div>
            <div class="order-info__wrap-button">
                <BaseTooltip :content="tooltips[0]">
                    <router-link :to="isValid && isButtonActive ? '#' : ''" :class="['order-info__link', {'disable': !(isValid && isButtonActive)}]" @click="confirm">
                        Подтвердить заказ
                    </router-link>
                </BaseTooltip>
            </div>
            <div class="order-info__text">
              Нажимая кнопку &laquo;Подтвердить заказ&raquo;, я&nbsp;подтверждаю, что достиг(ла) 18&nbsp;лет, ознакомлен(а) с&nbsp;<a class="order-info__text-link" href="/user-agreement" target="_blank">Пользовательским соглашением</a> и&nbsp;согласен(а) на&nbsp;получение информационных email и&nbsp;СМС рассылок от&nbsp;ООО &laquo;Цунами Букс&raquo; (ИНН 9725093665)
            </div>
        </div>
    </div>
</template>

<script>
import {inject} from "vue"
import { mapState } from "vuex"

export default {
    name: "OrderPrice",
    emits: ['confirmOrder'],
    data() {
        return {
            tooltipsList: {
                delivery: 'Выберите доставку',
                userProfile: 'Заполните данные получателя',
                userPhone: 'Подтвердите телефон',
            },
            tooltips: [],
            isButtonActive: true
        }
    },
    setup() {
        // const cart = inject('cart')

        // return {
        //     cart
        // }
    },
	methods: {
        confirm() {
            if (!this.isValid || !this.isButtonActive) return

            this.isButtonActive = false
            this.$emit('confirmOrder')
		}
	},
	computed: {
        ...mapState({
            orderData: state => state.order.orderData,
            orderConfirm: state => state.order.orderConfirm,
            cart: state => state.cart.cartData
        }),
        isValid() {
            let isValid = true
            this.tooltips = []

            _.forEach(this.orderConfirm, (item, index) => {
                if (!item) {
                    isValid = false
                    this.tooltips.push(this.tooltipsList[index])
                } else if (index == 'userPhone') {
                    let pin = localStorage.getItem('pin')
                    let newPhone = localStorage.getItem('unregistrationPhone')

                    if (!_.isNull(pin) && _.isNull(newPhone)) {
                        this.tooltips.push(this.tooltipsList.userPhone)
                    }
                }
            })

            this.tooltips.push('Оформить заказ')

            return isValid
        },
        getFullPrice() {
            return this.cart.final_price + this.cart.discount_sum
        },
        getQuantity() {
            let quantity = 0

            _.forEach(this.cart.items, (item) => {
                quantity += item.quantity.cart
            })

            return (quantity > 0) ? quantity + ' шт' : '';
		},
        getDeliveryPrice() {
            return !_.isNull(this.orderData.deliveryPrice) ? this.orderData.deliveryPrice : 'Уточняется'
		},
        getDeliveryPriceForSum() {
            return !_.isNull(this.orderData.deliveryPrice) ? this.orderData.deliveryPrice : 0
        }
	}
}
</script>

<style lang="scss" scoped>
.order-info {
    width: 388px;
    padding: 20px;
    background-color: $white;

    @media (max-width: $screen-xl-l) {
        width: 100%;
        display: flex;
    }

    @media (max-width: $screen-md) {
       flex-direction: column;
        background-color: inherit;
    }

    &__text-link {
        color: #818cf8;
        cursor: pointer;
        transition: .3s ease-in-out;

        &:hover,
        &:active {
            color: #4f46e5;
        }
    }

    &__top {
        padding-bottom: 20px;
        margin-bottom: 20px;
        border-bottom: 2px solid $Zinc-200;

        @media (max-width: $screen-xl-l) {
            border-bottom: unset;
        }

        &-title {
            margin-bottom: 20px;
            @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);

            &_size {
                @include _typography-ext(fn, 14, 20, 600, ls, $Zinc-500);
                white-space: nowrap;
            }
        }

        &-images {
            height: 98px;
            overflow: hidden;

            @media (max-width: $screen-md) {
                display: none;
            }
        }

        &-img {
            display: inline-block;
            width: 78px;
            height: 98px;
            padding: 6px;
            vertical-align: middle;
            border: 1px solid $Zinc-200;
            @include _border-radius(8px);

            &:not(:nth-child(4)) {
                margin-right: 12px;
            }

            img {
                height: 100%;
                object-fit: contain;
            }
        }
    }

    &__item {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-500);

        &:last-child {
            color: $Zinc-900;
            margin-bottom: 0;
        }

        &-text {
            font-weight: 400;
        }
    }

    &__total {
        color: $Zinc-900;
    }

    &__wrap-button {
        position: relative;
        margin: 20px 0;
    }

    &__link {
        @include _typography-ext(fn, 18, 24, 600, ls, $white);
        @include _button-reset(16px, $Zinc-700, none);
        @include _hover(bc, tc, bgi, $Zinc-900);
        height: 56px;
        display: block;

        &.disable {
            @include _button-reset(16px, $Zinc-100, none);
            color: $Zinc-400;

            &:hover, &:hover:not(:focus) {
                @include _button-reset(16px, $Zinc-100, none);
                color: $Zinc-400;
                cursor: default;
            }
        }            
    }

    &__text {
        @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-400);
    }
}
</style>