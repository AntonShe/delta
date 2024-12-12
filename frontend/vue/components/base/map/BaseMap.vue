<template>
    <div class="map-wrapper">
        <yandex-map ref="yandexMap"
                    :settings="getSettings"
                    :options="options"
                    :coords="coords"
                    :zoom="mapZoom"
                    :controls="controls"
                    :detailed-controls="detailedControls"
                    :cluster-options="clusterOptions"
                    @boundschange="changeZoom"
                    @click.self="onClick($event.get('coords'))"
        >
            <ymap-marker v-for="(placemark, index) in points"
                         :key="placemark.id"
                         :marker-id="index"
                         :coords="[placemark.latitude, placemark.longitude]"
                         :icon="markerIcon"
                         cluster-name="default"
                         @click.self="showPvz(placemark)"
            />
        </yandex-map>
    </div>
</template>

<script>
import {loadYmap, yandexMap, ymapMarker} from 'vue-yandex-maps'

import AbstractBalloon from "./balloons/AbstractBalloon"
import BaseBalloonCourier from './balloons/BaseBalloonCourier'
import BaseBalloonPvz from "./balloons/BaseBalloonPvz"
import PvzInfo from "../../order/PvzInfo"
const TYPE_PVZ = 'pvz'
const TYPE_ZONE = 'zone'
const TYPE_MULTI = 'multi'
const DEFAULT_ICON = '<svg width="37" height="52.4" viewBox="0 0 185 262" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.4998 137.959L20.3666 137.772L20.2211 137.596C8.21702 123.15 1 104.626 1 84.3985C1 38.3515 38.406 1 84.5924 1H99.4324C145.613 1 183.025 38.3515 183.025 84.3985C183.025 104.626 175.808 123.15 163.798 137.596L163.652 137.772L163.519 137.959C158.75 144.607 153.151 151.394 147.103 158.744C146.128 159.932 145.14 161.131 144.14 162.349C137.02 171.021 129.47 180.395 122.495 190.696C109.413 210.002 98.2144 232.738 95.0392 261H88.9917C85.8225 232.744 74.6183 210.008 61.5356 190.696C54.5549 180.395 47.0107 171.021 39.8906 162.349C38.8908 161.131 37.9031 159.932 36.9275 158.75C30.8678 151.394 25.2748 144.607 20.4998 137.959Z" fill="#5B2599" stroke="#FEFEFE" stroke-width="2"/><path d="M102.063 108.734V76.0361H51.3986C51.3986 91.5427 48.0113 97.2872 37.5039 97.2872V109.382C55.2162 109.382 63.9238 95.8935 63.9238 84.2954C65.5599 84.2954 88.841 84.2954 88.841 84.2954V108.734H102.063Z" fill="#FEFEFE"/><path fill-rule="evenodd" clip-rule="evenodd" d="M105.589 34.0312C107.056 34.0312 108.498 34.2191 109.91 34.5766C116.563 37.4065 122.95 43.4661 128.01 52.907C133.67 63.5476 137.136 77.4363 137.154 93.1246C137.148 95.7485 137.057 98.3359 136.875 100.869C135.948 113.467 132.749 124.641 128.016 133.536C123.744 141.529 118.545 147.092 113 150.333C110.625 151.4 108.147 151.964 105.595 151.964C102.614 151.964 99.7298 151.188 96.9908 149.746C94.4215 148.085 91.9856 145.94 89.7132 143.365C86.5743 139.766 83.9445 135.56 81.8418 131.149C79.9754 127.198 78.4363 122.938 77.2365 118.611C77.091 118.084 76.9517 117.551 76.8184 117.018H82.7507C83.5506 119.472 84.4716 121.835 85.5381 124.023C87.1379 127.337 89.1073 130.361 91.4402 132.761C94.0762 135.5 96.9787 137.22 99.9782 137.796C103.414 138.457 106.983 137.608 110.419 135.075C114.321 132.185 117.89 127.252 120.593 120.338C123.562 112.818 125.295 103.511 125.295 93.2216C125.295 82.9324 123.562 73.6248 120.593 66.1048C117.86 59.209 114.321 54.2582 110.419 51.386C107.013 48.8712 103.414 48.0047 99.9782 48.6652C96.9787 49.2409 94.0762 50.9618 91.4402 53.7008C89.1073 56.1004 87.1379 59.1241 85.5381 62.4387C85.1927 63.1416 84.8655 63.8688 84.5444 64.6081H78.1757C79.1877 61.4025 80.4056 58.2697 81.8296 55.3066C83.9323 50.877 86.5319 46.6898 89.7011 43.0904C92.9733 39.3879 96.5909 36.5642 100.451 34.8069C102.129 34.2979 103.838 34.0312 105.589 34.0312Z" fill="#65D0DD"/> </svg>'
const CLUSTER_ICON_CONTENT_LAYOUT = '<div class="map-default__cluster">{{properties.geoObjects.length}}</div>'

