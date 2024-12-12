<template>
	<div>
		<div class="order-step-title" :class="{'active': isActive}">
            {{ number }} | {{ title }}
		</div>
	</div>
</template>

<script>
export default {
    name: "OrderStepTitle",
	props: {
        title: String,
        number: Number,
        isActive: {
            type: Boolean,
            default: false,
		},
	}
}
</script>

<style lang="scss" scoped>
.order-step-title {
    padding: 10px 10px 10px 20px;
    width: max-content;
    background-color: $white;
    @include _typography-ext(fn, 24, 32, 600, ls, $Zinc-400);
    @include _border-radius(8px 8px 0 0);

    @media (max-width: $screen-xl) {
        @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-400);
    }
    
    &.active {
        color: $Red-400;
    }
}
</style>