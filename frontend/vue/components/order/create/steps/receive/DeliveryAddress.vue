<template>
	<div class="delivery-address">
		<div class="delivery-address__top">
            <InputBlockTitle title="Адрес доставки" />
            <div class="delivery-address__top-btn"
                 v-if="receiveType == 2"
                 @click="showMap">Выбрать на карте</div>
		</div>
		<div class="delivery-address__bottom" v-show="receiveType == 1">
			<div class="delivery-address__bottom-left">
                <base-field
                    :value="address"
                    additionalContainerClass="courier__input"
                    id="street"
                    name="street"
                    :error="searchError"
                    type="text"
                    placeholder="Адрес доставки"
                    @updateInputValue="setInputAddress"
                />
                <DeliverySuggests
                    :query="isChosen ? '' : address"
                    @suggestChoice="suggestChosen"
                />
			<div class="delivery-address__inputs-min">
                <BaseField
                        id="flat"
                        name="flat"
                        placeholder="Кв/офис"
                        :value="getDefaultFlat"
                        :isAddressDetails="true"
                        @updateInputValue="setAddressParams('flat')"                        
                />
                <BaseField
                        id="porch"
                        name="porch"
                        placeholder="Подъезд"
                        :value="getDefaultEntry"
                        :isAddressDetails="true"
                        @updateInputValue="setAddressParams('entry')"                        
                />
                <BaseField
                        id="intercom"
                        name="intercom"
                        placeholder="Домофон"
                        :value="getDefaultEntryCode"
                        :isAddressDetails="true"
                        @updateInputValue="setAddressParams('entryCode')"                        
                />
                <BaseField
                        id="floor"
                        name="floor"
                        placeholder="Этаж"
                        :value="getDefaultFlor"
                        :isAddressDetails="true"
                        @updateInputValue="setAddressParams('flor')"                        
                />
			</div>
            </div>
			<div class="delivery-address__bottom-right">
				<BaseTextarea
                        id="comment"
                        name="comment"
                        @changeInputValue="setAddressParams('courierComment')"
                        placeholder="Комментарий курьеру"
                        :value="getDefaultCourierComment"
				/>
			</div>
		</div>
        <div class="delivery-address__button-wrap">
            <button class="delivery-address__button delivery-address__button-cancel"
                    @click.prevent="cancelEdit">
                Отменить
            </button>
            <button class="delivery-address__button"
                    @click.prevent="saveAddress"
                    :class="{
                        'disabled': isSaveButtonDisabled
					}">
                Сохранить
            </button>
        </div>

		<div>
			<MapBlock
                :receiveType="receiveType"
                @close="closeMap"
                :city="city"
                :is-active="isShowMap"
                @pvzSelected="saveProfile"/>
		</div>
	</div>
</template>

<script>
import {loadYmap} from "vue-yandex-maps"
import BaseTextarea from "../../../../base/BaseTextarea"
import MapBlock from "../../../MapBlock"
import InputBlockTitle from "../../../../profile/content/info/InputBlockTitle"
import axios from "axios"
import DeliverySuggests from "./DeliverySuggests"
import { mapState } from 'vuex'

