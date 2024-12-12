<template>
    <div class="map map-wrapper"
         :class="{
                'map-wrapper__active': isActive,
                'map-wrapper__hidden': !isActive,
            }">
        <div class="map__header">
            <div class="map__header-courier" v-if="isCourier">
                <a class="map__title map__closebtn" @click="closeMap">К оформлению заказа</a>
            </div>
            <div class="map__header-pvz" v-else>
				<div>
					<div v-if="isShowPvz" class="map__title-back" @click="hidePvzInfo">Назад</div>
				</div>
                <div class="map__closebtn" @click="closeMap"><BaseSvgStore icon="x" /></div>
            </div>
        </div>
        <div class="carta">
            <PvzInfo v-if="isShowPvz"
                     :pvz="pvzInfo"
                     @pvzSelected="pvzSelected"
            />
            <BaseMap
                type="pvz"
                :city="city"
                :points="pickup_points"
                v-model:zoom="mapZoom"
                :detailed-controls="{
                            zoomControl: {
                                position: {
                                    right: 16,
                                    top: 57
                                },
                                size: 'small'
                            }
                        }"
                @beforeBalloonOpen="beforeBalloonOpen"
                @setPvz="setPvz"
            />
        </div>
    </div>
</template>

<script>
import BaseMap from "../base/map/BaseMap"
import {default_mixins} from "../../mixins/default_mixins"
import {points_and_polygons} from "../../mixins/points_and_polygons"
import PvzInfo from "./PvzInfo"

export default {
    name: "MapBlock",
    components: {
        PvzInfo,
        BaseMap
    },
    emits:['pvzSelected'],
    props: {
        receiveType: Number,
        city: String,
        isActive: {
            type: Boolean,
            default: false
        },
    },
    mixins: [
        points_and_polygons,
        default_mixins,
    ],
    data() {
        return {
            mapZoom: 12,
            hasFitting: false,
            cityForMap: this.city,
            pvzInfo: null,
            isShowPvz: false
        }
    },
    computed: {
        points_count() {
            let count = 0,
                city = this.city_name.replace(/,.+$/, '').toLowerCase(),
                region

            if (this.city_name.indexOf(',') !== -1) {
                region = this.city_name.replace(/^.+, /, '').toLowerCase()
            }

            _.forEach(this.pickup_points, point => {
                count += point.city_name.toLowerCase() === city
                && (!region || region === point.region.region_name.toLowerCase())
                    ? 1 : 0
            })

            return count;
        },
        points() {
            if (!this.hasFitting) {
                return this.pickup_points
            }
            return this.pointsWithoutFitting
        },
        pointsWithoutFitting() {
            let points = []
            this.pickup_points.forEach(point => {
                if (point.fitting_rooms_count && point.fitting_rooms_count > 0) {
                    points.push(point)
                }
            })
            return points
        },
        isCourier() {
            return this.receiveType === 1;
        }
    },
    created() {
        this.city_name = this.city
        this.getPointsAndPolygons()
            .then(() => this.updateMap())

    },
    methods: {
        updateMap() {
            if (this.points_count < 16) {
                this.mapZoom = 12
                return
            }
            this.mapZoom = 9
        },
        beforeBalloonOpen(data, balloonOpen) {
            if (data.city_name && this.city_name !== data.city_name) {
                this.city_name = data.city_name
            } else if (data.locality && this.city_name !== data.locality) {
                this.city_name = data.locality
            }
            balloonOpen()
        },
        closeMap() {
            this.$emit('close');
        },
        hidePvzInfo() {
            this.isShowPvz = false;
		},
        setPvz(point) {
            this.isShowPvz = true;
            this.pvzInfo = point;
        },
        pvzSelected(point) {
            this.$emit('pvzSelected', point);
        }
    }
}
</script>

<style lang="scss">
.carta {
    position: relative;
    width: 100%;
    height: 100vh;
    z-index: 1;
    background-color: $Zinc-50;

    & [class*="ground-pane"] {
        filter: grayscale(1);
    }
}

