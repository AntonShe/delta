<template>
	<div class="order-detail" v-if="isLoaded && isExist">
		<div class="order-detail__top-block">
			<div class="order-detail__title">
                <ProfileTitle :title="getTitle" :isShowAlways="true" :isShowDesktop="true" :isOrderDetail="true" link="/profile/orders"/>
                <div class="order-detail__title-wrapper">
                    <span class="order-detail__date">
                        {{ getDate }}
                    </span>
                    <span v-if="isMobile && !isOnlinePaymentAndNotYetPaid" class="order-detail__status">
                        <OrderPaymentStatus :status="order.status.status" :isDetail="true" :isLegal="isLegal" :link="getPaymentLink" :orderNumber="order.status.orderNumber"/>
                    </span>               
                </div>
			</div>
            <OrderPaymentInformer v-if="isOnlinePaymentAndNotYetPaid && !isLegal" />
            <span v-if="!isMobile && !isOnlinePaymentAndNotYetPaid" class="order-detail__status">
                <OrderPaymentStatus :status="order.status.status" :isDetail="true" :isLegal="isLegal" :link="getPaymentLink" :orderNumber="order.status.orderNumber"/>
            </span>
		</div>
		<div class="order-detail__list">
			<div class="order-detail__list-title">Состав заказа</div>
            <ul class="order-detail__list-list" v-for="(product, index) in order.products"
                :key="index"
            >
                <li class="order-detail__list-item">
                    <Product :product="product" :isCart="false" :index="index"/>
                </li>
            </ul>
		</div>
        <div class="order-detail__info">
			<div class="order-detail__info-documents">
				<base-dropdown v-if="isLegal" class="order-detail__docs" :items="documents">
					<template #button>
						<button>Документы по заказу</button>
					</template>
				</base-dropdown>
			</div>
			<div class="order-detail__info-price">
				<OrderPrice
					:price="{
                        items: order.status.orderPrice,
                        delivery: order.delivery.price,
					}"
                    :quantity="getQuantity"
					:link="order.paymentLink"
					:status="order.status.status"
				/>
                <OrderPaymentStatus
                    v-if="isOnlinePaymentAndNotYetPaid"
                    class="order-detail__pay-btn"
                    :status="order.status.status"
                    :isDetail="true"
                    :isLegal="isLegal"
                    :link="getPaymentLink"
                    :isTransactionDisabled="isTransactionDisabled"
                    :orderNumber="order.status.orderNumber"
                />
			</div>
        </div>
        <div class="order-detail__delivery">
			<OrderDelivery
				:paymentType="order.paymentType"
				:delivery="getDeliveryData"
                :storageDate="order.storageDate"
			/>
        </div>
        <div v-if="isCanceliable" class="order-detail__cancel">
			<div class="button__cancel" @click="showCancelOrderModal">
				<BaseSvgStore icon="cancelOrder"/> <span class="button__cancel-text">Отменить заказ</span>
			</div>

            <BaseModal :isOpen="isShowCancelOrderModal" @close="hideModal" :withCross="false">
                <template v-slot:title>Заказ № {{ order.status.orderNumber }}</template>
                <div class="cancel-popup">
                    Вы действительно хотите отменить заказ?
					<div class="cancel-popup__buttons">
						<button class="cancel-popup__button cancel-popup__button_red" @click="hideModal">
                            Отмена
						</button>
						<button class="cancel-popup__button cancel-popup__button_black" @click="cancelOrder">
                            Подтвердить
						</button>
					</div>
                </div>
            </BaseModal>
        </div>
	</div>

    <div class="order-detail" v-if="isLoaded && !isExist">
        <div class="order-detail__top-block">
            <div class="order-detail__title">
                <ProfileTitle title="Заказ не найден :(" :isShowAlways="true" link="/profile/orders"/>
            </div>
        </div>
    </div>

    <BaseModal :isOpen="isOrderPayModalOpen" @close="isOrderPayModalOpen = false" :withCross="false">
        <template v-slot:title>{{ orderPayInfo.title }}</template>
        <div class="cancel-popup">
            {{ orderPayInfo.text }}
            <div class="cancel-popup__buttons">
                <button class="cancel-popup__button cancel-popup__button_red" @click="isOrderPayModalOpen = false">
                    Закрыть
                </button>
                <button class="cancel-popup__button cancel-popup__button_black" @click="cancelOrder">
                    Попробовать ещё раз
                </button>
            </div>
        </div>
    </BaseModal>
</template>

<script>
import {ref, readonly, provide} from "vue"
import axios from "axios";
import OrderPaymentStatus from "./OrderPaymentStatus"
import OrderDelivery from "./OrderDelivery"
import OrderPrice from "./OrderPrice"
import OrderPaymentInformer from './OrderPaymentInformer.vue'
import Product from "../../../cart/Product"
import ProfileTitle from "../ProfileTitle"
import BaseDropdown from "../../../base/BaseDropdown"
import {data_format} from "../../../../mixins/data_format"
import { useStore } from 'vuex'

