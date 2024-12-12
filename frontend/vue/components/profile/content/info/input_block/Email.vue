<template>
    <div class="physical-email information__wrapper">
        <InputBlockTitle title="Почта"/>

        <base-field
            id="new-email"
            name="new-email"
            autocomplete="false"
            placeholder="Email"
            class="input-default--error-top"
            :error="mailError"
            :value="emailValue"
            :mask="maskEmail"
            :class="{'accepted': email && !buttonReset}"
            @updateInputValue="changeEmail"
        />

        <div v-if="!mailError && !email" class="information__input-text">
            Подтвердите почту и первыми узнавайте о наших акциях
        </div>

        <div class="information__button-wrap">
            <button v-if="buttonReset" class="information__button information__button--red"
                    @click="resetEmail">
                Отмена
            </button>
            <button class="information__button"
                    :class="{
                        'disabled': isDisabledButton
					}"
                    @click="sendCode">
                Подтвердить почту
            </button>
        </div>

        <Confirm type="email"
                 :email="newEmail"
                 v-if="isShowConfirmModal"
                 @emitConfirm="checkConfirm"
                 @replyCode="sendCode"
                 @closePopup="toggleModal"
        />
    </div>
</template>

<script>
import Confirm from "../Confirm"
import InputBlockTitle from "../InputBlockTitle"
import {IMaskDirective} from "vue-imask"
import BaseField from "../../../../base/BaseField"
import axios from "axios"
import { mapActions } from 'vuex'

export default {
    name: "Email",
    components: {
        InputBlockTitle,
        Confirm,
        BaseField
    },
    directives: {
        imask: IMaskDirective
    },
    props: {
        email: {
            type: String,
            default: ''
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
            isDisabledButton: true,
            isShowConfirmModal: false,
            buttonReset: false,
            emailValue: this.email,
            newEmail: null,
            maskEmail: {
                mask: /[0-9a-zA-Z!@#$%&'*+\-/=?^\._`{|}~]+/,
            },
            mailError: ''
        }
    },
    watch: {
        needEmail: function (newValue, oldValue) {
            if (newValue !== oldValue && newValue) {
                this.mailError = 'Подтвердите почту';
            }
        },
    },
    methods: {
        ...mapActions({
            updateUserInfo: 'user/updateUserInfo'
        }),
        changeEmail(value) {
            this.emailValue = value;
            this.isDisabledButton = !this.validateEmail(value);
            this.newEmail = (this.isDisabledButton) ? null : value;
            this.buttonReset = !!this.email;
        },
        toggleModal() {
            this.isShowConfirmModal = !this.isShowConfirmModal;
        },
        resetEmail() {
            this.emailValue = this.email;
            this.newEmail = null;
            this.buttonReset = false;
            this.isDisabledButton = true;
        },
        validateEmail(value) {
            if (
                value === ''
                || typeof value === "undefined"
                || value.length < 5
                || value.length > 50
            ) return false

            let regularStr = /[a-zA-Z]+[a-zA-Z\d\!\#\$\%\&\’\;\+\-\.\=\?\^\_\`\{\}\½\~]*[a-zA-Z\d]?@[a-zA-Z]+[a-zA-Z\d\-\d]*[a-zA-Z\d]+\.[a-zA-Z][a-zA-Z]+/
            let matches = value.match(regularStr)

            if (_.isNull(matches)) return false

            return (value === matches[0] && value !== this.email)
        },
        sendCode() {
            if (!this.isDisabledButton) {
                axios.post('/user/send-pin',
                    {
                        email: this.newEmail,
                        isUpdate: 1
                    }
                ).then((result) => {
                        if (result.data.result === true) {
                            localStorage.setItem('pin', this.newEmail)
                            this.isShowConfirmModal = true
                        } else if (result.data.result === false) {
                            this.mailError = 'Не удалось отправить код. Попробуйте позже.'
                        } else {
                            this.mailError = result.data.result
                        }
                    }
                )
            }
        },
        checkConfirm(isConfirmed) {
            if (isConfirmed) {
                let pin = localStorage.getItem('pin')
                let pinKey = pin.includes('@') ? 'email' : 'phone'

                this.isShowConfirmModal = false;

                this.updateUserInfo({
                    isLegal: this.isLegal,
                    [pinKey]: pin
                })

                this.mailError = '';
                this.newEmail = null;
                this.isDisabledButton = true;
                this.buttonReset = false;
                this.$emit('setNeedEmail');
            }
            this.isShowConfirmModal = false;
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

.physical-email {
    margin-bottom: 20px;
}

</style>