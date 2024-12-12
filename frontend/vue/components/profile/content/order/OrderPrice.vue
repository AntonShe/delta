<template>
	<div>
		<div class="row">
			<div class="left">
				{{ getItemLabel }}
			</div>
			<div class="right">{{ price.items }} &#8381;</div>
		</div>
        <div class="row">
            <div class="left">Доставка</div>
            <div class="right">{{ price.delivery }} &#8381;</div>
        </div>
        <div class="row row_color">
            <div class="left">Итого</div>
            <div class="right">{{ price.items + price.delivery }} &#8381;</div>
        </div>
		<div class="button" v-if="link">
            <router-link :to="'//' + link">
                {{ getButtonTitle }}
            </router-link>
		</div>
	</div>
</template>

<script>

export default {
    name: "OrderPrice",
	props: {
        status: String,
        quantity: Number,
        price: Object,
        link: {
            type: String,
			default: null
		},
		isLegal: {
            type: Boolean,
			default: false
		}
    },
	data() {
        return {
      		labels: ['товар', 'товара', 'товаров'],
			button: {
                  title: {
                      legal: 'Оплатить заказ',
                      physical: 'Счет для оплаты',
				  }
			}
		}
	},
	methods: {
        getLabel(number) {
            const n = Math.abs(Number(number)) % 100;

            const n1 = n % 10;

            if (n > 10 && n < 20) {
                return this.labels[2];
            }

            if (n1 > 1 && n1 < 5) {
                return this.labels[1];
            }

            if (n1 === 1) {
                return this.labels[0];
			}

            return this.labels[2];
		}
	},
	computed: {
        getItemLabel() {
			return (this.quantity + ' ' + this.getLabel(this.quantity));
		},
        getButtonTitle() {
            return (this.isLegal) ? this.button.title.legal :  this.button.title.physical;
        }
	}
}
</script>

<style lang="scss" scoped>
.row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-500);

    &_color {
        color: $Zinc-900;
        margin: 0;
    }
}

.button {
    margin-top: 34px;
    height: 56px;
    @include _button-reset(16px 24px, $Zinc-700, none);
    @include _typography-ext(fn, 18, 24, 600, ls, $white);
    @include  _hover(bc, tc, bgi, $Zinc-900);
}
</style>