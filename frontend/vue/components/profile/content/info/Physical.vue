<template>
    <div class="physical">
        <Personal :userInfo="userInfo" />

        <div class="physical__right-block">
            <Email
                :email="userInfo.email"
                :needEmail="needEmail"
                @setNeedEmail="needEmail = false"
            />
            <Password
                :isPasswordExist="userInfo.isPasswordExist"
                :needEmail="needEmail"
                @setNeedEmail="needEmail = true"
            />
        </div>
    </div>
</template>

<script>
import Personal from "./input_block/Personal"
import Password from "./input_block/Password"
import Email from "./input_block/Email"
import { mapState } from 'vuex'

export default {
    name: "Physical",
    components: {
        Email,
        Password,
        Personal
    },
    data() {
        return {
            needEmail: false
        }
    },
    computed: {
        ...mapState({
            userInfo: state => state.user.userInfo
        })
    }
}
</script>

<style lang="scss">
.physical {
    display: flex;
    gap: 20px;

    @media (max-width: $screen-xl-l) {
        flex-direction: column;
    }
}

.information {
    &__wrapper {
        padding: 20px;
        background-color: $white;
        width: 524px;
        @include _border-radius(8px);

        .input-default {
            margin-bottom: 10px;
        }

        @media (max-width: $screen-xl-l) {
            width: 100%;
            padding: 10px;
        }
    }

    &__button {
        height: 44px;
        @include _button-reset(9px 16px, $Zinc-700, none);
        @include _typography-ext(fn, 16, 26, 600, ls, $white);

        &--red {
            color: $Zinc-700;
            background-color: $Red-200;
            &:hover {
                background-color: $Red-300;
            }
        }

        &.disabled {
            color: $Zinc-400;
            @include _button-reset(9px 16px, $Zinc-100, none);
            cursor: default;
        }

        &:hover:not(.disabled, &--red) {
            background-color: $Zinc-900;
        }

        &-wrap {
            padding: 10px 0;
            text-align: end;
        }

        @media (max-width: $screen-md) {
            width: 100%;
        }
    }

    &__input-text {
        margin-bottom: 10px;
        @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-400);
    }
}
</style>