<template>
    <div class="delivery-pvz">
        <div class="delivery-pvz__info">
            <p class="delivery-pvz__title">Л-Пост: <span>В пункт выдачи</span></p>
            <ul class="delivery-pvz__info-list">
                <li class="delivery-pvz__info-item">
                    <p>Адрес:</p>
                    <p class="delivery-pvz__text">{{ address }}</p>
                </li>
                <li v-if="price" class="delivery-pvz__info-item">
                    <p>Стоимость доставки</p>
                    <p class="delivery-pvz__text">от <span>{{ price }}</span> ₽</p>
                </li>
                <li v-if="deliveryDate" class="delivery-pvz__info-item">
                    <p>Планируемая дата доставки</p>
                    <p class="delivery-pvz__text delivery-pvz__text--green">{{ deliveryDate }}</p>
                </li>
            </ul>
        </div>
        <div class="delivery-pvz__point">
            <ul class="delivery-pvz__point-list">
                <li v-if="locationDescription" class="delivery-pvz__point-item">
                    <p>Как добраться:</p>
                    <p class="delivery-pvz__text">{{ locationDescription }}</p>
                </li>
                <li v-if="schedule" class="delivery-pvz__point-item">
                    <div>График работы:</div>
                    <div class="delivery-pvz__text">
                        <ul>
                            <li v-for="dayWeek in schedule" :key="dayWeek">
                                {{ dayWeek }}
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="delivery-pvz__point-item">
                    <p>Срок хранения заказов в пункте выдачи:</p>
                    <p class="delivery-pvz__text">7 календарных дней</p>
                </li>
                <li class="delivery-pvz__point-item">
                    <p>Банковские карты:</p>
                    <p class="delivery-pvz__text">{{ isCard }}</p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    name: "DeliveryPvz",
    props: {
        card: {
            type: Boolean,
            default: false
        },
        locationDescription: {
            type: String,
            default: ''
        },
        deliveryDate: {
            type: String,
            default: ''
        },
        price: {
            type: Number,
            default: 0
        },
        address: {
            type: String,
            default: ''
        },
        schedule: {
            type: Array,
            default: null
        }
    },
    computed: {
        isCard() {
            return this.card ? 'Принимаются' : 'Не принимаются';
        }
    }
}
</script>

<style lang="scss" scoped>
.delivery-pvz {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 20px 20px 10px 10px;
    @include _border-radius(0px 0px 8px 8px);
    background-color: $white;

    @media (max-width: $screen-md) {
        padding: 20px 15px 0 15px;
    }

    &__info {
        padding-left: 10px;

        @media (max-width: $screen-md) {
            padding: 0;
        }
    }

    &__title {
        display: flex;
        gap: 6px;
        align-items: center;
        margin-bottom: 10px;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);

        @media (max-width: $screen-md) {
            font-size: 14px;
            line-height: 20px;
        }

        &::before {
            content: '';
            display: block;
            width: 30px;
            height: 30px;
            background: get-icon('delivery') no-repeat;
            background-size: cover;
        }
    }

    &__point {
        padding: 20px 0 0 10px;
        border-top: 1px solid #e4e4e7;

        @media (max-width: $screen-md) {
            padding: 20px 0;
            border-bottom: 1px solid #e4e4e7;
        }
    }

    &__info-list,
    &__point-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        list-style: none;
    }

    &__info-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-500);

        @media (max-width: $screen-md) {
            font-size: 14px;
            line-height: 20px;
        }
    }

    &__point-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-500);
    }

    &__text {
        color: $Zinc-900;

        &--green {
            color: $Green-600;
        }
    }
}
</style>