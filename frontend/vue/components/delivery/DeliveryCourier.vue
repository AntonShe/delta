<template>
    <div class="delivery-courier">
        <ul class="delivery-courier__price">
            <li class="delivery-courier__price-item">
                <p class="delivery-courier__date">{{ deliveryDate }}</p>
                <p>от {{ price }} ₽</p>
            </li>
        </ul>
        <div class="delivery-courier__info">
            <p class="delivery-courier__title">Л-Пост: <span>Курьером</span></p>
            <p>Адрес:</p>
            <p class="delivery-courier__address">{{ address }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "DeliveryCourier",
    props: {
        deliveryDate:{
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
        }
    },
}
</script>

<style lang="scss" scoped>
.delivery-courier {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 20px;
    @include _border-radius(0px 0px 8px 8px);
    background-color: $white;

    @media (max-width: $screen-md) {
        gap: 10px;
        padding: 20px 15px 0;
    }

    &__price {
        display: flex;
        flex-direction: column;
        gap: 6px;
        list-style: none;

        @media (max-width: $screen-xl-l) {
            max-width: 484px;
        }
    }

    &__price-item {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        align-items: center;
        padding: 10px;
        @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-900);
        @include _border-radius(8px);
        background: $Red-50;
    }

    &__date {
        display: flex;
        gap: 24px;
        align-items: center;

        &::before {
            content: '';
            display: block;
            width: 24px;
            height: 24px;
            background: get-icon('car', $Zinc-900);
        }
    }

    &__info {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 10px 0 10px 10px;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-500);

        @media (max-width: $screen-md) {
            gap: 0;
            padding: 0;
            font-size: 14px;
            line-height: 20px;
        }
    }

    &__title {
        display: flex;
        gap: 6px;
        align-items: center;
        margin-bottom: 10px;
        color: $Zinc-900;

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

    &__address {
        color: $Zinc-900;

        @media (max-width: $screen-md) {
            margin-top: 2px;
        }
    }
}
</style>