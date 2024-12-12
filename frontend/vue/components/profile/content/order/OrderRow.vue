<template>
	<div class="order order-list__row" @click.stop="redirectToOrderDetail">
		<div class="order-list__row-covers order-list__row-item">
            <div class="order-list__covers-wrap">
                <div class="order-list__row-img"
                    v-for="product in order.products"
                    :key="product.id"
                >
                    <img :src="getCover(product)"
                        @error="setEmptyCover(product)"
                    />
                </div>
            </div>
		</div>
		<div class="order-list__row-number order-list__row-item">
            <div class="order-list__item-text">№ заказа</div>
            <div class="order-list__item-info">
			    {{ order.status.orderNumber }}                
            </div>
		</div>
		<div class="order-list__row-dateCreate order-list__row-item">
            <div class="order-list__item-text">Дата заказа</div>
            <div class="order-list__item-info">
                {{ getPrettyDate(order.status.date_create) }}
            </div>
		</div>
		<div class="order-list__row-address order-list__row-item">
            <div class="order-list__item-text">Адрес доставки</div>
            <div class="order-list__item-info">
                {{ getFormattedAddress() }}
            </div>
		</div>
		<div class="order-list__row-quantity order-list__row-item">
            <div class="order-list__item-text">Кол-во книг</div>
            <div class="order-list__item-info">
                {{ getQuantity() }}
            </div>
		</div>
		<div class="order-list__row-sum order-list__row-item">
            <div class="order-list__item-text">Сумма</div>
            <div class="order-list__item-info">
                {{ order.status.orderPrice }} ₽
            </div>
		</div>
		<div class="order-list__row-dateDelivery order-list__row-item">
            <div class="order-list__item-text">Дата доставки</div>
            <div class="order-list__item-info">
                {{ getDeliveryDate }}
            </div>
		</div>
		<div class="order-list__row-status order-list__row-item">
            <div class="order-list__item-text">Статус</div>
            <div class="order-list__item-info">
                <span v-if="$helpers.isMobileScreen() && order.status.status === 0" class="order-list__item-status">Не оплачен</span>
                <OrderPaymentStatus
                    v-else
                    :status="order.status.status"
                    :isDetail="false"
                    :isLegal="order.user.profile[0].isLegal == 1"
                    :link="getPaymentLink(order)"
                    :orderNumber="order.status.orderNumber"
                />
            </div>
		</div>
        <div class="order-list__row-item">
            <OrderPaymentStatus
                v-if="$helpers.isMobileScreen() && order.status.status === 0"
                :status="order.status.status"
                :isDetail="false"
                :isLegal="order.user.profile[0].isLegal == 1"
                :link="getPaymentLink(order)"
                :isBig="true"
                :orderNumber="order.status.orderNumber"
            />
        </div>
	</div>
</template>

<script>
import OrderPaymentStatus from "./OrderPaymentStatus.vue";

