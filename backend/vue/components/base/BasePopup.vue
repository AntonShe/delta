<template>
    <div class="base-popup" :style="zIndex">
        <div class="base-popup__overlap"></div>

        <div class="base-popup__wrapper" @click="close">
            <div class="base-popup__popup popup"
                :class="wrapperClass"
                :style="containerMaxHeight + containerMaxWidth"
                 @click.stop
            >

                <div class="popup__button" @click="close">
                    <div class="popup__button-line"></div>
                    <div class="popup__button-line popup__button-line_revert"></div>
                </div>

                <div class="popup__content-wrapper">
                    <div class="popup__content">
                        <slot></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'BasePopup',
    props: {
        wrapperClass: {
            type: String,
            default: ''
        },
        level: {
            type: Number,
            default: 1
        },
        maxHeight: {
            type: String,
            default: ''
        },
        maxWidth: {
            type: String,
            default: ''
        },
        isActive: {
            type: Boolean,
            default: 0
        }
    },
    computed: {
        zIndex() {
            return this.isActive ? 'z-index: ' + this.level * 100 + ';' : 'z-index: -1; opacity: 0;'
        },
        containerMaxHeight() {
            return this.maxHeight !== '' ? 'max-height: ' + this.maxHeight + ';height: 100%;' : ''
        },
        containerMaxWidth() {
            return this.maxWidth !== '' ? 'max-width: ' + this.maxWidth + ';width: 100%;' : ''
        }
    },
    emits: ['closePopup'],
    watch: {
        isActive(newVal) {
            newVal ? this.setOverflow(true) : this.setOverflow(false)
        }
    },
    methods: {
        close() {
            this.$emit('closePopup')
        },
        setOverflow(value) {
            window.document.querySelector('body').style.overflow = value ? 'hidden' : ''
        },
    },
}
</script>

<style lang="scss">
.base-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;

    &__overlap {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.6);
    }

    &__wrapper {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: center;
    }
}

.popup {
    background: white;
    height: 100%;
    max-width: 60vw;
    max-height: calc(100vh - 100px);
    padding: 30px;
    border-radius: 10px;
    position: relative;

    &__content-wrapper {
        height: inherit;
        overflow-y: scroll;
    }

    &__button{
        position: absolute;
        top: 5px;
        right: 5px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 25px;
        height: 25px;
        border-radius: 5px;
        border: 1px solid $gray;
        cursor: pointer;
        transition: .3s;

        &:hover {
            background-color: $lightgray;
        }
    }

    &__button-line {
        position: absolute;
        width: 15px;
        height: 1px;
        background-color: $black;
        transform: rotate(45deg);

        &_revert{
            transform: rotate(-45deg);
        }
    }
}
</style>