export default {
    name: "DeliveryAddress",
    components: {
        MapBlock,
        BaseTextarea,
        InputBlockTitle,
        DeliverySuggests
    },
	props: {
        receiveType: Number,
        city: {
            type: String,
            default: ''
        },
        comment: {
            type: String,
            default: ''
        }
	},
    emits: ['profileSave', 'cancel'],
    beforeMount() {
        console.log('тут')
        if (this.receiveType == 1 && !_.isEmpty(this.deliveryProfile.courier)) {
            this.delivery.latitude = this.deliveryProfile.courier.latitude
            this.delivery.longitude = this.deliveryProfile.courier.longitude
            this.delivery.address = this.deliveryProfile.courier.address
            this.delivery.flat = this.deliveryProfile.courier.flat
            this.delivery.flor = this.deliveryProfile.courier.flor
            this.delivery.entry = this.deliveryProfile.courier.entry
            this.delivery.entryCode = this.deliveryProfile.courier.entryCode
            this.delivery.courierComment = this.deliveryProfile.courier.comment ? this.deliveryProfile.courier.comment : this.comment
            this.address = this.deliveryProfile.courier.address || ''
        }
    },
    data() {
        return {
      		addressParams: {},
			isSaveButtonDisabled: true,
			isShowMap: false,
            searchError: '',
            delivery: {
                city: this.city,
                latitude: '',
                longitude: '',
                address: '',
                flat: '',
                flor: '',
                entry: '',
                entryCode: '',
                courierComment: '',
                type: this.receiveType
            },
            address: '',
            isChosen: false
		}
	},
    async mounted() {
        await loadYmap()
    },
    computed: {
        ...mapState({
            deliveryProfile: state => state.order.deliveryProfile
        }),
        getDefaultAddress() {
            return (this.receiveType == 1 && !_.isEmpty(this.deliveryProfile.courier))
                ? this.deliveryProfile.courier.address
                : ''
        },
        getDefaultFlat() {
            return (this.receiveType == 1 && !_.isEmpty(this.deliveryProfile.courier))
                ? this.deliveryProfile.courier.flat
                : ''
        },
        getDefaultEntry() {
            return (this.receiveType == 1 && !_.isEmpty(this.deliveryProfile.courier))
                ? this.deliveryProfile.courier.entry
                : ''
        },
        getDefaultEntryCode() {
            return (this.receiveType == 1 && !_.isEmpty(this.deliveryProfile.courier))
                ? this.deliveryProfile.courier.entryCode
                : ''
        },
        getDefaultFlor() {
            return (this.receiveType == 1 && !_.isEmpty(this.deliveryProfile.courier))
                ? this.deliveryProfile.courier.flor
                : ''
        },
        getDefaultCourierComment() {
            return this.delivery.courierComment
        },
    },
	methods: {
        showMap() {
			this.isShowMap = true;
            document.body.classList.add('body--scroll_none');
		},
        closeMap() {
            this.isShowMap = false;
            document.body.classList.remove('body--scroll_none');
        },
        setAddressParams(fieldName) {
            if (fieldName == 'address') {
                this.getCoords(event.target.value)
            } else {

                this.delivery[fieldName] = event.target.value
                this.isSaveButtonDisabled = _.isEmpty(this.delivery.address)
            }
		},
        saveAddress() {
            if (+this.delivery.type === 1 && this.isSaveButtonDisabled) return

            this.isSaveButtonDisabled = true

            if (+this.delivery.type === 2) {
                this.delivery.address = this.delivery.city + ', ' + this.delivery.address
            } else {
                this.delivery.address = this.delivery.address.replace(" обл, ", " область, ")
            }

            axios
                .post('/delivery-profile/create',
                    this.delivery
                ).then(response => {
                    if (response.data.status) {
                        this.$emit('profileSave', this.delivery.courierComment)
                    } else {
                        console.log(response.data)
                    }
            })
		},
        setInputAddress(value) {
            this.isChosen = false
            this.address = value
        },
        cancelEdit() {
            this.$emit('cancel')
        },
        getCoords(address) {
            axios
                .post('/admin/backend/courier/suggest',
                    {
                        address: address
                    }
                ).then(result => {
                if (result.data.perception != 'exact') {
                    this.searchError = 'Уточните адрес'
                    this.isSaveButtonDisabled = true
                } else {
                    this.delivery.latitude = result.data.latitude
                    this.delivery.longitude = result.data.longitude
                    this.delivery.address = address
                    this.searchError = ''
                    this.isSaveButtonDisabled = false
                }
            })
        },
        saveProfile(point) {
            this.delivery.latitude = point.latitude
            this.delivery.longitude = point.longitude
            this.delivery.address = point.address
            this.delivery.pointId = point.id_point

            this.saveAddress()
            this.closeMap()
        },
        suggestChosen(item) {
            this.address = item.address.slice(0, 2) === 'г ' ? item.address.slice(2) : item.address
            this.isChosen = true
            this.getCoords(this.address)
        }
	}
}
</script>

<style lang="scss">
.delivery-address {
    max-width: 1068px;
    padding-top: 20px;
    border-top: 2px solid $Zinc-200;

    &__top {
        max-width: 484px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;

        &-btn {
            padding-left: 28px;
            background: get-icon('mark', $Zinc-500) no-repeat left center;
            @include _typography-ext(fn, 14, 20, 600, ls, $Zinc-500);
            cursor: pointer;

            @media (max-width: $screen-md) {
                padding-left: 22px;
                @include _typography-ext(fn, 12, 20, 600, ls, $Zinc-500);
            }
        }

        .title-min {
            margin-bottom: 0;
        }
    }

    &__bottom {
        width: 100%;
        max-width: 902px;
        display: flex;
        margin-bottom: 20px;
        gap: 28px;

        @media (max-width: $screen-xl) {
            flex-direction: column;
        }        

        &-left {
            padding-top: 10px;
            display: flex;
            flex-direction: column;
            width: 484px;

            @media (max-width: $screen-md) {
                width: 100%;
            }
        }

        &-right {
            width: 390px;
            height: 100px;

            @media (max-width: $screen-md) {
                width: 100%;
            }
        }
    }

    &__inputs-min {
        display: flex;
        justify-content: space-between;
        gap: 12px;
    }

    &__button {
        height: 44px;
        text-align: end;
        @include _button-reset(9px 16px, $Zinc-700, none);
        @include _typography-ext(fn, 16, 26, 600, ls, $white);
        margin-left: 25px;
        
        @media (max-width: $screen-md) {
            width: 100%;
        }

        &:hover:not(.disabled) {
            background-color: $Zinc-900;
        }
        
        &.disabled {
            color: $Zinc-400;
            @include _button-reset(9px 16px, $Zinc-100, none);
        }

        &-wrap {
            padding: 10px 0;
            text-align: end;
        }

        &-cancel {
            background-color: $Zinc-300;
        }
    }

    &__button-wrap {
        display: flex;
        flex-wrap: nowrap;
        align-items: flex-start;
        justify-content: flex-end;
    }
}

.courier {
    $root: input-default;

    &__input {
        & .#{$root}__hint {
            margin-top: 5px;
        }
    }
}
</style>