export default {
    name: "OrderRow",
    components: {OrderPaymentStatus},
	props: {
        order: {
            type: Object,
			default: null
		},
	},
    data() {
        return {
            statuses: [
                'Новый',
                'Создан',
                'Оплачен',
                'По ходу придумаем'
            ]
        }
    },
    computed: {
        getDeliveryDate() {
            if (!this.order.status || this.order.status.deliveryDate === '') return

            const deliveryDate = new Date(this.order.status.deliveryDate)
            const deliveryDay = `0${deliveryDate.getDate()}`.slice(-2)

            return `${deliveryDay}.${deliveryDate.getMonth() + 1}.${deliveryDate.getFullYear()}`
        }
    },
	methods: {
        getPaymentLink(order) {
            let idOrder = order.status.id + ''
            let middle = parseInt(Date.now() / 1000)

            return this.isLegal
                ? '/order/get-order-bill?token=' + idOrder + 'o' + (middle * order.status.id)
                : !Array.isArray(this.order.transaction) ? this.order.transaction.trans_id : '#'
        },
        redirectToOrderDetail() {
            this.$router.push({name: 'Profile', params: {page: 'orders', orderNumber: this.order.status.orderNumber}});
		},
        getPrettyDate(date) {
            let rawDate = new Date(date)

            let day = rawDate.getDate()
            let month = rawDate.toLocaleString('ru-RU', { month: 'short' })
            let hours = rawDate.getHours() < 10 ?  '0' + rawDate.getHours() :  rawDate.getHours()
            let minutes = rawDate.getMinutes() < 10 ?  '0' + rawDate.getMinutes() :  rawDate.getMinutes()

            return day + ' ' +  month + ' ' + hours + ':' + minutes
        },
        getFormattedAddress() {
            let type = this.order.delivery.type == 1 ? 'Курьер' : 'ПВЗ'
            let address  = this.order.delivery.city + ',  ' + this.order.delivery.address

            return type + ' ' + address
        },
        getQuantity() {
            let total = 0

            _.forEach(this.order.products, (product) => {
                total  += product.quantityCart
            })

            return total
        },
        getCover(product) {
            return _.isEmpty(product.cover) ? this.$emptyCoverUrl : product.cover
        },
        setEmptyCover(product) {
            product.cover = this.$emptyCoverUrl
        }
	}
}
</script>

<style lang="scss" >
.order-list__row {
    padding: 20px 10px;
    display: flex;
    column-gap: 20px;
    align-items: center;
    border-bottom: 2px solid $Zinc-200;
    background-color: $white;
    cursor: pointer;

    @media (max-width: 1610px) {
        @include _border-radius(8px);
        padding: 10px;
        flex-direction: column;
        align-items: start;
        row-gap: 10px;
        border: none;
        max-width: 800px;
        width: 100%;
    }

    &:last-child {
        border: none;
    }

    &-item {
        overflow: hidden;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);

        &:first-child {
            width: 130px;
        }

        &:nth-child(2) {
            width: 90px;
        }

        &:nth-child(3) {
            width: 110px;
            min-width: fit-content;
        }

        &:nth-child(4) {
            width: 330px;
        }

        &:nth-child(5) {
            width: 104px;
        }

        &:nth-child(6) {
            width: 70px;
        }

        &:nth-child(7) {
            width: 117px;
        }

        @media (max-width: 1610px) {
            width: 100%;
            display: flex;
            align-items: center;

            &:first-child,
            &:nth-child(2),
            &:nth-child(3),
            &:nth-child(4),
            &:nth-child(5),
            &:nth-child(6),
            &:nth-child(7) {
                width: 100%;
            }
        }

        @media (max-width: $screen-md-l) {
            font-size: 14px;
            line-height: 20px;
        }

        .order-list__item {
            &-text {
                width: 126px;
                min-width: 126px;
                margin-right: 26px;
                @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-500);
                display: none;

                @media (max-width: 1610px) {
                    display: block;
                }

                @media (max-width: $screen-md-l) {
                    margin-right: 10px;
                    width: 114px;
                    min-width: 114px;
                    font-size: 14px;
                    line-height: 20px;
                }
            }

            &-info {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;

                @media (max-width: 1610px) {
                    max-width: calc(100% - 152px);
                }

                @media (max-width: $screen-md-l) {
                    max-width: calc(100% - 124px);
                }
            }
        }
    }

    &-covers {
        overflow: hidden;
        padding: 0;

        .order-list__covers-wrap {
            display: flex;
            width: max-content;
        }

        .order-list__row-img {
            width: 38px;
            height: 56px;
            margin-right: 4px;

            img {
                height: 100%;
                object-fit: contain;
            }
        }
    }
}

.order-list__row-status {
    @media (min-width: 1611px) {
        width: 230px;
    }
}

.order-list__item-status {
    font-weight: 600;
}
</style>