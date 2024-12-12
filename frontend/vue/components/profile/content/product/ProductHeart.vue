<template>
    <div :class="['favourite', {'favourite_cart': isCart}, {'favourite_popup': isPopup}]">
        <BaseTooltip :content="isActive ? 'Убрать из избранного' : 'В избранное'" :isCard="true">
            <div 
                :class="['favourite__btn', {'active': isActive}, {'favourite__btn_cart': isCart}, {'favourite__btn_popup': isPopup}]"
                @click="change"
            >
                {{ text }}
            </div>
        </BaseTooltip>        
    </div>
</template>

<script>
export default {
    name: "ProductHeart",
    props: {
        isFavourite: {
            type: Boolean,
            default: false
        },
        text: {
            type: String,
            default: null
        },
        isCart: {
            type: Boolean,
            default: false
        },
        isPopup: {
            type: Boolean,
            default: false 
        }
    },
    emits: ['toggle'],
    data() {
        return {
            isActive: this.isFavourite
        }
    },
    watch: {
        isFavourite() {
            this.isActive = this.isFavourite
        }
    },
    methods: {
        change() {
            this.isActive = !this.isActive;
            this.$emit('toggle', this.isActive);
        }
    },
}
</script>

<style lang="scss" scoped>
.favourite {
    padding: 10px;
    position: absolute;
    top: -10px;
    right: -10px;
    height: 52px;
    background: white;
    border-radius: 0 10px 0 30px;
    z-index: 3;

    @media (min-width: $screen-xl-l) {
        &:hover:not(:focus) {
            .button-like__tooltip {
                display: block;
            }
        }
    }
}
.favourite__btn {
    width: 32px;
    height: 32px;
    background: get-icon('like-card', $Zinc-900) no-repeat center/80%;
    @include _button-reset(0, bgc, b);

    &.active {
        background: get-icon('like-card-active', $Red-400) no-repeat center/80%;
    }
}

// themes
.favourite_popup {
    padding: 0;
    height: auto;
    background-position: 1px;
    background-size: 16px;
    position: static;

    &:hover:not(:focus) {
        background-position: 1px;
        background-size: 16px;
    }
}

.favourite_cart {
    padding: 0 0 0px 2px;
    position: absolute;
    right: 8px;
    top: 8px;
    width: 24px;
    height: 24px;
    z-index: 3;

    @media (max-width: $screen-md) {
        display: none;
    }
}

.favourite__btn_popup {
    width: 16px;
    height: 16px;
    padding-left: 24px;
    background-size: 16px;
}
.favourite__btn_cart {
    width: 24px;
    height: 24px;
}
</style>