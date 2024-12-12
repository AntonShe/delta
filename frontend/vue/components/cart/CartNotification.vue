<template>
    <div class="notification flex">
        <div class="notification__icon"></div>
        <div class="notification__text">
			{{ text }}
        </div>
        <div class="notification__cross" @click="close" v-if="isCanClose"></div>
    </div>
</template>

<script>
export default {
    name: "CartNotification",
	props: {
        text: String,
		isCanClose: {
            type: Boolean,
			default: true
		}
	},
	methods: {
        close() {
            this.$emit('close');
		}
	}
}
</script>

<style lang="scss" scoped>
.notification {
    margin-bottom: 20px;
    align-items: center;

    &__icon {
        margin-right: 6px;
        width: 20px;
        height: 20px;
        background: get-icon('notification', $Rose-600) no-repeat center;
    }

    &__cross {
        width: 20px;
        height: 20px;
        background: get-icon('cross', $Zinc-400) no-repeat center;
        transition: background .2s ease-in-out;
        cursor: pointer;

        &:hover {
            background: get-icon('cross', $Zinc-900) no-repeat center;
        }
    }

    &__text {
        margin-right: 6px;
        @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-900);
    }
}
</style>