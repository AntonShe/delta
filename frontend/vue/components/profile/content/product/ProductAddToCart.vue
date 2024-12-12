<template>
	<div class="remove_from_cart_button">
		<div class="" v-if="isCart">
            <button @click="removeFromCart">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 13L13 1M1 1L13 13" stroke="#18181B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
		</div>
		<div v-else>
            <button
                    v-if="inCart"
                    @click="removeFromCart"
                    class="mini-card__button mini-card__button-tocart button-tocart"
            >
                В корзине (галочка)
            </button>
            <button
                    v-else
                    @click="addToCart"
                    class="mini-card__button mini-card__button-tocart button-tocart"
                    :class="{'loading' : loading}"
            >
                В корзину
            </button>
		</div>
	</div>

</template>

<script>
export default {
    name: "ProductAddToCart",
	props: {
        inCart: {
            type: Boolean,
			default: false
		},
        isCart: {
            type: Boolean,
            default: false
        },
        loading: {
            type: Boolean,
            default: false
        },
	},
	methods: {
        addToCart() {
            this.$emit('add');
		},
		removeFromCart() {
            this.$emit('remove');
		}
	}
}
</script>

<style scoped>

</style>