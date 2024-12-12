<template>
	<div>
        <div class="title-wrapper">
            <ProfileTitle title="Оформление заказа" :isShowAlways="true" :isShowDesktop="true" link="/cart" />
        </div>
		<div class="order">
			<div class="order__left">
				<order-steps />
			</div>
			<div class="order__right">
				<order-price v-if="isLoaded" @confirmOrder="submitOrder"/>
			</div>
		</div>
	</div>
</template>

<script>
import ProfileTitle from "../../profile/content/ProfileTitle"
import OrderPrice from "./OrderPrice"
import OrderSteps from "./OrderSteps"
import {provide, reactive, readonly, ref} from "vue"
import { useRouter } from 'vue-router'
import axios from "axios"
import BaseSvgStore from "../../base/BaseSvgStore"
import { mapState, mapActions } from 'vuex'

export default {
    name: "CreateOrder",
    components: {
        BaseSvgStore,
        OrderSteps,
        OrderPrice,
        ProfileTitle
    },
    emits: ['orderCreated'],
    setup() {
        const router = useRouter()

        const orderNumber = ref(0)
        const setOrderNumber = (id) => {
            orderNumber.value = id
        }

        return {
            orderNumber,
            setOrderNumber,
        }
    },
    beforeMount() {
        this.getCityList()
        this.loadingCartData()

        this.reloader = setInterval(() => {
            this.loadingCartData()
        }, 10000)

        localStorage.removeItem('pin')
        localStorage.removeItem('unregistrationPhone')
    },
    computed: {       
        ...mapState({
            orderData: state => state.order.orderData,
            orderConfirm: state => state.order.orderConfirm,
            cartData: state => state.cart.cartData
        }),
        isLoaded() {
            return !_.isEmpty(this.cartData)
        }
    },
    methods: {
        ...mapActions({
            getCityList: 'order/getCityList',
            getCartData: 'cart/getCartData',
            getCurrentCartCount: 'cart/getCurrentCartCount',
        }),
        async loadingCartData() {
            const isData = await this.getCartData()

            if (!isData) return

            if (!_.isEmpty(this.cartData.items)) {
                dataLayerBeginCheckout(Object.values(this.cartData.items), this.cartData.final_price, '', false)
            } else if (this.orderNumber === 0) {
                this.$router.push({
                    name: 'Cart'
                })
            }
        },
        submitOrder() {
            let isValid = true

            _.forEach(this.orderConfirm, (item) => {
                if (!item) isValid = false
            })

            _.forEach(this.orderData, (item) => {
                if (_.isNull(item)) isValid = false
            })

            if (isValid) {
                clearInterval(this.reloader)
                this.reloader = null
                this.orderData.carId = this.cartData.id

                axios
                    .post('/order', {
                        orderParams: this.orderData,
                        userData: this.getDataForNewUser()
                    })
                    .then( response => {
                        if (response.data.id === 0) {
                            alert("Ошибка оформления заказа :(\nПопробуйте позже")
                            this.reloader = setInterval(() => {
                                this.getCart()
                            }, 10000)

                            throw new Error('Ошибка оформления заказа')
                        } else {
                            this.setOrderNumber(response.data.id)
                            this.$emit('orderCreated', response.data.id)
                            this.getCurrentCartCount();

                            const deliveryPrice = this.orderData.deliveryPrice ? this.orderData.deliveryPrice : 0;
                            dataLayerPurchase(response.data.id, Object.values(this.cartData.items), this.cartData.final_price, deliveryPrice, '', false);
                        }
                    })
            } else {
                console.log('Что-то пошло не так.')
            }
        },
        getDataForNewUser() {
            return {
                phone: localStorage.getItem('unregistrationPhone'),
                firstName: localStorage.getItem('firstName'),
                secondName: localStorage.getItem('secondName'),
                lastName: localStorage.getItem('lastName'),
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.title-wrapper {
    display: flex;
    flex-wrap: nowrap;
    justify-content: flex-start;
}

.back-btn {
    cursor: pointer;
}

.order {
    display: flex;
    justify-content: space-between;

    @media (max-width: $screen-xl-l) {
        flex-direction: column;
    }    

    &__left {
        margin-right: auto;
        width: calc(100% - 156px - 388px);

        @media (max-width: $screen-xl-l) {
            width: 100%;
        }
    }

    &__right {
        margin-top: 40px;
    }
}
</style>