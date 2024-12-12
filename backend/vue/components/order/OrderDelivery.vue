<template>
    <div class="delivery">
        <div class="delivery__city">
            <p>Доставка</p>

            <base-autocomplete
                :items="cityList"
                :defaultValue="order.delivery.city"
                id="city"
                name="city"
                placeholder="Город"
                :isEditable="isEditable"
                @setValue="setCity"
            />
        </div>

        <div class="delivery__tabs tabs">
            <div class="tabs__courier courier-tab">
                <div class="flex justify-content-center">
                    <Button
                        class="mr-auto ml-auto"
                        label="Курьер"
                        :severity="order.delivery.type === 1 ? 'info' : 'secondary'"
                        :disabled="!isEditable"
                        @click="switchTab(1)"
                    />
                </div>

                <div v-show="order.delivery.type == 1"
                    class="courier-tab__content courier">

                    <div class="courier__field">
                        <base-field
                            additionalContainerClass="courier__input"
                            name="courier-address"
                            id="courier-address"
                            :value="address"
                            type="text"
                            placeholder="Адрес доставки"
                            labelText="Адрес доставки"
                            :error="searchError"
                            :isEditable="isEditable"
                            @updateInputValue="setInputAddress"
                        />
                        <OrderSuggests
                            v-if="isEditable"
                            :query="isChosen ? '' : address"
                            @suggestChoice="suggestChosen"
                        />
                    </div>
                    <input
                        :value="order.delivery.flat"
                        type="text"
                        class="courier__flat"
                        placeholder="Квартира"
                        :readonly="!isEditable"
                        @input="addressUpdate('flat')"                        
                    >
                    <input 
                        :value="order.delivery.entry"
                        type="text"
                        class="courier__entries"
                        placeholder="Подъезд"
                        :readonly="!isEditable"
                        @input="addressUpdate('entry')"                           
                    >
                    <input 
                        :value="order.delivery.entryCode"
                        type="text"
                        class="courier__entries-code"
                        placeholder="Код домофона"
                        :readonly="!isEditable"                        
                        @input="addressUpdate('entryCode')"                        
                    >
                    <input
                        :value="order.delivery.flor"
                        type="text"
                        class="courier__flor"
                        placeholder="этаж"
                        :readonly="!isEditable"
                        @input="addressUpdate('flor')"                                                    
                    >
                    <textarea
                        :value="order.delivery.courierComment"
                        name=""
                        id=""
                        cols="30"
                        rows="3"
                        class="courier__comment-for-courier"
                        placeholder="Комментарий для курьера"
                        :readonly="!isEditable"
                        @input="addressUpdate('courierComment')"                        
                    ></textarea>

                </div>
            </div>

            <div class="tabs__pvz pvz-tab">
                <div class="flex justify-content-center">
                    <Button
                        class="mr-auto ml-auto"
                        label="ПВЗ"
                        :severity="order.delivery.type === 2 ? 'info' : 'secondary'"
                        :disabled="!isEditable"
                        @click="switchTab(2)"
                    />
                </div>

                <div :class="{'pvz-tab__content_visible': order.delivery.type == 2}"
                    class="pvz-tab__content">
                    <div class="pvz__selected">Выбран: {{order.delivery.city}}, {{order.delivery.address}}</div>

                    <div class="pvz__map">
                        <base-map
                            class="result__map map-default"
                            ref="mapBlock"
                            type="pvz"
                            :start-center="startMapCoords"
                            :city=" order.delivery.city"
                            :points="pickup_points"
                            @pickPvz="setPoint"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {inject} from "vue"
import {loadYmap} from "vue-yandex-maps"
import {points_and_polygons} from "../../mixins/points_and_polygons"
import axios from "axios"
import BaseAutocomplete from "../base/BaseAutocomplete"
import BaseField from "../base/BaseField"
import BaseMap from "../base/map/BaseMap"
import OrderSuggests from "./OrderSuggests"

