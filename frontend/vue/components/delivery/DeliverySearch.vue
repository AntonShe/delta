<template>
    <div class="delivery-search" :class="{ 'delivery-search--border-bottom': borderBottom }">
        <div class="delivery-search__select">
            <base-autocomplete
                id="city"
                name="city"
                placeholder="Город"
                :default-value="citySearch"
                :items="cityList"
                :isRequired="true"
                :close-show="true"
                @setValue="setCity"
                @cleared="clearCity"
            />
        </div>
        <div class="delivery-search__radio">
            <div v-for="input in inputRadio" :key="input.id">
                <input :id="input.id" class="delivery-search__radio-button" type="radio" name="delivery"
                       :checked="input.typeTab === this.typeDelivery">
                <label :for="input.id" class="delivery-search__radio-label"
                       @click="switchTab(input.typeTab)">{{ input.label }}</label>
            </div>
        </div>
    </div>
</template>

<script>
import BaseAutocomplete from "../base/BaseAutocomplete"
import { mapState, mapActions } from 'vuex'

export default {
    name: "DeliverySearch",
    components: {
        BaseAutocomplete
    },
    data() {
        return {
            citySearch: this.city ? this.city : '',
            typeDelivery: this.typeTab,
            inputRadio: [
                {
                    id: "deliveryCourier",
                    typeTab: 'zone',
                    label: 'Курьером'
                },
                {
                    id: "deliveryPickupPoint",
                    typeTab: 'pvz',
                    label: 'В пункт выдачи'
                }
            ]
        }
    },
    emits: ['switchTab', 'setCity'],
    props: {
        city: {
            type: String,
            default: ''
        },
        typeTab: {
            type: String,
            default: 'zone'
        },
        borderBottom: {
            type: Boolean,
            default: false
        }
    },
    beforeMount() {
        this.getCityList()
    },
    computed: {
        ...mapState({
            cityList: state => state.order.cityList
        })
    },
    methods: {
        ...mapActions({
            getCityList: 'order/getCityList'
        }),
        switchTab(id) {
            if (this.typeDelivery === id) {
                return;
            }

            this.typeDelivery = id;
            this.$emit('switchTab', id);
        },
        setCity(city) {
            if (this.citySearch === city) {
                return;
            }

            this.citySearch = city;
            this.$emit('setCity', city);
        },
        clearCity() {
            this.citySearch = '';
        }
    }
}
</script>

<style lang="scss" scoped>
.delivery-search {
    background: $white;
    padding: 20px;
    margin: 0;
    @include _border-radius(8px);

    @media (max-width: $screen-xl-l) {
        padding: 20px 10px;
    }

    @media (max-width: $screen-md) {
        padding: 10px 15px;
        margin-bottom: 50vh;
        @include _border-radius(0);
    }

    &--border-bottom {
        @include _border-radius(8px 8px 0 0);

        @media (max-width: $screen-md) {
            margin-bottom: 0;
            @include _border-radius(0);
        }
    }

    &__radio {
        display: flex;
        align-items: center;
        gap: 36px;
        margin-top: 20px;

        @media (max-width: $screen-md) {
            justify-content: space-around;
        }
    }

    &__radio-button {
        position: absolute;
        z-index: -1;
        opacity: 0;

        &:checked + label::before {
            border: none;
            background: get-icon('radio-btn', $Red-400);
        }
    }

    &__radio-label {
        display: flex;
        align-items: center;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
        cursor: pointer;

        &::before {
            content: '';
            display: block;
            width: 20px;
            height: 20px;
            margin-right: 8px;
            border: 1px solid $Zinc-400;
            @include _border-radius(50%);
        }
    }
}
</style>