<template>
    <BaseMap
        v-model:zoom="mapZoom"
        :points="pvzCity"
        :type="typeDelivery"
        :city="city"
        :detailed-controls="{
                            zoomControl: {
                                position: {
                                    right: 16,
                                    top: 57
                                },
                                size: 'small'
                            }
                        }"
        @setPvz="setPvzInfo"
        @addressClick="addressClick"
        @update:zoom="setZoom"
    />
</template>

<script>
import BaseMap from "../base/map/BaseMap"
import axios from "axios"

export default {
    name: "DeliveryMap",
    components: {
        BaseMap
    },
    emits: ['setPvzInfo', 'addressClick'],
    props: {
        city:{
            type: String,
            default: 'Москва'
        },
        typeDelivery:{
            type: String,
            default: 'zone'
        }
    },
    data() {
        return {
            mapZoom: 12,
            points: JSON.parse(localStorage.getItem('points')),
            pvzCity: [],
        }
    },
    watch: {
        city: function (newValue, oldValue) {
            if (newValue !== oldValue) {
                this.setPvzCity();
                this.fnMapZoom();
            }
        },
        typeDelivery: function (newValue, oldValue) {
            if (newValue !== oldValue) {
                this.setPvzCity();
            }
        },
    },
    beforeMount() {
        this.setPvzCity();
    },
    methods: {
        fnMapZoom() {
            if (this.pvzCity.length >= 16) {
                this.setZoom(9);
            } else {
                this.setZoom(12);
            }
        },
        setZoom(zoom) {
            if (this.mapZoom !== zoom) {
                this.mapZoom = zoom;
            }
        },
        getDataForPoint(data) {
            const rawDate = new Date(data);
            let monthName = rawDate.toLocaleString('ru-Ru', {month: "long"});
            monthName = monthName.substring(0, monthName.length - 1) + 'я';
            let fullDate = [
                rawDate.getDate(),
                monthName,
                '(' + rawDate.toLocaleString('ru-Ru', {weekday: "short"}) + ')'
            ]

            return fullDate.join(' ');
        },
        async setPvzCity() {
            if (this.typeDelivery === 'zone') {
                this.pvzCity = [];
                return;
            }

            if (!this.points) {
                await axios
                    .get('/admin/backend/pvz/points')
                    .then(response => {
                        localStorage.setItem('points', JSON.stringify(response.data));
                        this.points = response.data;
                    })
            }

            this.pvzCity = this.points.filter(item => item.city_name === this.city);
        },
        async setPvzInfo(point, centerPvz) {
            if (this.mapZoom < 16) {
                this.mapZoom = 16;
            }
            centerPvz(this.mapZoom);

            let pvzInfo;
            const {address, id_point, location_description: locationDescription, is_card: card, schedule} = point;

            await axios
                .post('/admin/backend/pvz/calculate', {
                    idPoint: id_point
                })
                .then(result => {
                    if (!_.isEmpty(result.data)) {
                        const {DateClose, SumCost} = result.data.JSON_TXT[0];
                        const deliveryDate = this.getDataForPoint(DateClose);
                        const price = parseInt(SumCost);

                        pvzInfo = {address, locationDescription, card: card === 1, price, deliveryDate, schedule};
                    } else {
                        throw new Error('Ошибка при получение ПВЗ');
                    }
                })
                .catch((error) => {
                    throw new Error(error.message);
                })

            this.$emit('setPvzInfo', pvzInfo);
        },
        async addressClick(coords, address, completeAddress) {
            if (this.mapZoom < 16) {
                this.mapZoom = this.mapZoom === 15 ? this.mapZoom + 1 : this.mapZoom + 2;
            }

            let addressInfo;

            if (completeAddress) {
                await axios
                    .post('/admin/backend/courier/calculate', {
                        address: 'Россия, ' + address,
                        latitude: coords[0],
                        longitude: coords[1],
                    })
                    .then(response => {
                        if (!response.data.JSON_TXT) {
                            addressInfo = {}
                        } else {
                            const {DateClose, SumCost} = response.data.JSON_TXT[0];
                            const deliveryDate = this.getDataForPoint(DateClose);
                            const price = parseInt(SumCost);

                            addressInfo = {address, deliveryDate, price};
                        }
                    })
                    .catch((error) => {
                        throw new Error(error.message);
                    })
            }

            this.$emit('addressClick', addressInfo);
        }
    },
}
</script>

<style lang="scss">
.ymap-container {
    width: 100%;
    height: 100%;

    & [class*="ground-pane"] {
        filter: grayscale(1);
    }

    & [class*="copyrights-promo"] {
        display: none;
    }

    & [class*="copyright__wrap"] {
        display: none;
    }
}
</style>