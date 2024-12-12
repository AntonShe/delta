<template>
    <div v-show="isShow" class="preloader">

    </div>
</template>

<script>
export default {
    name: "BasePreloader",
    props: {
        isShow: {
            type: Boolean,
            default: false
        },
    },
}
</script>

<style lang="scss">
    .preloader {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba($white, .5);
    }
</style>