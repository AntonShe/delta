<template>
	<div class="addres-card">
		<div class="addres-card__address">{{ getAddress }}</div>
        <div
            class="addres-card__wrap" 
            :class="[{'courier': isCourier && deliveryProfile.courier.date}, {'no-delivery': isCourier && !deliveryProfile.courier.date}]"
        >
            <template v-if="(isCourier && deliveryProfile.courier.date) || !isCourier">
                <div class="addres-card__data">{{ getDate }}</div>
                <div class="addres-card__price">{{ getPrice }} &#8381;</div>
            </template>
            <template v-else>
                По данному адресу доставки нет.
            </template>
        </div>
		<button class="addres-card__btn" @click="emitChange">Изменить</button>
	</div>
</template>

<script>
import { mapState } from 'vuex'

export default {
    name: "AddressCard",
    props: {
        deliveryType: Number
    },
	methods: {
        emitChange() {
            this.$emit('changeCard');
		}
	},
	computed: {
        ...mapState({
            deliveryProfile: state => state.order.deliveryProfile
        }),
        isCourier() {
            return (this.deliveryType == 1);
		},
        getAddress() {
            return this.isCourier
                ? this.deliveryProfile.courier.address
                : this.deliveryProfile.point.address
        },
        getDate() {
            let rawDate = this.isCourier
                ? new Date(this.deliveryProfile.courier.date)
                : new Date(this.deliveryProfile.point.date)

            return rawDate.toLocaleDateString('ru-Ru', { day: 'numeric', month: 'long' }) + ' ('
                + rawDate.toLocaleString('ru-Ru', {weekday: 'short'}) + ')'
        },
        getPrice() {
            return this.isCourier
                ? this.deliveryProfile.courier.price
                : this.deliveryProfile.point.price
        },
	}
}
</script>

<style lang="scss" scoped>
.addres-card {
    padding: 10px 20px;
    max-width: 306px;
    width: 100%;
    @include _border-radius(8px);
    background-color: $Zinc-50;
    border: 1px solid $Zinc-200;

    &:hover {
        border-color: $Indigo-600;
    }

    &__address {
        margin-bottom: 6px;
    }

    &__wrap {
        display: flex;
        padding-left: 40px;
        margin-bottom: 6px;
        background: get-icon('l-post') no-repeat left;
        @include _typography-ext(fn, 16, 26, 600, ls, $Green-600);

        &.courier {
            background: get-icon('car', $Green-600) no-repeat left;
        }

        &.no-delivery {
            @include _typography-ext(fn, 14, 22, 600, ls, $Rose-600);
            padding-left: 0;
            background-image: none;
        }
    }

    &__data{
        margin-right: 16px;
    }

    &__btn {
        cursor: pointer;
        @include _button-reset(0, inherit, none);
        @include _typography-ext(fn, 12, 16, 600, ls, $Zinc-500);

        &:hover {
            color: $Red-400;
        }
    }
}
</style>