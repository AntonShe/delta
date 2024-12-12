<template>
    <div v-if="pvzInfo" class="pvz-info">
        <div class="pvz-info__description">
            <div class="pvz-info__touch-line">
                <div></div>
            </div>
            <div class="pvz-info__service">Л-Пост: {{ pvzInfo.address }}</div>
            <div v-if="pvzInfo.address" class="pvz-info__point-item">
                <div class="pvz-info__qs-point">Адрес</div>
                <div class="pvz-info_as-point">{{ pvzInfo.address }}</div>
            </div>
            <div v-if="getPriceDate.price" class="pvz-info__point-item">
                <div class="pvz-info__qs-point">Стоимость доставки</div>
                <div class="pvz-info__as-point">{{ getPriceDate.price }}</div>
            </div>
            <div v-if="getPriceDate.date" class="pvz-info__point-item">
                <div class="pvz-info__qs-date">Планируемая дата доставки</div>
                <div class="pvz-info__as-date">{{ getPriceDate.date }}</div>
            </div>
            <div>
                <button class="pvz-info__button" @click="pvzSelected">
                    Доставить сюда
                </button>
            </div>
        </div>
        <div class="pvz-info__options">
            <div v-if="pvzInfo.location_description" class="pvz-info__point-item">
                <div class="pvz-info__qs-point">Как добраться:</div>
                <div class="pvz-info__as-point">{{ pvzInfo.location_description }}</div>
            </div>
            <!-- TODO:
            <div class="pvz-info__point-item">
                <div class="pvz-info__qs-point">Ориентировочный срок доставки до пункта выдачи:</div>
                <div class="pvz-info__as-point">{{ getEstimateTime }}</div>
            </div>
            -->
            <div v-if="pvzInfo.schedule" class="pvz-info__point-item">
                <div class="pvz-info__qs-point">График работы:</div>
                <div class="pvz-info__as-point">
                    <ul>
                        <li v-for="dayWeek in pvzInfo.schedule" :key="dayWeek">
                            {{ dayWeek }}
                        </li>
                    </ul>
                </div>
            </div>
            <!-- TODO:
            <div class="pvz-info__point-item">
                <div class="pvz-info__qs-point">Срок хранения заказов в пункте выдачи:</div>
                <div class="pvz-info__as-point">{{ getKeepingTime }}</div>
            </div>
            -->
            <div class="pvz-info__point-item">
                <div class="pvz-info__qs-point">Банковские карты:</div>
                <div class="pvz-info__as-point">{{ isCanPayByCard }}</div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "PvzInfo",
    emits: ['pvzSelected'],
    props: {
        pvz: Object
    },
    data() {
        return {
            pvzInfo: null
        }
    },
    watch: {
        pvz: function (newValue, oldValue) {
            if (newValue !== oldValue) {
                this.setPvzInfo();
            }
        },
    },
    beforeMount() {
        this.setPvzInfo();
    },
    computed: {
        isCanPayByCard() {
            return (this.pvzInfo.is_card) ? 'Принимаются' : 'Не принимаются';
        },
        getPriceDate() {
            return {
                price: this.pvzInfo.price,
                date: this.pvzInfo.date,
            }
        },
        getEstimateTime() {
            return (this.pvzInfo.estimatedDeliveryTime)
                ? this.pvzInfo.estimatedDeliveryTime
                : '7 дней (без учета срока комплектования заказа)';
        },
        getKeepingTime() {
            return (this.pvzInfo.keepingTime) ? this.pvzInfo.keepingTime : '7 календарных дней';
        }
    },
    methods: {
        async setPvzInfo() {
            const pvz = this.pvz;
            await axios
                .post('/admin/backend/pvz/calculate', {
                    idPoint: pvz.id_point
                })
                .then(result => {
                    if (!_.isEmpty(result.data)) {
                        let resultData = result.data.JSON_TXT[0];
                        let rawData = new Date(resultData.DateClose);

                        pvz.price = parseInt(resultData.SumCost) + ' ₽'
                        pvz.date = rawData.toLocaleDateString('ru-Ru', {
                            day: 'numeric',
                            month: 'long',
                            weekday: 'short'
                        })
                    }
                    this.pvzInfo = pvz;
                })
                .catch((error) => {
                    this.pvzInfo = pvz;
                    throw new Error(error.message);
                });
        },
        pvzSelected() {
            this.$emit('pvzSelected', this.pvzInfo);
        },
    }
}
</script>

<style lang="scss" scoped>
.pvz-info {
    max-width: 360px;
    display: block;
    background-color: $white;
    padding: 30px 20px 30px 10px;
    height: 100%;
    min-width: 387px;
    position: absolute;
    z-index: 100;
    left: 0;
    top: 0;

    @include _border-radius(8px);
    margin-right: 30px;

    @media(max-width: $screen-xl) {
        order: 2;
        margin-right: 0;
        height: auto;
    }
    @media(max-width: $screen-md) {
        padding: 0 15px 40px 15px;
    }
    @media(max-width: $screen-md-l) {
        max-width: unset;
        position: relative;
    }

    &__point-item {
        margin-bottom: 10px;
    }

    &__qs-point {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-500);
        margin-bottom: 2px;
    }

    &__as-point {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
        margin-bottom: 10px;
    }

    &__as-date {
        @include _typography-ext(fn, 16, 26, 400, ls, $Green-600);

        @media (max-width: $screen-md) {
            @include _typography-ext(fn, 14, 20, 400, ls, $Green-600);
        }
    }

    &__service {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);

        &::before {
            content: '';
            display: block;
            width: 30px;
            height: 30px;
            background: get-icon('delivery') no-repeat;
            background-size: cover;
            margin-right: 6px;
        }
    }

    &__description {
        padding: 0 10px 30px;
        border-bottom: 1px solid $Zinc-200;

        @media(max-width: $screen-md) {
            padding: 0;
            border-bottom: none;
        }
    }

    &__options {
        padding-top: 20px;
    }

    &__button {
        width: 100%;
        @include _typography-ext(fn, 16, 26, 600, ls, $white);
        @include _button-reset(9px, $Zinc-700, none);
        @include _hover(bc, tc, bgi, $Zinc-900);
        margin-left: auto;
    }

    &__touch-line {
        display: none;
        padding: 10px 0;

        & > div {
            width: 30px;
            height: 4px;
            background: $Zinc-200;
            border-radius: 100px;
            margin: 0 auto;
        }

        @media (max-width: $screen-md) {
            display: block;
        }
    }
}

</style>