<template>
    <div class="information__wrapper">
        <input-block-title title="Пароль"/>

        <div v-if="!isPasswordExist || isNeedChangePassword">
            <base-field
                id="new-password"
                name="new-password"
                placeholder="Введите пароль"
                type="password"
                autocomplete="false"
                value=""
                :mask="passMask"
                @updateInputValue="(value) => {this.setPassword(value, 0)}"
            />

            <base-field
                id="secondPass"
                name="secondPass"
                placeholder="Повторите пароль"
                type="password"
                value=""
                :mask="passMask"
                @updateInputValue="(value) => {this.setPassword(value, 1)}"
            />
        </div>

        <div v-else>
            <base-field
                id="stub"
                name="stub"
                type="password"
                value="Строка-для-пароля"
                :disabled="true"
            />
        </div>
        <div class="information__button-wrap">
            <div class="error-hit" v-if="showError">{{ errorText }}</div>
            <button
                v-if="isChange"
                @click="startChange()"
                class="information__button">
                Изменить пароль
            </button>

            <button
                v-if="isSaveChange"
                @click="resetPass"
                class="information__button information__button--red">
                Отмена
            </button>

            <button
                v-if="isSaveChange"
                :class="{'disabled': saveDisabled}"
                @click="savePass"
                class="information__button">
                Сохранить изменения
            </button>

            <button
                v-if="isSave"
                :class="{'disabled': saveDisabled || needEmail}"
                @click="savePass"
                class="information__button">
                Сохранить
            </button>

        </div>
    </div>
</template>

<script>
import InputBlockTitle from "../InputBlockTitle"
import BaseField from "../../../../base/BaseField"
import { mapActions, mapState } from 'vuex'

export default {
    name: "Password",
    components: {
        BaseField,
        InputBlockTitle
    },
    props: {
        isPasswordExist: {
            type: Boolean,
            default: false
        },
        isLegal: {
            type: Boolean,
            default: false
        },
        needEmail: {
            type: Boolean,
            default: false
        }
    },
    emits: ['setNeedEmail'],
    data() {
        return {
            firstPass: null,
            confirmPass: null,
            isNeedChangePassword: false,
            isClear: false,
            saveDisabled: true,
            passMask: {
                mask: /[0-9a-zа-яA-ZА-Я!@#$%&\-]+/
            },
            errorText: '',
            showError: false
        }
    },
    watch: {
        needEmail: function (newValue, oldValue) {
            if (newValue !== oldValue) {
                this.showError = newValue;
                this.errorText = newValue ? 'Чтобы установить пароль, подтвердите почту' : '';
            }
        },
    },
    computed: {
        ...mapState({
            userInfo: state => state.user.userInfo
        }),
        isChange() {
            return (this.isPasswordExist && !this.isNeedChangePassword)
        },
        isSaveChange() {
            return (this.isPasswordExist && this.isNeedChangePassword)
        },
        isSave() {
            return (!this.isChange && !this.isSaveChange)
        }
    },
    methods: {
        ...mapActions({
            updateUserInfo: 'user/updateUserInfo'
        }),        
        startChange() {
            this.isClear = true
            this.isNeedChangePassword = true
        },
        setPassword(value, type) {
            this.showError = false;

            if (type === 0) {
                this.firstPass = value
            } else if (type === 1) {
                this.confirmPass = value
            }

            this.saveDisabled = !(!_.isEmpty(this.confirmPass) && !_.isEmpty(this.firstPass) && this.confirmPass.length >= 8);
        },
        resetPass() {
            this.showError = false;
            this.firstPass = null;
            this.confirmPass = null;
            this.isClear = false;
            this.isNeedChangePassword = false;
        },
        savePass() {
            if (this.saveDisabled || this.needEmail) {
                return;
            }

            if (this.confirmPass !== this.firstPass) {
                this.showError = true;
                this.errorText = 'Пароли отличаются';
                return;
            }

            if (!_.isEmpty(this.userInfo.email)
                && this.userInfo.email !== undefined
            ) {
                this.updateUserInfo({
                    isLegal: this.isLegal,
                    password: this.confirmPass
                })

                this.firstPass = null
                this.confirmPass = null
                this.isNeedChangePassword = false
                this.isClear = false
                this.saveDisabled = true
            } else {
                this.$emit('setNeedEmail');
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.information {
    &__button-wrap {
        display: flex;
        gap: 10px;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: flex-end;

        @media (max-width: $screen-md) {
            flex-direction: column-reverse;
            flex-wrap: wrap;
        }
    }
}

.error-hit {
    margin: 0 15px 0 0;
    font-size: 12px;
    line-height: 16px;
    font-weight: 400;
    color: #E11D48;
    opacity: 1;
}
</style>