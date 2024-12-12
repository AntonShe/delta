<template>
    <div class="favourite-card__button favourite-card__button-count button-tocart counter" data-counter>
        <button class="favourite-card__button-count_minus counter__button counter__button_minus"
                @click="decrement"
        ></button>
        <div class="favourite-card__button-count-input counter__input">
            <input type="text" readonly :value="quantity">
        </div>
        <button 
            class="favourite-card__button-count_plus counter__button counter__button_plus"
            :class="{'disabled': isCountDisabled}"
            @click="increment"
        ></button>
    </div>
</template>

<script>
export default {
    name: "ProductQuantity",
	props: {
        quantity: {
            type: Number,
            required: true
        },
        stock: {
            type: Number,
            required: true
        }
	},
    emits: ['changeQuantity'],
    computed: {
        isCountDisabled() {
            return !(this.quantity < this.stock)
        }
    },
	methods: {
        decrement() {
            this.$emit('changeQuantity', this.quantity - 1);
		},
        increment() {
            this.$emit('changeQuantity', this.quantity + 1);
        },
	}
}
</script>

<style scoped>

</style>