.map {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: $Zinc-50;
    z-index: 10;

    &__header {
        background: $white;
    }

    &__info-point {
        display: block;
        background-color: $white;
        padding: 0 20px 10px 10px;
        margin-bottom: 20px;
        @include _border-radius(0 0 8px 8px);

        @media (max-width: $screen-md) {
            margin-bottom: 0;
            padding-bottom: 0;
            position: relative;
        }
    }

    &__point-list {
        border-top: 1px solid $Zinc-200;
        padding: 20px 10px 0;
        list-style: none;

        @media (max-width: $screen-xl) {
            padding-bottom: 20px;
            border-bottom: 1px solid $Zinc-200;
        }
    }

    &__qs-point {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-500);
        margin-bottom: 2px;
    }

    &__as-point {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
        margin-bottom: 10px;
    }

    &__point-item:last-child &__as-point {
        margin-bottom: 0;
    }

    &__header-courier {
        display: flex;
        max-width: 1612px;
        height: 82px;
        margin: 0 auto;
        padding: 15px 20px 23px;
        justify-content: space-between;
        align-items: center;

        @media (max-width: $screen-xl){
            height: 138px;
            flex-direction: column;
            align-items: flex-start;
            padding: 20px; 
        }

        @media(max-width: $screen-md){
            padding: 10px 15px 20px; 
            height: 112px;
        }
    }

    &__header-pvz{
        display: flex;
        max-width: 1612px;
        height: 82px;
        margin: 0 auto;
        padding: 19px 20px 19px;
        justify-content: space-between;
        align-items: center;

        @media(max-width: $screen-md){
            padding: 10px 15px; 
            height: 64px;
        }
    }

    &__title {
        display: flex;
        align-items: center;
        @include _typography-ext(fn, 12, 16, 600, ls, $Zinc-900);

        @media (max-width: $screen-xl){
            @include _typography-ext(fn, 24, 32, 600, ls, $Zinc-900);
            height: 44px;
            align-items: center;
        }

        @media(max-width: $screen-md){
            @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);
        }

        &::before {
            content: '';
            display: block;
            width: 16px;
            height: 16px;
            margin-right: 6px;
            background: get-icon('tick-left', $Zinc-900) no-repeat center;

            @media (max-width: $screen-xl){
                margin: 0 10px;
            }

            @media(max-width: $screen-md){
                margin: 0 4px 0 0;
            }
        }        
    }

    &__courier-search, &__pvz-search{
        position: relative;
        width: 388px;

        @media(max-width: $screen-md){
            width: 100%;
        }

        &::before {
            content: '';
            display: block;
            position: absolute;
            left: 10px;
            top: 10px;
            width: 20px;
            height: 20px;
            background: get-icon('search', $Zinc-400) no-repeat center;
            z-index: 1;
        }

        .input-default__input {
            padding-left: 38px;
        }

        .input-default__label {
            padding: 10px 0 0 26px;
        }
    }

    &__pvz-search{
        @media(max-width: $screen-md){
            width: 212px;
        }
    }

    &__title-back{
        display: flex;
        align-items: center;
        @include _typography-ext(fn, 14, 20, 600, ls, $Zinc-500);
        cursor: pointer;

        &::before {
            content: '';
            display: block;
            width: 14px;
            height: 14px;
            margin-right: 8px;
            background: get-icon('tick-left', $Zinc-500) no-repeat center;
        }
    }

    &__closebtn {
        cursor: pointer;
    }
}

.map-wrapper {
    &__hidden {
        height: 1px;
        width: 1px;
        opacity: 0;
        z-index: -1;
    }

    &__active {
        height: 1080px;
        width: 100%;
        opacity: 1;
        z-index: 999;

        @media (max-width: $screen-xl){
            height: 374px;
        }
        @media (max-width: $screen-md){
            height: 100%;
        }
    }
}

.ymap-container {
    width: 100%;
    height: 100%;
}
</style>