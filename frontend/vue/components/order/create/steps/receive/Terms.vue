<template>
	<div class="terms">
		<div class="terms__item"
             v-for="term in terms"
			 :key="term.id"
			 :class="{
			     'active': isActive(term.id),
			     'car': term.icon === 'car',
			     'plane': term.icon === 'plane'
			 }"
			 @click="setSelected(term.id)"
		>
			{{ term.title }}
		</div>
	</div>
</template>

<script>
export default {
    name: "Terms",
	data() {
        return {
            selectedIndex: 0
		}
	},
	methods: {
        setSelected(id) {
            this.selectedIndex = id;

            this.$emit('setTerms', id);
		},
        isActive(id) {
            return this.selectedIndex === id;
        }
	},
}
</script>

<style lang="scss" scoped>
.terms {
    margin: 20px 0;
    width: 293px;
    @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-900);

    &__item {
        padding: 11px 50px;
        @include _border-radius(8px);
        background-color: $Zinc-50;

        &:not(:last-child) {
            margin-bottom: 6px;
        }
        &.active {
            background-color: $Red-50;
        }

        &.car {
            background-image: get-icon('car', $Zinc-900);
            background-repeat: no-repeat;
            background-position: 10px;
        }

        &.plane {
            background-image: get-icon('plane', $Zinc-900);
            background-repeat: no-repeat;
            background-position: 10px;
        }
    }
}
</style>