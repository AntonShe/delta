<template>
    <div v-if="isShow" class="preloader" :style="`z-index: ${level * 100}`"></div>
</template>

<script>
export default {
    name: "BasePreloader",
    props: {
        isShow: {
            type: Boolean,
            default: false
        },
        level: {
            type: Number,
            default: 5
        },
    },
    watch: {
        isShow(newVal) {
            newVal ? this.setOverflow(true) : this.setOverflow(false)
        },
    },
    methods: {
        setOverflow(value) {
            window.document.querySelector('body').style.overflow = value ? 'hidden' : ''
        },
    }
}
</script>

<style lang="scss">
    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba($white, .5);
    }
</style>