// Тип обращения к Яндексу (см. YandexLogsTypes.php)
const JSAPI_SCRIPT = 5

/**
 * КАРТА ДЛЯ СЛУЖБЫ ДОСТАВКИ Л-ПОСТ
 *
 * ОСНОВНЫЕ ВХОДНЫЕ ПАРАМЕТРЫ ДЛЯ КОРРЕКТНОЙ РАБОТЫ КОМПОНЕНТА:
 * 1) type - тип доставки. Возможные варианты: "pvz", "zone", "multi";
 * 2) city - город, по которому отцентрируется карта;
 * 3) polygons - полигоны, выделяемые на карте. Будут выделены, если тип доставки "zone" или "multi";
 * 4) points - точки, которые отрисуются на карте. Будут отрисованы, если тип доставки "pvz" или "multi".
 *
 * ПОЛЕЗНАЯ ИНФОРМАЦИЯ ПРИ РАБОТЕ С КАРТОЙ:
 * 1) Если вам необходимо изменять входной параметр zoom в родительском компоненте, тогда для корректной работы
 * компонента BaseMap передавайте этот параметр через v-model.
 * 2) При использовании input'а для передачи входного параметра city для корректной работы компонента BaseMap
 * необходимо повесить на этот input модификатор lazy.
 *
 * ПОЛЕЗНАЯ ИНФОРМАЦИЯ ПРИ РАБОТЕ С БАЛУНАМИ:
 * 1) В пользовательское событие beforeBalloonOpen передаются:
 *  - объект с данными точки;
 *  - функция Promise.resolve(), которая выполняет открытие балуна;
 *  - объект карты (экземпляр класса Map (см. https://yandex.ru/dev/maps/jsapi/doc/2.1/ref/reference/Map.html)).
 *  В функцию для открытия балуна можно передать объект, в свойствах которого будут дополнительные данные, и zoom,
 *  который сработает перед открытием балуна, по-умолчанию равен 16.
 * 2) Если нужно передать свой балун, то необходимо релизовать класс унаследованный от {@link AbstractBalloon},
 * создать его экземпляр и передать в один из следующих входных параметров: balloonPvz, balloonCourier.
 */
