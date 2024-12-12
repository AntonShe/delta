<template>
	<div class="bg-body">
		<Order />
	</div>
</template>

<script>
import Order from "../../components/order/Order"

export default {
    name: "OrderPage",
    components: {
        Order
    }
}
</script>

<style lang="scss" scoped>
.bg-body {
    background-color: #FAFAFA;
}
</style>