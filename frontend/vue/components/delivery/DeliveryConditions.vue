<template>
    <div v-if="type === 'zone'">
        <p class="delivery-conditions__text">
            Доставка тяжёлых (от 20 кг) и/или крупногабаритных заказов осуществляется при наличии
            парковочного места на расстоянии не более 250 метров от подъезда.
        </p>
        <p class="delivery-conditions__text">
            При отсутствии парковочного места на указанном расстоянии курьер связывается с получателем
            заказа по телефону и предлагает на выбор следующие варианты:
        </p>
        <p class="delivery-conditions__text">
            <span class="delivery-conditions__number">1. </span>
            Получение заказа непосредственно у машины
        </p>
        <p class="delivery-conditions__text">
            <span class="delivery-conditions__number">2. </span>
            Организация проезда на территорию дома/предприятия или парковки ближе к подъезду без
            нарушения ПДД (ожидание не более 15 минут). Если въезд на территорию вашего дома/предприятия
            ограничен, вы можете уточнить номер машины курьера заранее, чтобы подготовить пропуск.
            Доставка тяжелых (от 20 кг) и/или крупногабаритных заказов выше третьего этажа
            осуществляется при наличии лифта.
        </p>
    </div>
    <div v-if="type === 'pvz'">
        <h3 class="delivery-conditions__title">Что нужно для получения заказа:</h3>
        <p class="delivery-conditions__text">
            <span class="delivery-conditions__number">1. </span>
            Назвать номер заказа.
        </p>
        <p class="delivery-conditions__text">
            <span class="delivery-conditions__number">2. </span>
            Если заказ был предварительно оплачен, необходимо предъявить
            документ,удостоверяющий личность.Если предоплаченный заказ за вас получает кто-то
            другой,
            необходимо указать в заказе его ФИО.
        </p>
        <p class="delivery-conditions__text">
            <span class="delivery-conditions__number">3. </span>
            Для получения заказа, оформленного на юридическое лицо, требуется доверенность.
        </p>
    </div>

</template>

<script>
export default {
    name: "DeliveryConditions",
    props: {
        type: {
            type: String,
            default: ''
        }
    },
}
</script>

<style lang="scss" scoped>

.delivery-conditions {
    &__title {
        @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-900);
        margin-bottom: 20px;

        @media (max-width: $screen-md) {
            margin-bottom: 10px;
        }
    }

    &__text {
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
        margin: 0 0 16px 0;

        &:last-child {
            margin-bottom: 0;
        }
    }

    &__number {
        @include _typography-ext(fn, 16, 26, 400, ls, $Red-400);
    }
}
</style>