export default {
    name: "BaseMap",
    components: {
        yandexMap,
        ymapMarker,
        PvzInfo
    },
    emits: ['beforeBalloonOpen', 'update:zoom', 'error', 'setPvz', 'addressClick'],
    props: {
        startCenter: {
            type: Array,
            default: [55.755864, 37.617698]
        },
        isCourier: {
            type: Boolean,
            default: true
        },
        show: {
            type: Boolean,
            default: true
        },
        zoom: {
            type: Number,
            default: 12,
        },
        type: {
            validator: function (value) {
                return [TYPE_PVZ, TYPE_ZONE, TYPE_MULTI].includes(value)
            }
        },
        city: {
            type: String,
            default: 'Москва',
            validator(value) {
                return !_.isEmpty(value)
            }
        },
        polygons: Array,
        points: Array,
        style: {
            type: Object,
            default() {
                return {}
            }
        },
        class: {
            type: String,
            default: 'map-default'
        },
        additionalClass: {
            type: String
        },
        balloonPvz: {
            type: AbstractBalloon,
            default: () => new BaseBalloonPvz
        },
        balloonCourier: {
            type: AbstractBalloon,
            default: () => new BaseBalloonCourier,
        },
        fillColorForPolygons: {
            type: String,
            default: 'rgba(149, 140, 212, 0.16)'
        },
        settings: {
            type: Object
        },
        controls: {
            type: Array,
            default: ['zoomControl']
        },
        detailedControls: {
            type: Object,
            default: {
                zoomControl: {
                    position: {
                        right: 16,
                        top: 22
                    },
                    size: 'small'
                }
            }
        }
    },
    data() {
        return {
            types: {
                pvz: TYPE_PVZ,
                zone: TYPE_ZONE,
                multi: TYPE_MULTI
            },
            coords: this.startCenter,
            // addressCoordinates: [55.755864, 37.617698],
            options: {
                minZoom: 1,
                autoFitToViewport: 'always',
                suppressMapOpenBlock: true,
                yandexMapDisablePoiInteractivity: true
            },
            clusterOptions: {
                default: {
                    clusterIcons: [
                        {
                            size: [32, 32],
                            offset: [-16, -16],
                            shape: {
                                type: 'Circle',
                                coordinates: [0, 0],
                                radius: 16,
                            },
                        },
                        {
                            size: [40, 40],
                            offset: [-20, -20],
                            shape: {
                                type: 'Circle',
                                coordinates: [0, 0],
                                radius: 20,
                            },
                        }
                    ],
                    clusterIconContentLayout: CLUSTER_ICON_CONTENT_LAYOUT
                },
            },
            markerIcon: {
                layout: 'default#imageWithContent',
                imageHref: '',
                imageSize: [37, 52.4],
                imageOffset: [-18.5, -52.4],
                contentLayout: DEFAULT_ICON
            },
            mapZoom: this.zoom,
            isPolygonsAddedToMap: false,
            centerIsShifting: false,
            mapLoaded: false,
            showPoints: true
        }
    },
    computed: {
        loading() {
            return (this.centerIsShifting)
                || (this.type === TYPE_PVZ && _.isEmpty(this.points))
                || (this.type === TYPE_ZONE && !this.isPolygonsAddedToMap)
                || (this.type === TYPE_MULTI && _.isEmpty(this.points) && !this.isPolygonsAddedToMap)
        },
        getSettings() {
            return this.$yandexSettings;
        }
    },
    watch: {
        polygons: {
            deep: true,
            handler(newValue) {
                if (this.mapLoaded && (this.type === TYPE_ZONE || this.type === TYPE_MULTI) && !_.isEmpty(newValue)) {
                    this.removePolygonsToMap()
                    this.addPolygonsToMap()
                }
            }
        },
        city() {
            this.balloonClose()
            this.setCenterBySetCity()
        },
        type(newValue) {
            this.balloonClose()
            if (newValue === TYPE_PVZ) {
                if (this.isPolygonsAddedToMap)
                    this.removePolygonsToMap()
            } else {
                this.addPolygonsToMap()
            }
        },
        zoom(newValue) {
            this.mapZoom = newValue
        },
        points() {
            this.showPoints = false
            this.$nextTick(() => {
                this.showPoints = true
                this.clusterOptions.default.clusterIconContentLayout = CLUSTER_ICON_CONTENT_LAYOUT
            })
        }
    },
    async mounted() {
        await loadYmap(this.$yandexSettings)

        this.mapLoaded = true
        this.setCenterBySetCity(true)

        if (!this.isPolygonsAddedToMap && !_.isEmpty(this.polygons) && this.type !== TYPE_PVZ)
            this.addPolygonsToMap()
    },
    methods: {
        balloonCourierOpen() {
            if (!this.balloonCourier.map) this.balloonCourier.map = this.$refs.yandexMap.myMap
            this.geocode(this.coords).then(res => {
                let firstGeoObject = res.geoObjects.get(0)
                let premiseNumber = (firstGeoObject.getPremiseNumber()) ? firstGeoObject.getPremiseNumber() : ''

                let thoroughfareOrPremise = (firstGeoObject.getThoroughfare())
                    ? firstGeoObject.getThoroughfare()
                    : (firstGeoObject.getPremise()) ? firstGeoObject.getPremise() : ''

                let dataForBalloon = {}

                dataForBalloon.fullAddress = firstGeoObject.getAddressLine()
                dataForBalloon.isIncludedInPolygon = false
                _.forEach(this.yandexPolygons, polygon => {
                    if (polygon.geometry.contains(this.coords)) dataForBalloon.isIncludedInPolygon = true
                })
                dataForBalloon.isExactAddress = !_.isEmpty(premiseNumber)
                dataForBalloon.locality = !_.isEmpty(firstGeoObject.getLocalities())
                    ? firstGeoObject.getLocalities()[0]
                    : null
                dataForBalloon.streetAndHouse = thoroughfareOrPremise +
                    (dataForBalloon.isExactAddress
                        ? (!_.isEmpty(thoroughfareOrPremise) ? ', ' : 'Дом №') + premiseNumber
                        : '')

                this.balloonOpen(this.coords, dataForBalloon, true)
            })
        },
        showPvz(point) {
            let centerPvz = (zoom) => this.$refs.yandexMap.myMap.setCenter([point.latitude, point.longitude], zoom);
            this.$emit('setPvz', point, centerPvz);
        },
        balloonPvzOpen(point) {
            if (!this.balloonPvz.map) this.balloonPvz.map = this.$refs.yandexMap.myMap
            let coords = [point.latitude, point.longitude]
            this.balloonOpen(coords, point)
        },
        balloonOpen(coords, dataForBalloonDefault, isCourier = false) {
            this.balloonClose()

            let result = new Promise((resolve) => this.$emit(
                'beforeBalloonOpen',
                Object.assign({
                    point_type: this.type,
                    latitude: coords[0],
                    longitude: coords[1]
                }, dataForBalloonDefault),
                resolve,
                this.$refs.yandexMap.myMap
            ))

            result.then((dataForBalloonFromClient = {}, zoomBeforeOpen = 16) => {
                let dataForBalloon = Object.assign({}, dataForBalloonDefault, dataForBalloonFromClient),
                    balloonObject = (this.type === TYPE_ZONE || (this.type === TYPE_MULTI && isCourier)) ? this.balloonCourier : this.balloonPvz,
                    balloonOptions = {closeButton: false}

                // Добавляем обработчики событий балуна при его открытии
                this.$refs.yandexMap.myMap.events.once('balloonopen', () => balloonObject.bindListener())
                // Удаляем обработчики событий балуна при его закрытии
                this.$refs.yandexMap.myMap.events.once('balloonclose', () => balloonObject.unbindListener())
                // Подписываемся на окончание исполнения шага плавного движения
                this.$refs.yandexMap.myMap.events.once('actiontickcomplete', () =>
                    // Открываем балун
                    this.$refs.yandexMap.myMap.balloon.open(coords, balloonObject.render(dataForBalloon),
                        balloonOptions)
                )
                // Центрируем карту
                this.$refs.yandexMap.myMap.setCenter(coords, zoomBeforeOpen)
            })
        },
        balloonClose() {
            if (this.$refs.yandexMap.myMap.balloon.isOpen())
                this.$refs.yandexMap.myMap.balloon.close()
        },
        setCenterBySetCity(mounted = false) {

            let city = this.city

            // Костыль из-за некорректного геокодирования яндекса
            if (this.city.includes('Горно-Алтайск')) {
                city = 'Горно-Алтайск'
            }

            this.geocode(city).then((res) => {
                if (!res.geoObjects.get(0))
                    return console.log('Ошибка! Не удалось отцентрировать карту по переданному городу')

                this.centerIsShifting = true
                this.coords = res.geoObjects.get(0).geometry.getCoordinates()

                if (mounted) {
                    let diff = _.differenceBy(this.startCenter, this.coords, val => val.toFixed(3));
                    this.centerIsShifting = !_.isEmpty(diff);
                }

                let handler = this.$refs.yandexMap.myMap.events.group()
                handler.add('boundschange', (e) => {
                    let diff = _.differenceBy(e.get('newCenter'), this.coords, val => val.toFixed(3))
                    if (_.isEmpty(diff) && this.centerIsShifting) {
                        handler.removeAll()
                        this.centerIsShifting = false
                    }
                })
            }).catch(error => this.$emit('error', error))
        },
        addPolygonsToMap() {
            let processedPolygons = []
            let processedPolygon

            _.forEach(this.polygons, polygon => {
                processedPolygon = new ymaps.GeoObject({
                    geometry: {
                        type: "Polygon",
                        coordinates: [polygon, []]
                    },
                    properties:{
                        hintContent: "Курьерская зона доставки"
                    }
                }, {
                    fillColor: this.fillColorForPolygons,
                    strokeWidth: 0,
                    zIndex: 'auto'
                })

                if (this.type === TYPE_MULTI) {
                    processedPolygon.events.add('click', this.onClick)
                }

                this.$refs.yandexMap.myMap.geoObjects.add(processedPolygon)
                processedPolygons.push(processedPolygon)
            })

            this.yandexPolygons = processedPolygons
            this.isPolygonsAddedToMap = true
        },
        removePolygonsToMap() {
            if (this.yandexPolygons) {
                _.forEach(this.yandexPolygons, polygon => this.$refs.yandexMap.myMap.geoObjects.remove(polygon))
                this.isPolygonsAddedToMap = false
            }
        },
        onClick(eventData) {
            // this.addressCoordinates = _.isArray(eventData) ? eventData : eventData.get('coords')

            this.balloonClose()
            if (this.type === TYPE_ZONE || this.type === TYPE_MULTI) {
                this.coords = _.isArray(eventData) ? eventData : eventData.get('coords')
                this.$refs.yandexMap.myMap.events.once('boundschange', () => this.balloonCourierOpen())
                this.geocode(this.coords)
                    .then(res => {
                        const firstGeoObject = res.geoObjects.get(0);
                        const metaData = firstGeoObject.properties.get('metaDataProperty');
                        const completeAddress = metaData.GeocoderMetaData.precision === "exact";
                        const address = firstGeoObject.getAddressLine();
                        this.$emit('addressClick', this.coords, address ,completeAddress);
                    })
                this.$refs.yandexMap.myMap.setCenter(this.coords, this.mapZoom)
            }
        },
        changeZoom(e) {
            if (!this.centerIsShifting) {
                this.mapZoom = e.get('newZoom')
                this.$emit('update:zoom', this.mapZoom)
            }
        },
        async geocode(data) {
            if (typeof ymaps === 'undefined') {
                await loadYmap(this.$yandexSettings)
            }

            return ymaps.geocode(data)
        }
    }
}
</script>

<style lang="scss">
.map-wrapper {
    width: 100%;
    height: 100%;
    position: relative;

    &--pvz{
        display: flex;
        max-width: 1612px;
        margin: 20px auto 0;

        @media(max-width: $screen-xl){
            margin-top: 0;
            flex-direction: column;
        }
    }
}
.map-default {
    min-width: 50%;
    height: 100%;

    @media(max-width: $screen-xl){
        order: 1;
    }

    &__cluster{
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        border: 4px solid $white;
        background-color: #5b2599;
        box-shadow: 0 0 4px #555555;
        font-size: 15px;
        line-height: 15px;
        border-radius: 100%;
        color: $white;
    }
}
</style>