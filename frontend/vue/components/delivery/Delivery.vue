<template>
    <div class="bg-body">
        <section class="container delivery">
            <div class="delivery__info" :class="{ 'delivery__info--open': isPvzOpen || isAddressOpen }">
                <h1 class="delivery__title">Доставка</h1>
                <p class="delivery__label">
                    Укажите населённый пункт и способ получения, чтобы узнать подробности доставки
                </p>
                <DeliverySearch
                    :city="city"
                    :typeTab="typeTab"
                    :border-bottom="isPvzOpen || isAddressOpen"
                    @switchTab="switchTab"
                    @setCity="setCity"
                />
                <div v-if="wrongAddress" class="delivery__error">Уточните, пожалуйста, адрес</div>
                <div v-if="notDeliveryIncluded" class="delivery__error">
                    <p>Этот адрес не входит в зону доставки :(</p>
                    <p>Пожалуйста, выберите другой адрес</p>
                </div>
                <div v-if="isAddressOpen && addressInfo" ref="touchBlock" class="delivery__courier">
                    <div ref="touchLine" class="delivery__touch-line"></div>
                    <DeliveryCourier
                        :delivery-date="addressInfo.deliveryDate"
                        :address="addressInfo.address"
                        :price="addressInfo.price"
                    />
                    <div class="delivery__block">
                        <DeliveryConditions type="zone"/>
                    </div>
                </div>
                <div v-if="isPvzOpen && pvzInfo" ref="touchBlock" class="delivery__pvz">
                    <div ref="touchLine" class="delivery__touch-line"></div>
                    <DeliveryPvz
                        :card="pvzInfo.card"
                        :location-description="pvzInfo.locationDescription"
                        :delivery-date="pvzInfo.deliveryDate"
                        :address="pvzInfo.address"
                        :price="pvzInfo.price"
                        :schedule="pvzInfo.schedule"
                    />
                    <div class="delivery__pvz-conditions delivery__pvz-conditions--desktop-hidden">
                        <DeliveryConditions type="pvz"/>
                    </div>
                </div>
            </div>
            <div ref="map" class="delivery__map" :class="{ 'delivery__map--order-first': isPvzOpen || isAddressOpen }">
                <DeliveryMap
                    :city="city"
                    :type-delivery="typeTab"
                    @setPvzInfo="setPvzInfo"
                    @addressClick="setAddressInfo"
                />
            </div>
            <div class="delivery__pvz-conditions delivery__pvz-conditions--mobile-hidden">
                <DeliveryConditions v-if="typeTab === 'pvz' && isPvzOpen" type="pvz"/>
            </div>
        </section>
    </div>
</template>

<script>
import DeliveryPvz from "./DeliveryPvz";
import DeliveryCourier from "./DeliveryCourier";
import DeliveryMap from "./DeliveryMap";
import DeliveryConditions from "./DeliveryConditions";
import DeliverySearch from "./DeliverySearch";

export default {
    name: "Delivery",
    components: {
        DeliverySearch,
        DeliveryCourier,
        DeliveryPvz,
        DeliveryConditions,
        DeliveryMap
    },
    data() {
        return {
            typeTab: 'zone',
            city: 'Москва',
            isPvzOpen: false,
            pvzInfo: null,
            isAddressOpen: false,
            addressInfo: null,
            notDeliveryIncluded: false,
            wrongAddress: false,
        }
    },
    updated() {
        if (this.isPvzOpen && this.pvzInfo || this.isAddressOpen && this.addressInfo) {
            this.initialTouchLine();
        }
    },
    methods: {
        switchTab(id) {
            this.resetData();
            this.typeTab = id;
        },
        setCity(city) {
            this.resetData();
            this.city = city;
        },
        setPvzInfo(pvzInfo) {
            this.resetData();

            if (pvzInfo) {
                this.pvzInfo = pvzInfo;
                this.isPvzOpen = true;
            }
        },
        setAddressInfo(addressInfo) {
            this.resetData();

            if (!addressInfo) {
                this.wrongAddress = true;
            } else if (Object.keys(addressInfo).length === 0) {
                this.notDeliveryIncluded = true;
            } else {
                this.addressInfo = addressInfo;
                this.isAddressOpen = true;
            }
        },
        resetData() {
            this.isPvzOpen = false;
            this.isAddressOpen = false;
            this.notDeliveryIncluded = false;
            this.wrongAddress = false;
            this.pvzInfo = null;
            this.addressInfo = null;
        },
        initialTouchLine() {
            const {touchLine, touchBlock, map} = this.$refs;

            if (!touchLine || !touchBlock || !map) {
                return;
            }

            let initialCoordinate = null;
            let swipeTop = null;
            const mapHeight = map.getBoundingClientRect().height - 10;

            touchLine.addEventListener('touchmove', (event) => {
                event.preventDefault();
                const coordY = Math.floor(event.changedTouches[0].pageY);

                if (initialCoordinate === null && swipeTop === null) {
                    initialCoordinate = coordY;
                    swipeTop = touchBlock.className.split(' ').includes(`${touchBlock.className.split(' ')[0]}--swipe-top`);
                }

                const swipeValue = initialCoordinate - coordY;

                if (!swipeTop && swipeValue >= 0 && swipeValue <= mapHeight) {
                    touchBlock.style.marginTop = `${mapHeight - swipeValue}px`;
                }
                if (swipeTop && swipeValue <= 0 && Math.abs(swipeValue) <= mapHeight) {
                    touchBlock.style.marginTop = `${Math.abs(swipeValue)}px`;
                }
            })
            touchLine.addEventListener('touchend', (evt) => {
                evt.preventDefault();
                const coordY = Math.floor(event.changedTouches[0].pageY);
                const swipeValue = initialCoordinate - coordY;

                if (swipeTop && swipeValue < -50) {
                    touchBlock.classList.remove(`${touchBlock.className.split(' ')[0]}--swipe-top`);
                }
                if (!swipeTop && swipeValue > 50) {
                    touchBlock.classList.add(`${touchBlock.className.split(' ')[0]}--swipe-top`);
                }

                touchBlock.removeAttribute('style');
                initialCoordinate = null;
                swipeTop = null;
            })
        }
    }
}
</script>

<style lang="scss" scoped>

.bg-body {
    background-color: $Zinc-50;
}

.delivery {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding-top: 20px;
    padding-bottom: 50px;

    @media (max-width: $screen-xl-l) {
        flex-direction: column;
        padding-bottom: 0;
    }

    @media (max-width: $screen-md) {
        padding: 66px 0 0 0;
        background: $white;
    }

    &__info {
        min-width: 524px;
        max-width: 524px;

        @media (max-width: $screen-xl-l) {
            min-width: 100%;
            max-width: 100%;
        }

        &--open {
            @media (max-width: $screen-xl-l) {
                order: 2;
                padding-bottom: 40px;

                .delivery__title,
                .delivery__label {
                    display: none;
                }
            }
        }
    }

    &__title {
        @include _typography-ext(fn, 30, 36, 600, ls, $Zinc-900);
        margin: 0 0 20px;

        @media (max-width: $screen-md) {
            display: none;
        }
    }

    &__label {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
        margin-bottom: 20px;

        @media (max-width: $screen-md) {
            display: none;
        }
    }

    &__map {
        height: 100vh;
        max-height: 800px;
        width: 100%;
        max-width: calc(100% - 544px);

        @media (max-width: $screen-xl-l) {
            height: 370px;
            min-width: 100vw;
            max-width: 100vw;
            margin-left: calc((100vw - 100%) / -2);
        }

        @media (max-width: $screen-md) {
            position: absolute;
            top: 176px;
            left: 0;
            height: calc(50vh + 10px);
        }

        &--order-first {
            @media (max-width: $screen-xl-l) {
                order: 1;
            }
        }
    }

    &__touch-line {
        display: none;

        @media (max-width: $screen-md) {
            position: absolute;
            display: flex;
            justify-content: center;
            top: 0;
            left: 0;
            right: 0;
            padding: 10px 0 64px;
        }        

        &:before {
            content: '';
            width: 30px;
            height: 4px;
            background: $Zinc-200;
            border-radius: 100px;
            margin: 0 auto;
        }
    }

    &__block {
        margin-top: 20px;

        @media (max-width: $screen-md) {
            margin: 20px 15px 0;
        }
    }

    &__pvz-conditions {
        @media (max-width: $screen-xl-l) {
            margin-top: 44px;
            padding: 10px;
            @include _border-radius(8px);
            background: $white;
        }

        @media (max-width: $screen-md) {
            margin: 20px 15px 0;
            padding: 0;
        }

        &--desktop-hidden {
            display: none;

            @media (max-width: $screen-xl-l) {
                display: block;
            }
        }

        &--mobile-hidden {
            display: block;

            @media (max-width: $screen-xl-l) {
                display: none;
            }
        }
    }

    &__pvz,
    &__courier {
        @media (max-width: $screen-md) {
            position: relative;
            padding-top: 14px;
            margin-top: 50vh;
            @include _border-radius(8px 8px 0 0);
            z-index: 5;
            background: $white;
            box-shadow: 0px -4px 6px 0px rgba(0, 0, 0, 0.10);
        }

        &--swipe-top {
            @media (max-width: $screen-md) {
                margin-top: 0;
            }
        }
    }

    &__error {
        padding: 10px 20px;
        @include _typography-ext(fn, 14, 18, 400, ls, $Rose-600);

        @media (max-width: $screen-md) {
            position: absolute;
            top: 176px;
            z-index: 5;
            left: 0;
            right: 0;
            text-align: center;
            background: $white;
        }
    }
}
</style>