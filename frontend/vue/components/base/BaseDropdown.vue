<template>
	<div v-if="items" class="base-dropdown" :class="className">
		<div class="button" @click="toggleMenu">
            <slot name="button"></slot>
		</div>
        <div v-if="isShow" class="base-dropdown__list">
            <div 
                v-for="(item, key) in items"
                class="base-dropdown__item" 
                :key="key"
            >
                <a target="_blank" :href="item.link">{{ item.title }}</a>
            </div>
        </div>
	</div>
</template>

<script>
export default {
    name: "BaseDropdown",
	props: {
        items: {
            type: Object,
			default: null
		},
        className: {
            type: String,
            default: ''
        }
	},
	data() {
        return {
            isShow: false
		}
	},
	methods: {
        toggleMenu() {
            this.isShow = !this.isShow;
		}
	}
}
</script>

<style lang="scss" scoped>
.base-dropdown {
    @include _border-radius(8px);
    width: 100%;
    background-color: $white;
    box-shadow: 0 15px 20px rgba(0, 0, 0, 0.05);
    transition: backgroung .2s ease-in-out;
    overflow: hidden;
    position: relative;

    &__list {
        padding: 4px;
        @include _border-radius(8px);
    }

    &__item {
        padding: 5px 16px;
        @include _border-radius(8px);
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);

        &:hover {
            background-color: $Red-50;
        }

        &:active {
            background-color: $Red-200;
        }
    }
}
</style>