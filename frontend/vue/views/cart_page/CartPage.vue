<template>
	<Cart v-if="isLoaded" />
</template>

<script>
import Cart from "../../components/cart/Cart"
import axios from "axios"
import { mapActions, mapState } from 'vuex'

export default {
    name: "CartPage",
    components: {
        Cart
    },
    data() {
        return {
            checker: null
        }
    },
    beforeMount() {
        this.getCartData()
    },
    mounted() {
        this.cartUpdateChecker()
    },
    computed: {
        ...mapState({
            cartData: state => state.cart.cartData
        }),
        isLoaded() {
            return !_.isNull(this.cartData)
        }
    },
	methods: {
        ...mapActions({
            getCartData: 'cart/getCartData'
        }),
        cartUpdateChecker() {
            var cartObj = this
            setInterval(function() {
                if (!_.isNull(cartObj.cartData)) {
                    axios.get('/cart/get-cart')
                        .then(result => {
                            if (result.data.date_update != cartObj.cartData.date_update) {
                                cartObj.cartData = result.data
                            }
                        })
                }
            }, 20000)
        }
	},
}
</script>

<style scoped>

</style>