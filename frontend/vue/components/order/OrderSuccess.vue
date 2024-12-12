<template>
	<div v-if="isLoaded" class="order-success">
        <img class="order-success__img-success" src="../../../web/img/success-capybara.svg" alt="Заказ оформлен успешно">
        <div class="order-success__title">
            Спасибо, ваш заказ принят!
        </div>
        <div :class="['order-success__wrapper', {'order-success__wrapper_pay-mobile': isMobilePaySite}]">
            <div :class="['order-success__description', {'order-success__description_pay-mobile': isMobilePaySite}]">
                <ul class="order-success__list">
                    <li class="order-success__element">
                        <div class="order-success__question">Заказ</div>
                        <div class="order-success__answer order-success__answer-order">
                            <BaseTooltip v-if="!isMobile" content="Перейти к заказу">
                                <router-link :to="orderLink">
                                    {{ getOrderNumberWithDate }}
                                </router-link>                           
                            </BaseTooltip>
                            <span v-else @click="isSelectOrder = true">{{ getOrderNumberWithDate }}</span>
                        </div>
                    </li>
                    <li class="order-success__element">
                        <div class="order-success__question">Ожидаемая дата доставки</div>
                        <div class="order-success__answer">{{ getDeliveryDate }}</div>
                    </li>
                    <li class="order-success__element">
                        <div class="order-success__question">Тип оплаты</div>
                        <div class="order-success__answer">{{ getPaymentType }}</div>
                    </li>
                    <li class="order-success__element">
                        <div class="order-success__question">Тип доставки</div>
                        <div class="order-success__answer">{{ getDeliveryType }}</div>
                    </li>
                    <li class="order-success__element">
                        <div class="order-success__question">Адрес доставки</div>
                        <div class="order-success__answer">{{ orderInfo.delivery.address }}</div>
                    </li>
                    <li class="order-success__element">
                        <div class="order-success__question">Статус заказа</div>
                        <div :class="['order-success__answer', {'order-success__answer_red': orderInfo.status.status === 0}]">{{ getStatus }}</div>
                    </li>
                    <li class="order-success__element">
                        <div class="order-success__question">Сумма заказа</div>
                        <div class="order-success__answer order-success__price">{{ getFullPrice }} P</div>
                    </li>
                </ul>
                <div class="order-success__books">
                    <div class="order-success__img-book" v-for="(product, index) in orderInfo.products"
                        :key="index"
                    >
                        <img :src="getCover(product)" :alt="product.title" @error="setEmptyCover(product)">
                    </div>
                </div>
            </div>
            <div class="order-success__buttons">
                <OrderPaymentStatus
                    v-if="orderInfo.paymentType === 1"
                    class="order-success__trans"
                    :status="orderInfo.status.status"
                    :isDetail="false"
                    :isLegal="false"
                    :link="getButton.link"
                    :isBig="true"
                    :orderNumber="orderInfo.status.orderNumber"
                />
                <a v-else 
                    :href="getButton.link"
                    class="order-success__button"
                    target="_blank"
                >
                    {{ getButton.text }}
                </a>             
            </div>
        </div>
        <div v-if="isMobile && isSelectOrder" class="order-success__mobile-wrapper" @click="closeLink">
            <div class="order-success__mobile-link js-link">
                <div class="order-success__mobile-text">Перейти к заказу?</div>
                <button class="order-success__mobile-btn" @click.prevent="$router.push(orderLink)">
                    Перейти
                </button>
            </div>        
        </div>

	</div>
</template>

<script>
import OrderPaymentStatus from "../profile/content/order/OrderPaymentStatus"
import {ref} from "vue"
import axios from "axios"
import { useStore } from 'vuex'

export default {
    name: "OrderSuccess",
    components: {
        OrderPaymentStatus
    },
    data() {
        return {
            paymentType: {
                1: 'Оплата на сайте',
                2: 'Оплата при получении',
                3: 'Оплата по счету',
            },
            deliveryType: {
                1: 'Курьером',
                2: 'В пункт выдачи',
            },
            statuses: {
                0: 'Ожидает оплаты',
                1: 'Создан',
            },
            isMobile: window.innerWidth < 760,
            isSelectOrder: false
        }
    },
	props: {
        orderNumber: Number
    },
    setup() {
        const store = useStore()        
        const orderInfo = ref({})
        const getOrder = async (orderNumber) => {
            const data = await store.dispatch('user/getUserOrders', {orderNumber})

            orderInfo.value = data.orders[0]
        }

        return {
            orderInfo,
            getOrder
        }
    },
    beforeMount() {
        this.getOrder(this.orderNumber)
        window.addEventListener('resize', this.onResize);
    },
    computed: {
        isLoaded() {
            return !_.isEmpty(this.orderInfo)
        },
        getDeliveryDate() {
            let rawDate = new Date(this.orderInfo.status.deliveryDate)
            let year = rawDate.getFullYear()
            let month = rawDate.getMonth() + 1

            if (parseInt(month) < 10) month = '0' + month

            let day = rawDate.getDate()

            if (parseInt(day) < 10) day = '0' + day

            return day + '.' + month + '.' + year
        },
        getOrderNumberWithDate() {
            let rawData = new Date(this.orderInfo.status.date_create)
            let finalDate = rawData.toLocaleDateString('ru-Ru', {day: 'numeric', month: 'numeric', year: 'numeric'})

            return `№ ${this.orderInfo.status.orderNumber} от ${finalDate}`
		},
        getStatus() {
            return this.statuses[this.orderInfo.status.status]
		},
		getPaymentType() {
            return this.paymentType[this.orderInfo.paymentType]
		},
		getDeliveryType() {
            return this.deliveryType[this.orderInfo.delivery.type]
		},
        getFullPrice() {
            return this.orderInfo.status.orderPrice + this.orderInfo.delivery.price
		},
        getButton() {
            let button = {
                link: '',
                text: '',
            }

            switch (this.orderInfo.paymentType) {
                case 1:
                    button.link = this.orderInfo.transaction && !Array.isArray(this.orderInfo.transaction) ? this.orderInfo.transaction.trans_id : '#'
                    button.text = 'Оплатить картой онлайн'
                    break
                case 2:
                    button.link = '/'
                    button.text = 'Продолжить покупки'
                    break
                case 3:
                    let idOrder = this.orderInfo.status.id + ''
                    let middle = parseInt(Date.now() / 1000)

                    button.link = '/order/get-order-bill?token=' + idOrder + 'o' + (middle * this.orderInfo.status.id)
                    button.text = 'Скачать счёт на оплату'
                    break
            }

            return button
        },
        isMobilePaySite() {
            return +this.orderInfo.paymentType === 1 && this.isMobile
        },
        orderLink() {
            return `/profile/orders/${this.orderInfo.status.orderNumber}`
        }
	},
    methods: {
        getCover(product) {
            return _.isEmpty(product.cover) ? this.$emptyCoverUrl : product.cover
        },
        setEmptyCover(product) {
            product.cover = this.$emptyCoverUrl
        },
        onResize() {
            this.isMobile = window.innerWidth < 760
		},
        closeLink(e) {
            if (!e.target.closest('.js-link')) {
                this.isSelectOrder = false
            }
        }     
    }
}
</script>