export default {
    name: "OrderDelivery",
    components: {
        BaseAutocomplete,
        BaseField,
        BaseMap,
        OrderSuggests
    },
    mixins: [points_and_polygons],
    data() {
        return {
            cityList: [],
            pointId: null,
            searchError: '',
            selectedPvz: {},
            address: this.order.delivery ? this.order.delivery.address : "",
            isChosen: false
        }
    },
    beforeMount() {
        this.getCityList()

        if (!_.isEmpty(this.order.delivery.city)) {
            this.city_name = this.order.delivery.city
            this.getPointsAndPolygons()
        }
    },
    async mounted() {
        await loadYmap()
    },
    setup() {
        const order = inject('order', {})

        const setType = inject('setType')
        const setOrderCity = inject('setCity')
        const setAddress = inject('setAddress')
        const setPoint = inject('setPoint')

        const isEditable = inject('isEditable')

        return {
            order,
            setType,
            setOrderCity,
            setAddress,
            setPoint,
            isEditable
        }
    },
    computed: {
        startMapCoords() {
            return _.isNumber(this.order.delivery.latitude) && _.isNumber(this.order.delivery.longitude)
                ? [this.order.delivery.latitude, this.order.delivery.longitude]
                : [55.755864, 37.617698]
        }
    },
    methods: {
        getCityList() {
            axios
                .get('/admin/backend/courier/city')
                .then((response) => {
                    this.cityList = response.data
                })
        },
        switchTab(id) {
            this.setType(id)

            if (
                !_.isEmpty(this.order.delivery.city)
                && this.order.delivery.type == 2
            ) this.setCity(this.order.delivery.city)
        },
        setCity(value) {
            this.setOrderCity(value)
            this.city_name = value
            this.getPointsAndPolygons()
        },
        addressUpdate(field) {
            if (field == 'address') {
                this.getCoords(event.target.value)
            } else {
                this.setAddress(field, event.target.value)
            }
        },
        setInputAddress(value) {
            this.isChosen = false
            this.address = value
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
                    } else {
                        this.searchError = ''
                        this.setAddress('latitude', result.data.latitude)
                        this.setAddress('longitude', result.data.longitude)
                        this.setAddress('address', address)
                    }
                })
        },
        setPoint(point) {
            this.setPoint({
                pointId: point.id_point,
                address: point.address,
                latitude: point.latitude,
                longitude: point.longitude,
            })
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
.delivery {
    &__city {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: flex-start;
    }
}

.tabs {
    display: flex;
    flex-wrap: nowrap;
    align-items: flex-start;
    justify-content: space-between;
    margin-top: 20px;

    &__courier, &__pvz {
        max-width: 49%;
        width: 100%;
    }

    &__switcher {
        cursor: pointer;
        padding: 15px 20px;
        box-shadow: 0 0 5px 0 rgba(0 0 0/.5);
        transition: .3s;
        max-width: fit-content;
        margin: 0 auto;

        &:hover {
            box-shadow:inset 0 0 5px 0 rgba(0 0 0/.5);
        }
    }
}

.courier {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    margin-top: 20px;

    &__field {
        margin-bottom: 15px;
        width: 100%;
    }

    &__flat,
    &__entries,
    &__entries-code,
    &__flor {
        max-width: 23%;
    }

    &__comment-for-courier {
        margin-top: 15px;
        width: 100%;
    }

    &__field {
        position: relative;
    }

    &__address-notice {
        position: absolute;
    }
}

.pvz-tab {
    &__content {
        margin-top: 20px;
        opacity: 0;
        height: 1px;

        &_visible {
            opacity: 1;
            height: 400px;
        }
    }

}

.pvz {
    &__field {
        position: relative;
    }

    &__address-notice {
        position: absolute;
    }

    &__map {
        height: 100%;
    }
}

.ymap-container {
    width: 100%;
    height: 100%;
}
</style>