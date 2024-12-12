<template>
	<div class="order-step">
		<OrderStepTitle :number="1" title="Способ получения" :is-active="true"/>
		<div class="receive-type__wrap" v-show="isShowCity">
            <base-autocomplete
                :default-value="getDefaultCity"
                :items="cityList"
                :isRequired="true"
                :class="{
                    'accepted': isAccepted,
                }"
                id="city"
                name="city"
                placeholder="Город*"
                @setValue="setCity"
                @changeValue="clearCity"
                @cleared="clearCity"
            />

            <base-radio-group
                v-if="city != ''"
                :list="receiveTypes"
                :default-value="getDeliveryType"
                @onChanged="setDeliveryType"/>
		</div>
		<div class="receive__delivery-address" v-if="isShowTerms">
            <DeliveryAddress
                :receiveType="receiveType"
                :city="city"
                :comment="comment"
                @profileSave="reloadProfiles"
                @cancel="cancelChange"
            />
		</div>
        <div class="receive__address-card" v-if="issetCard && !isShowTerms">
			<AddressCard :delivery-type="parseInt(receiveType)" @changeCard="changeCard"/>
        </div>
        <div class="receive__add-address" v-if="!issetCard && !isShowTerms && isAccepted && typeSelected">
            <add-new @clicked="changeCard"/>
        </div>
	</div>
</template>

<script>
import OrderStepTitle from "../../OrderStepTitle"
import Terms from "./Terms"
import AddressCard from "./AddressCard"
import DeliveryAddress from "./DeliveryAddress"
import BaseAutocomplete from "../../../../base/BaseAutocomplete"
import BaseRadioGroup from "../../../../base/BaseRadioGroup"
// import {inject} from "vue"
import AddNew from "./AddNew"
import { mapActions, mapMutations, mapState } from 'vuex'

export default {
    name: "Receive",
    components: {
        AddNew,
        BaseAutocomplete,
        DeliveryAddress,
        AddressCard,
        Terms,
        OrderStepTitle,
        BaseRadioGroup
    },
    // setup() {
    //     const cart = inject('cart')

    //     return {
    //         cart
    //     }
    // },
    emits: ['setDeliveryType'],
	data() {
        return {
            receiveTypes: [
                {
                    label: 'Курьер',
                    value: 1,
                    id: 'courier',
                    name: 'courier',
                },
                {
                    label: 'В пункт выдачи',
                    value: 2,
                    id: 'pvz',
                    name: 'pvz',
                },
			],
            receiveType: null,
            isShowTerms: false,
            isShowCity: true,
            city: '',
            comment: ''
		}
	},
	computed: {
        ...mapState({
            cityList: state => state.order.cityList,
            deliveryProfile: state => state.order.deliveryProfile,
            cart: state => state.cart.cartData
        }),
        isEmpty() {
            return _.isEmpty(this.deliveryProfile.courier) && _.isEmpty(this.deliveryProfile.point)
        },
        issetCard() {
            if (this.getDeliveryType == 1) {
                return !_.isEmpty(this.deliveryProfile.courier)
            } else {
                return !_.isEmpty(this.deliveryProfile.point)
            }
        },
        isAccepted() {
            return !_.isEmpty(this.city)
        },
        typeSelected() {
            return this.receiveType !== null
        },
        getDefaultCity() {
            let city = ''
            let rawCity

            if (!this.isEmpty) {
                if (this.getDeliveryType == 1 && !_.isEmpty(this.deliveryProfile.courier)) {
                    rawCity = this.deliveryProfile.courier.address.split(',')
                } else if (!_.isEmpty(this.deliveryProfile.point)) {
                    rawCity = this.deliveryProfile.point.address.split(',')
                } else {
                    return !_.isEmpty(this.city) ? this.city : ''
                }

                city = rawCity[0]
                this.city = rawCity[0]

                this.setDeliveryType(this.getDeliveryType)
            }

            return city
        },
        getDeliveryType() {
            if (!_.isNull(this.receiveType)) return this.receiveType

            let storedType = localStorage.receiveType

            if (!_.isEmpty(storedType)
                && (
                    (storedType == 1 && !_.isEmpty(this.deliveryProfile.courier))
                    || (storedType == 2 && !_.isEmpty(this.deliveryProfile.point))
                )
            ) return storedType

            let type = null

            if (!this.isEmpty) {
                type = !_.isEmpty(this.deliveryProfile.courier) ? 1 : 2
                this.receiveType = type
            }

            return type
        },
	},
    methods: {
        ...mapMutations({
            setOrderData: 'order/setOrderData'
        }),
        ...mapActions({
            getDeliveryProfile: 'order/getDeliveryProfile'
        }),        
        setDeliveryType(value) {
            let deliveryType = value == 1 ? 'courier' : (value == 2 ? 'point' : null)
            this.receiveType = value;
            localStorage.receiveType = value

            if (!_.isNull(deliveryType) && !_.isEmpty(this.deliveryProfile[deliveryType])) {
                this.$emit('setDeliveryType', deliveryType)
                this.setOrderData([{
                        item: 'deliveryProfileId',
                        value: this.deliveryProfile[deliveryType].id
                    },
                    {
                        item: 'deliveryPrice',
                        value: this.deliveryProfile[deliveryType].price
                    },
                    {
                        item: 'deliveryDate',
                        value: this.deliveryProfile[deliveryType].date
                    },
                    {
                        item: 'deliveryType',
                        value: this.deliveryProfile[deliveryType].type
                    },
                    {
                        item: 'courierComment',
                        value: this.comment
                    }
                ])

                if (!_.isEmpty(this.cart)) {
                    const shippingTier = value === 1 ? 'Курьер' : (value === 2 ? 'В пункт выдачи' : null);
                    dataLayerAddShippingInfo(Object.values(this.cart.items), this.cart.final_price, shippingTier, '', false);
                }
            }
		},
        setCity(city) {
            this.city = city
        },
        clearCity() {
            this.city = ''
        },
        reloadProfiles(comment) {
            this.isShowTerms = false
            this.isShowCity = !this.isShowCity
            this.comment = comment
            this.getDeliveryProfile()
        },
        changeCard() {
            this.isShowCity = !this.isShowCity
            this.isShowTerms = true
        },
        cancelChange() {
            this.isShowCity = !this.isShowCity
            this.isShowTerms = false
        }
	},
}
</script>

<style lang="scss">
.receive-type {
    &__wrap {
        width: 100%;
        max-width: 524px;
        padding: 20px;
        background-color: $white;
        display: flex;
        flex-direction: column;
        gap: 20px;

        @media (max-width: $screen-md) {
            padding: 10px;
            width: 100%;
        }
    }
}

.receive__address-card {
    width: 100%;
    max-width: 524px;
    padding: 20px;
    border-top: 2px solid $Zinc-200;
    background-color: $white;

    @media (max-width: $screen-md) {
        padding: 10px;
    }
}

.receive__delivery-address {
    padding: 20px;
    background-color: $white;

    @media (max-width: $screen-md) {
        padding: 10px;
    }
}
</style>