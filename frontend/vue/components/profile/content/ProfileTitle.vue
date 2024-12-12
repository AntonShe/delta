<template>
	<div :class="['profile__title-min', {'profile__title-min_detail': isOrderDetail}]">
		<div v-if="isShow || isShowAlways">
			<div class="profile__title-wrapper">
                <router-link :class="['profile__link-back', {'profile__link-back_detail': isOrderDetail}]" v-if="isShow || isShowDesktop" :to="link"></router-link>
				<span>{{ title }}</span>
			</div>
		</div>
	</div>
</template>

<script>
export default {
    name: "ProfileTitle",
	props: {
        title: String,
		link: {
            type: String,
			default: '/profile'
		},
		isShowAlways: {
            type: Boolean,
            default: false
		},
        isShowDesktop: {
            type: Boolean,
            default: false
        },
        isOrderDetail: {
            type: Boolean,
            default: false
        }
	},
    data() {
        return {
            isShow: false,
        }
    },
    mounted() {
        this.onResize();
        window.addEventListener('resize', this.onResize);
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.onResize);
    },
	methods: {
        onResize() {
            this.isShow = (window.innerWidth <= 760);
        },
	},
}
</script>

<style lang="scss" scoped>
.profile__title-min {
    margin-bottom: 20px;
    @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);
}

.profile__title-wrapper {
    display: flex;
    align-items: center;
}

.profile__link-back {
    padding-left: 28px;
    height: 24px;
    display: block;
    background: get-icon('tick-left', $Zinc-900) no-repeat left;
}

// themes
.profile__title-min_detail {
    margin-bottom: 0;

    @media (max-width: 768px) {
        margin-bottom: 15px;
    }
}

.profile__link-back_detail {
    padding-left: 54px;
    background-position: 10px;

    @media (max-width: $screen-md) {
        padding-left: 28px;
        background-position: 0;
    }
}
</style>