<template>
	<div class="order-list">
		<div class="order-list__head">
			<div class="order-list__head-item"
				v-for="(title, index) in headConfig"
				:key="index"
			>
				{{ title }}
			</div>
		</div>
		<div class="order-list__body">
			<div class="order-list__item"
				v-for="(orderGroup, period) in orderList"
                :key="period"
			>
                <div class="order-list__item-period">
                    {{ period }}
                </div>
                <order-row
                    v-for="order in orderGroup"
                    :key="order.status.orderNumber"
                    :order="order"
                />
			</div>
		</div>
	</div>
</template>

<script>
import OrderRow from "./OrderRow"
import {inject} from "vue"

export default {
    name: "OrderList",
    components: {
        OrderRow
    },
	data() {
        return {
            headConfig: [
                '',
				'№ заказа',
				'Дата заказа',
				'Адрес доставки',
				'Кол-во книг',
				'Сумма',
				'Дата доставки',
				'Статус',
			]
		}
	},
    setup() {
        const orderList = inject('orders')
        const pagination = inject('pagination')

        return {
            orderList,
            pagination
        }
    }
}
</script>

<style lang="scss">
.order-list {
    &__head {
        display: flex;
        align-items: center;
        height: 44px;

        @media  (max-width: 1610px) {
            display: none;
        }

        &-item {
            padding: 9px 10px;
            @include _typography-ext(fn, 16, 26, 40, ls, $Zinc-500);
            line-height: unset;

            &:first-child {
                width: 150px;
            }

            &:nth-child(2) {
                width: 112px;
            }

            &:nth-child(3) {
                width: 130px;
            }

            &:nth-child(4) {
                width: 346px;
            }

            &:nth-child(5) {
                width: 124px;
            }

            &:nth-child(6) {
                width: 90px;
            }

            &:nth-child(7) {
                width: 137px;
            }

            &:nth-child(8) {
                width: 230px;
            }
        }
    }

    &__body {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;

        @media (min-width: 1609px) {
            margin-bottom: 0;
            gap: 10px;
            position: relative;
            top: -56px;
            z-index: 1;
        }
    }
}

.order-list__item {
    display: flex;
    flex-direction: column;
    row-gap: 20px;

    @media (min-width: $tablet-width) {
        display: block;
        border-radius: 0 0 8px 8px;
        overflow: hidden;
    }

    &-period {
        margin-top: 10px;
        padding: 10px;
        width: 150px;
        height: 100%;
        border-radius: 8px 8px 0 0;
        background-color: $white;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-500);

        @media (max-width: 1610px) {
            display: none;
        }
    }
}

.thead {
    display: flex;

    &__cell {
        margin-right: 20px;
        width: 150px;
    }
}
</style>