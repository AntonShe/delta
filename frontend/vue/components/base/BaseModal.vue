<template>
    <div v-show="isOpen" class="modal__overlay" @click.stop="close()">
        <div class="modal__container" @click.stop>
            <div class="modal__title">
                <slot name="title"/>
            </div>
            <div class="modal__content">
                <slot></slot>
            </div>
            <div class="modal__button" @click.stop="close" v-if="withCross">
                <div class="modal__button-line"></div>
                <div class="modal__button-line modal__button-line_revert"></div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "BaseModal",
    emits: ['close'],
    props: {
        isOpen: {
            type: Boolean,
            default: true
        },
        withCross: {
            type: Boolean,
            default: true
        }
    },
    methods: {
        close() {
            this.$emit('close')
        }
    },
    watch: {
        isOpen(newVal){
            window.document.querySelector('body').style.overflow = newVal ? 'hidden' : ''
        }
    },
}
</script>

<style lang="scss">
.modal {
    $root: modal;

    &__overlay {
        position: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(2px);
        overflow: auto;
        z-index: 5;

        @media (max-width: $screen-md) {
            align-items: start;
        }
    }

    &__container {
        position: relative;
        width: 440px;
        min-height: 190px;
        margin: 0 auto;
        box-shadow: 0 0 50px 5px rgba(0, 0, 0, 0.1);
        @include _border-radius(20px);
        overflow: hidden;
        background-color: $white;

        @media (max-width: $screen-md) {
            width: 100%;
            height: calc(100% - 66px);
            top: 66px;
            border-radius: unset;
        }        
    }

    &__title{
        padding: 14px 24px;
        background-color: $Red-25;
        @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-900);

        @media (max-width: $screen-md) {
            padding: 10px 15px 0 15px;
            background-color: $white;
            @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);            
        }
    }

    &__content {
        padding: 10px 24px 24px;
        height: 100%;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);

        @media (max-width: $screen-md) {
            padding: 10px 15px;
        }
    }

    &__button{
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 32px;
        height: 32px;
        border-radius: 5px;
        border: 1px solid $Zinc-400;
        cursor: pointer;

        &:hover {
            .#{$root}__button-line {
                width: 22px;
            }
        }
    }

    &__button-line {
        position: absolute;
        width: 20px;
        height: 1px;
        background-color: $black;
        transform: rotate(45deg);

        &_revert{
            transform: rotate(-45deg);
        }
    }
}

</style>