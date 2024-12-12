<template>
    <div class="cart-list__remove">
        <div class="cart-list__remove-checkbox">
            <base-checkbox @updateValue="checkAll" :value="isAllRemove"/>
        </div>
        <div>
			<base-tooltip content="Удалить выбранное">
                <button class="cart-list__remove-button" @click="removeSelected"></button>
			</base-tooltip>
        </div>
    </div>

    <ul class="cart-list__products">
        <li class="cart-list__product" v-for="(product, index) in productList"
            :key="index">
            <Product :product="product"
                     :isRemove="isRemoveChecked(product)"
                     @changeRemoveList="changeRemoveList"
            />
        </li>
    </ul>
</template>

<script>
import Product from "./Product"
import BaseField from "../base/BaseField"
import BaseTooltip from "../base/BaseTooltip"
import BaseCheckbox from "../base/BaseCheckbox"
import { mapActions } from 'vuex'

export default {
    name: "ProductList",
    components: {
        BaseCheckbox,
        BaseTooltip,
        BaseField,
        Product
    },
	props: {
        productList: {
            type: Object,
			default: null
		},
	},
	data() {
        return {
            forRemove: [],
            isAllRemove: false,
            isShowTooltip: false
		}
	},
	methods: {
        ...mapActions({
            removeItems: 'cart/removeItems'
        }),
        isRemoveChecked(product) {
            return !!this.forRemove.find(item => item.id === product.id)
        },
        removeSelected() {
            const arrProductId = this.forRemove.map(item => {
                dataLayerRemoveCart({
                    ...item.productInfo,
                    list_id: 'cart',
                    list_name: 'Корзина',
                }, item.productInfo.id, false, item.quantity.cart);

                return item.id;
            })
            this.removeItems({ids: arrProductId, isCart: true})
		},
        checkAll(value) {
            if (value) {
                _.forEach(this.productList, (item) => {
                    this.addForRemove(item)
                })
			} else {
                this.forRemove = []
			}
		},
        changeRemoveList(value) {
            if (value.isAdd) {
                this.addForRemove(value.product)
			} else {
                this.forRemove = this.forRemove.filter(product => product.id !== value.id)
			}
		},
		addForRemove(product) {
            if (! this.isRemoveChecked(product)) {
                this.forRemove.push(product)
            }
		},
        toggleTooltip() {
            this.isShowTooltip = !this.isShowTooltip
		}
	}
}
</script>

<style lang="scss">
.cart-list {
    &__remove {
        position: relative;
        margin-bottom: 10px;
        padding-left: 22px;
        height: 44px;
        display: flex;
        align-items: center;

        @media (max-width: $screen-xl) {
            padding-left: 0;
        }

        @media (max-width: $screen-md) {
            display: none;
        }        

        &-checkbox {
            margin-right: 10px;
        }

        &-button {
            width: 44px;
            height: 44px;
            background: get-icon('trash', $Zinc-400) no-repeat center;
            @include _button-reset(p, bgc, none);
            @include  _hover(bc, tc, get-icon('trash', $Zinc-900) no-repeat center, bgc);
        }
    }

    &__products {
        background-color: $white;
        box-shadow: inset 0 -1px 0 $Zinc-200, inset 0 1px 0 $Zinc-200;

        .cart-list__product {
            padding: 20px 42px 20px 22px;

            @media (max-width: $screen-xl) {
                padding: 10px 0;
            }

            @media (max-width: $screen-md) {
                margin-bottom: 20px;
                border-bottom: 1px solid #E4E4E7;
                border-top: 1px solid #E4E4E7;
            }
            
            &:not(:last-child) {
                border-bottom: 1px solid $Zinc-200;
            }
        }
    }
}
</style>