<style lang="scss" scoped>
.order-success {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 726px;
    margin: 0 auto;

    @media (max-width: $screen-xl) {
        width: 100%;
    }

    &__title {
        @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-600);

        @media (min-width: $tablet-width) {
            @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-600);
        }

        @media (min-width: $screen-xl-l) {
            @include _typography-ext(fn, 24, 32, 600, ls, $Zinc-600);
        }
    }

    &__img-success {
        margin: 10px auto;
        width: 110px;

        @media (min-width: $tablet-width) {
            width: 170px;
            height: 117px;                
        }

        @media (min-width: $screen-xl-l) {
            margin: 20px auto 10px auto;
            width: 240px;
            height: 165px;                
        }            
    }

    &__wrapper {
        display: flex;
        flex-direction: column;
        max-width: 100%;
    }

    // theme
    &__wrapper_pay-mobile {
        @include _border-radius(10px);
        margin-top: 20px;
        padding: 10px;
        flex-direction: column-reverse;
        background-color: $white;

        .order-success__buttons {
            margin: 0 auto;
            max-width: 245px;
        }

        .order-success__button {
            @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-700);
            padding: 10px;
            height: 46px;
        }
    }

    &__description {
        @include _border-radius(8px);
        margin-top: 20px;
        padding: 20px;
        width: 100%;
        background: $white;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        
        @media (max-width: $screen-xl) {
            padding: 20px 40px;
        }

        @media (max-width: $screen-md) {
            @include _border-radius(10px);
            padding: 10px;
            box-shadow: none;
        }
    }

    // theme
    &__description_pay-mobile {
        margin-top: 10px;
    }

    &__list {
        margin-bottom: 10px;
        display: flex;
        flex-direction: column;
        row-gap: 10px;
        background: $white;
    }

    &__element {
        list-style: none;
        display: flex;
        padding: 6px 10px 6px 20px;
        border-bottom: 1px solid $Zinc-400;

        @media (max-width: $screen-md) {
            padding: 6px 10px;
            flex-direction: column;
        }
    }

    &__answer {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
        margin-left: auto;
        width: 100%;

        @media (max-width: $screen-md) {
            margin-left: 0;
            margin-bottom: 2px;
        }
    }

    &__answer_red {
        color: $Rose-600;
    }

    &__question {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-500);
        width: 280px;
        min-width: 280px; 
        display: flex;
        align-items: flex-end;
    }

    &__answer-order {
        @include _typography-ext(fn, 16, 26, 400, ls, $Indigo-400);
        position: relative;
    }

    &__price {
        @include _typography-ext(fn, 18, 26, 600, ls, $Zinc-900);
    }

    &__books {
        height: 98px;
        width: 342px;
        overflow: hidden;
    }

    &__img-book {
        display: inline-block;
        margin-right: 10px;
        width: 78px;
        height: 98px;
        padding: 6px;
        @include _border-radius(4px);
        border: 1px solid $Zinc-200;

        @media (max-width: $screen-md) {
            margin-right: 6px;
        }
    }

    &__img-book:nth-child(4){
        margin-right: 0;
    }

    &__img-book img {
        height: 100%;
        object-fit: contain;
    }

    &__buttons {
        margin-top: 20px;
        display: flex;
        width: 100%;

        @media (min-width: $tablet-width) {
            margin-top: 40px;
        }
    }

    &__button {
        @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-900);
        @include _button-reset(16px, $Red-200, none);
        margin: 0 auto;
        width: 100%;
        height: 56px;
        display: block;

        @media (min-width: $tablet-width) {
            width: 300px;
        }
    }

    &__trans {
        margin: 0 auto;
    }

    &__mobile-wrapper {
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        z-index: 101;
    }

    &__mobile-link {
        @include _border-radius(8px 8px 0 0);
        padding: 20px 15px 40px 15px;
        position: fixed;
        bottom: 0;
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%;
        text-align: center;
        background-color: $white;
        box-shadow: 0px -4px 6px rgba(0, 0, 0, 0.1);
    }

    &__mobile-text {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
    }

    &__mobile-btn {
        @include _typography-ext(fn, 18, 24, 600, ls, $white);
        @include _button-reset(16px, $Zinc-700, none);
    }
}

</style>