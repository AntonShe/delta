<template>
	<div class="order-step order-step--payment" v-if="isLoaded">
        <OrderStepTitle :number="3" title="Способ оплаты" :is-active="true"/>
		<div class="order-payment">
			<!-- <div v-if="Array.isArray(userInfo.company) && (+userInfo.company[0].userId === 414213 || +userInfo.company[0].userId === 413099)" class="order-payment__wrapper"> -->
            <div class="order-payment__wrapper">
                <button class="order-payment__btn" v-if="userInfo.isLegal == 0"
                        :class="{'order-payment__btn_active': paymentType == 1}"
                        @click.prevent="setPayment(1)">{{ getTitle }}</button>

                <button class="order-payment__btn" v-if="userInfo.isLegal == 1"
                        :class="{'order-payment__btn_active': paymentType == 3}"
                        @click.prevent="setPayment(3)">{{ getTitle }}</button>

				<div class="order-payment__btn-dropdown" v-if="false">
					<div class="order-payment__dropdown-checkbox">
						<BaseCheckbox
								id="bonus"
								name="bonus"
                                @updateValue="setAllBonusWithdraw"
								:value="false"
						/>
<!--                        :label="getCheckboxLabel"-->
					</div>
					<div class="order-payment__dropdown-input">
                        <BaseField
                                id="bonus"
                                name="bonus"
                                placeholder="Другая сумма"
                                @updateInputValue="setBonusWithdraw"
                        />
                        <button class="order-payment__dropdown-btn" @click="acceptBonus">
                            Применить
                        </button>
					</div>
				</div>

			</div>

            <button class="order-payment__btn" v-if="userInfo.isLegal == 0"
                    :class="{'order-payment__btn_active': paymentType == 2}"
                    @click.prevent="setPayment(2)">Оплата при получении заказа</button>
		</div>
	</div>
</template>

<script>
import OrderStepTitle from "../../OrderStepTitle.vue"
import {inject} from "vue"
import { mapMutations, mapState } from 'vuex'

export default {
    name: "Payment",
    components: {OrderStepTitle},
	props: {
        defaultType: Number
	},
	data() {
        return {
            paymentType: this.defaultType,
      		title: {
                  legal: 'Оплата по счету',
                  physical: 'Банковской картой на сайте',
			},
			bonusWithdraw:0,// this.bonus.quantity,
            isShowBonusWithdraw: false,
		}
	},
    setup() {
        const userInfo = inject('userInfo', {})

        return {
            userInfo
        }
    },
	methods: {
        ...mapMutations({
            setOrderData: 'order/setOrderData'
        }),
        setAllBonusWithdraw() {
      		//this.bonusWithdraw = this.bonus.quantity;
		},
        setBonusWithdraw(value) {
            this.bonusWithdraw = value;
        },
        acceptBonus() {
            this.isShowBonusWithdraw = false;
            this.$emit('setBonus', this.bonusWithdraw);
		},
        setPayment(val) {

            if (this.paymentType !== val) {
                this.paymentType = val

                this.setOrderData([{
                    item: 'paymentType',
                    value: this.paymentType,
                }])

                let paymentLabel = this.title.legal;

                if (this.userInfo.isLegal !== 1 && val !== 1) {
                    paymentLabel = val === 2 ? 'Оплата при получении заказа' : this.title.physical;
                }

                dataLayerPaymentInfo(Object.values(this.cart.items), this.cart.final_price, '', paymentLabel,false)
            }
		}
	},
    mounted() {
        this.setOrderData([{
            item: 'paymentType',
            value: this.defaultType,
        }])
    },
    watch: {
        cart(newVal) {
            if (newVal) {
                let paymentLabel = this.title.legal;

                if (this.userInfo.isLegal !== 1 && this.defaultType !== 1) {
                    paymentLabel = this.defaultType === 2 ? 'Оплата при получении заказа' : this.title.physical;
                }

                dataLayerPaymentInfo(Object.values(this.cart.items), this.cart.final_price, '', paymentLabel,false)
            }
        },
    },
    computed: {
        ...mapState({
            cart: state => state.cart.cartData
        }),
        isLoaded() {
            return !_.isEmpty(this.userInfo)
        },
        getTitle() {
			return this.isLoaded && this.userInfo.isLegal == 1 ? this.title.legal : this.title.physical;
		},
        isShow() {
			//return ((this.bonus.quantity > 0) && this.isShowBonusWithdraw);
		},
        getCheckboxLabel() {
            //return 'Списать ' + this.bonus.quantity + 'Р с баланса';
		},
	}
}
</script>

<style lang="scss">
.order-payment {
    display: flex;
    margin-top: 20px;
    gap: 20px;

    @media  (max-width: $screen-md) {
        flex-direction: column;
        gap: 10px;
    }

    &__btn {
        display: flex;
        align-items: center;
        gap: 8px;
        width: auto;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
        @include _button-reset(20px 40px, $white, 2px solid $Zinc-200);
        text-align: start;
        transition: none;

        @media (max-width: $screen-md) {
            padding: 20px 30px;
            width: 100%;
        }

        &::before {
            content: '';
            display: block;
            min-width: 20px;
            width: 20px;
            height: 20px;
            border-radius: 100%;
            border: 1px solid $Red-400;
        }

        &_active {
            border-color: $Red-200;

            &::before {
                border: none;
                background: get-icon('radio-btn', $Red-400);
            }
        }

        &-dropdown {
            width: 286px;
            padding: 10px;
            position: absolute;
            z-index: 2;
            background-color: $white;
            @include _border-radius(8px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    }

    &__dropdown {
        &-checkbox {
            padding: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid $Zinc-200;
            @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-900);
        }

        &-input {
            display: flex;
        }

        &-btn {
            margin-left: 18px;
            height: 36px;
            @include _typography-ext(fn, 14, 20, 400, ls, $white);
            @include _button-reset(9px 12px, $Zinc-700, none);
            @include  _hover(bc, tc, bgi, $Zinc-900);
        }
    }
}
</style>