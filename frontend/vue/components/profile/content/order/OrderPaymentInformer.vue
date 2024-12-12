<template>
    <div class="payment-informer">После оплаты заказа подождите, пожалуйста, 5 минут, чтобы информация обновилась</div>
</template>

<style lang="scss" scoped>
.payment-informer {
    @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-600);
    padding: 2px 8px;
    height: fit-content;
    background-color: $Orange-50;
    border-radius: 4px;
}
</style>