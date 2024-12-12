<template>
	<div>
		<base-modal :isOpen="true" @close="hideModal" :withCross="false">
            <template v-slot:title>
                {{ getTitle }}
            </template>
			<div class="confirm">
				<div class="confirm__inputs">
					<confirm-inputs
                        :length=5
                        :isValidCode="isValidCode"
                        :key="inputsUpdater"
                        @enterCode="checkCode"
                        @updateValidCode="isValidCode=true"
					/>
				</div>
				<div class="confirm__text">
					<div v-if="isPhone">
                        На номер <span class="confirm__text_phone">{{ phone }}</span> отправили SMS&#8209;код. Введите его
					</div>
					<div v-else>
                        На почту <span class="confirm__text_email">{{ email }}</span> отправлено письмо с кодом. Введите его
					</div>
				</div>
			</div>

			<div class="confirm__btns">
				<button class="confirm__btn"
                        :class="{
                            'disabled': isRepeatDisabled
					    }"
                        @click="sendCode">
					Повторить попытку {{ getTimer }}
				</button>
			</div>
            <div v-if="isBlockedRepeat" class="preloader js-preloader"></div>
		</base-modal>

	</div>
</template>

<script>
import axios from "axios"
import ConfirmInputs from "./ConfirmInputs"
import BaseModal from "../../../base/BaseModal"

export default {
    name: "Confirm",
    components: {
        BaseModal,
        ConfirmInputs
    },
    emits: ['emitConfirm', 'replyCode', 'closePopup'],
	props: {
        type: String,
		phone: {
            type: String,
			default: null
		},
        email: {
            type: String,
            default: null
        },
	},
	data() {
        return {
			timer: 0,
            interval: null,
			code: null,
            isValidCode: true,
            isBlockedRepeat: false,
            inputsUpdater: 0
		}
	},
	mounted() {
        this.timer = 10
        this.decrementTimer();
    },
    computed: {
        isRepeatDisabled() {
            return this.timer > 0 || this.isBlockedRepeat;
		},
        getTimer() {
            return (this.timer > 0) ? this.timer : '' ;
		},
		isPhone() {
            return this.type === 'phone';
		},
		getTitle() {
            return (this.isPhone)
				? 'Изменить телефон'
				: 'Изменить почту';
		}
	},
	methods: {
        decrementTimer() {
            this.interval = setInterval(() => {
                if (this.timer === 0) {
                    clearInterval(this.interval);
                    this.interval = null;
                } else {
                    this.timer--
                }
            }, 1000);
		},
		confirm() {
            let pin = localStorage.getItem('pin')
            let pinKey = pin.includes('@') ? 'email' : 'phone'

            this.isBlockedRepeat = true
            axios.post('/user/verify-pin', {
                [pinKey]: pin,
                pin: this.code,
            }).then((result) => {
                this.isValidCode = result.data.result

                if (this.isValidCode) {
                    this.$emit('emitConfirm', this.isValidCode)                    
                } else {
                    this.inputsUpdater++
                    this.isBlockedRepeat = false
                }
            })
		},
        sendCode() {
            if (!this.isRepeatDisabled) {
                this.$emit('replyCode')
                this.timer = 10;
                this.decrementTimer()
            }
		},
        checkCode(code) {
            this.code = code;
            this.confirm();
		},
        hideModal() {
            this.$emit('closePopup', false);
		}
	}
}
</script>

<style lang="scss" scoped>
.confirm {
    &__inputs {
        text-align: center;
        margin-bottom: 10px;
    }

    &__text {
        margin-bottom: 10px;
        @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-500);

        &_email {
            color: $Indigo-400;
        }

        @media (max-width: $screen-md) {
          text-align: center;
        }
    }

    &__btns {
        display: flex;
        justify-content: end;
        gap: 18px;

        @media (max-width: $screen-md) {
            flex-direction: column-reverse;
            row-gap: 10px;
        }
    }
}

.confirm__btn {
    margin: auto;
    height: 44px;
    @include _button-reset(9px 16px, $Zinc-700, none);
    @include _typography-ext(fn, 16, 26, 600, ls, $white);

    @media (max-width: $screen-md) {
        width: 100%;
    }

    &:hover:not(.disabled) {
        background-color: $Zinc-900;
    }

    &.disabled {
        color: $Zinc-400;
        @include _button-reset(9px 16px, $Zinc-100, none);
        cursor: default;
    }
}
</style>