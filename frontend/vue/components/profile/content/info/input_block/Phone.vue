<template>
    <div>
        <InputBlockTitle title="Телефон" v-if="withTitle"/>

        <base-field
                id="phone"
                name="phone"
                placeholder="Телефон"
                :value="newPhone"
                @updateInputValue="setPhone"
                :class="{
                    'accepted': isSuccess,
                    'phone-field': true
                }"
				:isRequired="isRequired"
                :mask="{mask: '{+7} (000) 000-00-00'}"
                :error="phoneError"
        />

        <Confirm type="phone"
                 :phone="newPhone"
                 v-if="isShowConfirmModal"
                 @emitConfirm="checkConfirm"
                 @closePopup="() => {this.isShowConfirmModal = false}"
                 @replyCode="confirmPhone"
        />

        <div class="phone__text" v-if="isPhoneNew">
            Отправим на ваш номер SMS-сообщение с кодом подтверждения
        </div>
        <div class="phone__btn-wrap" v-if="isPhoneNew">
			<button class="phone__btn" @click="confirmPhone">
                Подтвердить телефон
			</button>
        </div>
    </div>
</template>

<script>
import axios from "axios"
import Confirm from "../Confirm"
import InputBlockTitle from "../InputBlockTitle"
import BaseField from "../../../../base/BaseField"
import { mapState } from 'vuex'

export default {
    name: "Phone",
    components: {
        BaseField,
        InputBlockTitle,
        Confirm
    },
	props: {
        phone: String,
        withTitle: {
            type: Boolean,
            default: false
        },
		isRequired: {
            type: Boolean,
            default: false
        },
        isCabinet: {
            type: Boolean,
            default: false
        },
	},
    emits: ['setPhone', 'confirm'],
	data() {
        return {
            isPhoneNew: false,
			newPhone: this.phone,
            isShowConfirmModal: false,
            phoneError: '',
            isSuccess: !_.isEmpty(this.phone)
		}
	},
    computed: {
        ...mapState({
            userInfo: state => state.user.userInfo
        }),        
        isValidData() {
            if (!this.isCabinet) return true

            return !_.isEmpty(this.userInfo.lastName) && !_.isEmpty(this.userInfo.firstName)
        }
    },
	methods: {
        setPhone(value) {
            if (value.length == 12) {
                let phone = value[0] + value[1] + ' (' + value[2] + value[3] + value[4] + ') ' + value[5] + value[6] + value[7] + '-' + value[8] + value[9] + '-' + value[10] + value[11]

                this.newPhone = phone
                this.isPhoneNew = this.isSuccess = true
                this.$emit('setPhone', this.newPhone)
            } else {
                this.isSuccess = false
            }
		},
        checkConfirm(isConfirmed) {
            this.isShowConfirmModal = false

            this.$emit('confirm', isConfirmed)
		},
        confirmPhone() {
            if (this.isValidData) {
                axios.post('/user/send-pin',
                    {
                        phone: this.newPhone,
                        isUpdate: 1
                    }
                ).then((result) => {
                        if (result.data.result === true) {
                            localStorage.setItem('pin', this.newPhone)
                            this.phoneError = ''
                            this.isPhoneNew = false
                            this.isShowConfirmModal = true
                        } else if (result.data.result === false) {
                            this.phoneError = 'Не удалось отправить код. Попробуйте позже.'
                        } else {
                            this.phoneError = result.data.result
                        }
                    }
                )
            } else {
                this.phoneError = 'Сначала заполните Имя и Фамилию'
            }
		},
	}
}
</script>

<style lang="scss" >
.phone {
    &__text {
        @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-500);
    }

    &__btn {
        height: 44px;
        @include _button-reset(9px 16px, $Zinc-700, none);
        @include _typography-ext(fn, 16, 26, 600, ls, $white);

        &.disabled {
            color: $Zinc-400;
            @include _button-reset(9px 16px, $Zinc-100, none);
        }

        &:hover:not(.disabled) {
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
}

.phone-field {
    $root: input-default;

    .#{$root}__hint {
        margin-top: 5px;
    }
}


</style>