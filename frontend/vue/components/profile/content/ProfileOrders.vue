<template>
    <div class="orders">
        <ProfileTitle title="Мои заказы" :isShowAlways=true />
        <div class="orders__header-wrapper">
            <div class="orders__search">
                <div class="orders__search-input">
                    <BaseField
                        id="orderNumber"
                        name="orderNumber"
                        placeholder="Поиск по номеру заказа"
                        :isProfileOrders="true"
                        @changeInputValue="setOrderNumber"
                    />
                </div>
                <div class="orders__search-calendar">
                    <Datepicker
                        class="search-calendar"
                        v-model="searchParams.dateCreateRange"
                        :alt-position="getPosition"
                        range
                        auto-apply
                        multi-calendars
                        multi-calendars-solo
                        :enable-time-picker="false"
                        input-class-name="search-calendar__input"
                        locale="ru-Ru"
                        @update:model-value="setOrderDate"
                    />
                </div>
                
            </div>
            <OrderPaymentInformer v-if="isHasSomeOnlinePayment" />            
        </div>
  		<div v-if="!isEmpty" class="orders-table">
            <OrderList />
            <BasePagination :pager-params="pagination" @pageSelected="setPageNumber"/>
		</div>
    </div>
</template>

<script>
import {ref, provide, readonly} from "vue"
import OrderList from "./order/OrderList"
import OrderPaymentInformer from './order/OrderPaymentInformer'
import ProfileTitle from "./ProfileTitle"
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import BasePagination from "../../base/BasePagination"
import { useStore } from 'vuex'

export default {
    name: "ProfileOrders",
    components: {
        ProfileTitle,
        OrderList,
        OrderPaymentInformer,
        Datepicker,
        BasePagination
    },
	data() {
        return {
            orders: null,
			searchParams: {
                orderNumber: null,
                dateCreateRange: null,
                page: 1
			}
		}
	},
	beforeMount() {
		this.getOrderList();
    },
    setup() {
        const store = useStore()
        const orders = ref([])
        const pagination = ref({})

        const getOrderList = async (params = {}) => {
            const items = await store.dispatch('user/getUserOrders', params)

            if (!_.isEmpty(items)) {
                let dateCreate,
                    year = '',
                    month = '',
                    key = '',
                    ordersList = {}

                _.forEach(items.orders, (order) => {
                    dateCreate = new Date(order.status.date_create)
                    month = dateCreate.toLocaleString('ru-RU', { month: 'long' })
                    month = month.charAt(0).toUpperCase() + month.slice(1)
                    year = dateCreate.getFullYear()
                    key = year + ' / ' + month

                    if (ordersList[key] === undefined) ordersList[key] = []

                    ordersList[key].push(order)
                })

                orders.value = ordersList
                pagination.value = items.pagination
            }
        }

        provide('orders', readonly(orders))
        provide('pagination', readonly(pagination))

        return {
            orders,
            pagination,
            getOrderList
        }
    },
    computed: {
        isEmpty() {
            return _.isEmpty(this.orders)
        },
        isHasSomeOnlinePayment() {
            return Object.values(this.orders).flat().some(item => item.status.paymentType === 1 && item.status.status === 0 && item.status.statusPayment === 0)
        }
    },
    methods: {
		setOrderNumber(value) {
            this.searchParams.orderNumber = value;
            this.checkOrderNumber()
            this.getOrderList(this.searchParams);
		},
        setOrderDate(value) {
            this.searchParams.dateCreateRange = _.isEmpty(value) ? '' : JSON.stringify(value);
            this.checkOrderNumber()
            this.getOrderList(this.searchParams);
		},
        setPageNumber(value) {
            this.searchParams.page = value
            this.checkOrderNumber()
            this.getOrderList(this.searchParams)
        },
        checkOrderNumber() {
            if (!this.searchParams.orderNumber) {
                delete this.searchParams.orderNumber
            }
        }
	}
}
</script>

<style lang="scss">
.orders__title {
    margin-bottom: 10px;
    @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);
}

.orders__search-input {
    position: relative;
    width: 100%;

    @media (min-width: $screen-xl-l) {
        width: 306px;
    }

    &::before {
        content: '';
        display: block;
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        background: get-icon('search', $Zinc-400) no-repeat center;
        z-index: 1;
    }
}

.orders__search-calendar {
    width: 44px;
    height: 44px;
    align-items: center;
    justify-content: center;
    display: none;

    @media (min-width: $tablet-width) {
        display: flex;
    }
}

.orders__search {
    display: flex;
    gap: 20px;
}

.orders__header-wrapper {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;

    @media (min-width: $screen-xl-l) {
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-end;
        gap: 20px;
    }
}

.search-calendar__input {
    padding: 12px;
    width: 44px;
    height: 44px;    
    opacity: 0;
}

.dp__input_icon {
    width: 24px;
    height: 24px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: $Zinc-400;
}

.dp__clear_icon {
    right: -35px!important;
}
</style>