export default {
    name: "OrderDetail",
    components: {
        ProfileTitle,
        Product,
        OrderPrice,
        OrderDelivery,
        OrderPaymentStatus,
        OrderPaymentInformer,
        BaseDropdown
    },
    mixins: [data_format],
    data() {
        return {
            order: null,
            isShowCancelOrderModal: false,
            isMobile: window.innerWidth < 760,
            isOrderPayModalOpen: false,
            isOrderPaySuccess: false
		}
	},
    setup() {
        const store = useStore()
        const order = ref(null)
        const isTransactionDisabled = ref(false)

        const getOrderFull = async (orderNumber) => {
            const data = await store.dispatch('user/getUserOrders', {orderNumber})

            order.value = data.orders[0]
            isTransactionDisabled.value = !Array.isArray(order.value.transaction) && order.value.transaction.isPending
        }

        provide('order', readonly(order))

        return {
            order,
            getOrderFull,
            isTransactionDisabled
        }
    },
	beforeMount() {
        this.getOrderFull(this.$route.params.orderNumber)
        window.addEventListener('resize', this.onResize)
    },
    computed: {
        documents() {
            let idOrder = this.order.status.id + ''
            let middle = parseInt(Date.now() / 1000)

            return [
                {
                    title: 'Спецификация',
                    link: '/order/get-specifications?token=' + idOrder + 'o' + (middle * this.order.status.id)
                },
                {
                    title: 'ИНН/ОГРН',
                    link: '/file/INN_OGRN_DELTABOOK.pdf'
                },
                {
                    title: 'Устав',
                    link: '/file/Ustav_Deltabook.pdf'
                }
            ]
        },
        isLoaded() {
            return !_.isNull(this.order)
        },
        isExist() {
            return !_.isNull(this.order) && !_.isEmpty(this.order)
        },
        isLegal() {
            return this.order.user.profile[0].isLegal == 1 ? true : false
        },
        getTitle() {
            return 'Заказ № ' + this.order.status.orderNumber;
        },
        getDate() {
            let date = this.order.status.date_create;
            return `${this.dataFormatToDaysAndMonth(date)} ${this.dataFormatToHourAndMinutes(date)}`;
        },
        getQuantity() {
            let count = 0

            _.forEach(this.order.products, (item) => {
                count += item.quantityCart
            })

            return count
        },
        getDeliveryData() {
            let firstName = _.isEmpty(this.order.subUser.subFirstName)
                ? this.order.user.firstName
                : this.order.subUser.subFirstName
            let lastName = _.isEmpty(this.order.subUser.subLastName)
                ? this.order.user.lastName
                : this.order.subUser.subLastName
            let phone = _.isEmpty(this.order.subUser.subPhone)
                ? this.order.user.phone
                : this.order.subUser.subPhone

            return {
                receiver: {
                    name: firstName + ' ' + lastName,
                    phone: phone
                },
                address: this.order.delivery.address,
                type: this.order.delivery.type
            }
        },
        getPaymentLink() {
            let idOrder = this.order.status.id + ''
            let middle = parseInt(Date.now() / 1000)

            return this.isLegal
                ? '/order/get-order-bill?token=' + idOrder + 'o' + (middle * this.order.status.id)
                : !Array.isArray(this.order.transaction) ? this.order.transaction.trans_id : '#'
        },
        isCanceliable() {
            return this.order.status.status < 4 || this.order.status.status === 31
        },
        orderPayInfo() {
            const payInfo = {}

            if (this.order && this.order.status) {
                payInfo.title = this.isOrderPaySuccess ? 'Оплата прошла' : 'Что-то пошло не так',
                payInfo.text = this.isOrderPaySuccess ? `Мы уже собираем ваш заказ №${this.order.status.orderNumber}` : `Заказ №${this.order.status.orderNumber} не получилось оплатить`
            }

            return payInfo
        },
        isOnlinePaymentAndNotYetPaid() {
            return (this.order.status.paymentType === 1 || this.order.status.paymentType === 3) && this.order.status.status === 0
        }
    },
	methods: {
        showCancelOrderModal() {
            this.isShowCancelOrderModal = true;
		},
		cancelOrder() {
            axios.post('/order/reject',
                {
                    id: this.order.status.id
                }
            ).then(response => {
                if (response.data.status === true) {
                    this.getOrderFull(this.order.status.orderNumber)

                    const shipping = this.order.delivery.price ? this.order.delivery.price: 0;
                    const total = this.order.status.orderPrice;
                    dataLayerRefund(this.order.status.orderNumber, Object.values(this.order.products), total, shipping, '', false);
                } else {
                    alert('Не удалось отменить. Попробуйте позже или свяжитесь с менеджером.');
                    throw new Error('Не удалось отменить заказ')
                }
            }).catch(error => {
                throw new Error("Ошибка " + error)
            })

            this.isShowCancelOrderModal = false;
		},
        hideModal() {
            this.isShowCancelOrderModal = false;
		},
        onResize() {
            this.isMobile = window.innerWidth < 760
		}       
	}
}
</script>

