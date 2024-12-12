<template>
    <div class="page">
        <div class="page__sidebar">
            <base-menu></base-menu>
        </div>
        <section class="page__content">
            <div class="page__title">
                <slot name="title"></slot>
            </div>
            <slot></slot>
        </section>
    </div>
</template>

<script>
export default {
    name: "BasePage"
}
</script>

<style lang="scss">
    .page {
        display: flex;
        width: 100%;
        padding: 50px 50px 50px 0;

        &__sidebar{
            width: 150px;
            margin-right: 30px;
        }

        &__content{
            flex-grow: 1;
        }

        &__title {
            height: 50px;
        }
    }
</style>