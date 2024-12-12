<template>
	<div
        @mouseover="toggleTooltip"
        @mouseout="toggleTooltip"
	>
		<slot />
		<p v-if="isShow" :class="['tooltip-def', {'tooltip-def_card': isCard}]">{{ content }}</p>
	</div>
</template>

<script>

export default {
    name: "BaseTooltip",
	props: {
        content: String,
        isCard: {
            type: Boolean,
            default: false
        }
	},
	data() {
        return {
            isShow: false
		}
	},
	methods: {
        toggleTooltip() {
            if (this.$helpers.isMobileScreen()) return

            this.isShow = !this.isShow;
		}
	}
}
</script>

<style lang="scss" scoped>
.tooltip-def {
    position: absolute;
    width: max-content;
    top: 105%;
    left: 0;
    padding: 5px 10px;
    background-color: $Zinc-700;
    box-shadow: 0 3px 8px rgba(45, 77, 108, 0.15);
    z-index: 5;
    border-radius: 4px;
    @include _typography-ext(fn, 14, 20, 400, ls, $white);
}
// themes
.tooltip-def_card {
    top: -14px;
    left: 88%;
    background-color: $Zinc-500;
}
</style>