<style lang="scss">
.order-detail {
    display: flex;
    flex-direction: column;
    max-width: 1340px;
    width: 100%;

    &__top-block {
        padding: 10px;
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        row-gap: 10px;
        justify-content: space-between;
        align-items: center;
        background-color: $white;

        @media (max-width: 768px) {
            flex-wrap: wrap;
        }

        @media (max-width: $screen-md) {
            background-color: inherit;
            padding: 0;
            order: -2;
        }
    }

    &__title {
        display: flex;
        align-items: center;
        gap: 10px;

        @media (max-width: 768px) {
            flex-wrap: wrap;
        }

        @media (max-width: $screen-md) {
            flex-direction: column;
            align-items: unset;
        }
    }

    &__date {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-400);

        @media (max-width: 768px) {
            @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-400);
            min-width: fit-content;
            color: #a1a1aa;
        }
    }

    &__status {
        @media (max-width: 768px) {
            width: 100%;
        }
    }

    &__info {
        margin-bottom: 20px;
        width: 100%;
        padding: 20px 20px 20px 61px;
        display: flex;
        justify-content: space-between;
        background-color: $Red-25;

        @media (max-width: $screen-md) {
            padding: 10px;
            flex-direction: column;
            order: -1;
        }      

        &-documents {
            width: 202px;

            @media (max-width: $screen-md) {
                width: 100%;
                order: 1;                
            }

            button {
                width: 100%;
                @include _typography-ext(fn, 14, 20, 700, ls, $Zinc-900);
                @include _button-reset(8px 12px 8px 38px, $Red-50, none);
                @include  _hover(bc, tc, bgi, $Red-200);
                background-image: get-icon('clip', $Zinc-900);
                background-repeat: no-repeat;
                background-position: 12px;
            }
        }

        &-price {
            width: 292px;

            @media (max-width: $screen-md) {
                width: 100%;
            }
        }

        .order-detail__docs {
            @media (max-width: 955px) {
                margin-top: 20px;
            }
        }
    }

    &__delivery {
        margin-bottom: 20px;
    }

    &__list {
        margin-bottom: 20px;

        &-title {
            display: inline-block;
            padding: 10px;
            border-radius: 10px 10px 0 0;
            @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-900);
            background: $white;
        }

        &-list {
            margin: 0;
            padding: 0;
            list-style: none;
            background-color: $white;
        }

        &-item {
            padding: 20px;
            border-bottom: 1px solid $Zinc-200;

            .product {
                column-gap: 40px;
                @media (max-width: 1410px) {
                    column-gap: 22px;
                }

                @media (max-width: 955px) {
                    display: -ms-grid;
                    display: grid;
                    grid-template-areas:
                    "a b i"
                    "a c ."
                    "a d .";
                    grid-template-columns: 98px minmax(195px, 557px) 16px;
                    column-gap: 10px;
                    row-gap: 6px;
                }

                &__number {
                    @media (max-width: 1085px) {
                        display: none;
                    }
                }

                &__cover {
                    padding: 0;
                    width: 66px;
                    height: 84px;
                    border: none;
                }

                &__book {
                    max-width: 602px;
                    margin-right: auto;

                    @media (max-width: 1445px) {
                        max-width: 343px;
                    }
                }

                &__quantity {
                    max-width: 62px;
                    width: 100%;
                }
            }
        }
    }

    &__cancel {
        display: flex;
        justify-content: flex-end;
    }

    &__pay-btn {
        margin-top: 20px;

        @media (min-width: $tablet-width) {
            display: flex;
            justify-content: flex-end;            
        }
    }
}

.order-detail__title-wrapper {
    display: flex;
    align-items: center;
    column-gap: 6px;
}

.button {
    &__cancel {
        display: flex;
        flex-wrap: nowrap;
        cursor: pointer;

        &-text {
            margin-left: 10px;
        }
    }
}

.cancel-popup {
    @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);

    &__buttons {
        margin-top: 34px;
        display: flex;
        align-items: center;
        justify-content: end;

        @media (max-width: 760px) {
            flex-direction: column-reverse;
        }
    }

    &__button {
        height: 44px;
        @include _border-radius(8px);
        font-weight: 600;

        @media (max-width: 760px) {
            width: 100%;
            margin-bottom: 10px;
        }

        &_red {
            margin-right: 10px;
            @include _button-reset(9px 16px, $Red-50, none);
            @include  _hover(bc, tc, bgi, $Red-200);

            @media (max-width: 760px) {
                margin-right: 0;
            }
        }

        &_black {
            color: $white;
            @include _button-reset(9px 16px, $Zinc-700, none);
            @include  _hover(bc, tc, bgi, $Zinc-900);
        }
    }
}

.button-cancel {
    width: 140px;
    padding-left: 28px;
    cursor: pointer;
    background: get-icon('cross-round', $Zinc-900) no-repeat left center;
    @include _typography-ext(fn, 14, 20, 600, ls, $Zinc-900);

    &__wrapper {
        display: flex;
        justify-content: flex-end;
    }
}
</style>