<template>
	<div class="container order">
		<div v-if="orderSuccess && orderNumber > 0">
			<OrderSuccess :order-number="orderNumber" />
		</div>
		<div v-else-if="orderInfo">
			<create-order :order="orderInfo" @orderCreated="changeView"/>
		</div>
	</div>
</template>

<script>
import OrderSuccess from "./OrderSuccess"
import CreateOrder from "./create/CreateOrder"

export default {
    name: "Order",
    components: {
        CreateOrder,
        OrderSuccess
    },
	data() {
		return {
            orderSuccess: false,
			orderInfo: false,
            orderNumber: 0
		}
	},
	beforeMount() {
        if (this.$route.params.page === 'success') {
            this.orderSuccess = true
		} else if (!this.$route.params.page) {
            this.orderInfo = true
		}
    },
    methods: {
        changeView(id) {
            this.orderInfo = false
            this.orderNumber = id
            this.$route.params.page = 'success'
            this.orderSuccess = true
            window.scrollTo(0, 0);
        }
    }
}
</script>

<style lang="scss" scoped>
.order {
    padding-bottom: 50px;

    @media (max-width: $screen-md) {
        padding-top: 66px;
        overflow-x: hidden;
    }
}

</style>