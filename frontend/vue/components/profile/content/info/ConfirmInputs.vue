<template>
	<div>
		<div>
			<input
				type="number"
				v-for="(letter, index) in getCodeLetters"
                v-imask="maskVal"
				:key="index"
				min="0"
				max="9"
                maxlength="1"
				@complete="setLetter(index)"
				@keydown="removeLetter(index)"
                ref="codeInput"
				:class="{
                    'error': !isValidCode
				}"
			>
		</div>
		<div class="error-message" v-if="!isValidCode">
            Неверный код. Попробуйте ещё раз.
		</div>
	</div>
</template>

<script>
import {IMaskDirective} from 'vue-imask'

export default {
    name: "ConfirmInputs",
	props: {
        length: {
            type: Number,
			default: 5
		},
        isValidCode: {
            type: Boolean,
            default: true
		},
	},
    directives: {
        imask: IMaskDirective
    },
	data() {
        return {
            code: Array(this.length).fill(''),
			focusIndex: 0,
            maskVal:  {
                mask: '0',
            },
		}
	},
	mounted() {
        this.setFocus(this.focusIndex);
    },
    methods: {
        removeLetter(index) {
            if (event.keyCode == 8 || event.keyCode == 46) {
                this.code[index] = event.target.value
                if (_.isEmpty(event.target.value)) {
                    this.focusIndex = index - 1
                    this.setFocus(this.focusIndex)
                }
                this.checkFilled(this.code)
                return
            }
        },
        setLetter(index) {
            this.code[index] = event.target.value
            this.focusIndex = index + 1

            if (index === 0 && this.isValidCode === false) this.$emit('updateValidCode')

            this.setFocus(this.focusIndex)
            this.checkFilled(this.code);
		},
        setFocus(index) {
            if (this.$refs.codeInput[index]) {
                this.$refs.codeInput[index].select();
			}
		},
        checkFilled(code) {
            let isFilled = true;
            code.forEach(letter => {
                if (String(letter).length === 0) {
                    isFilled = false;
				}
			});

            if (isFilled) {
                this.$emit('enterCode', code.join(''));
			}
		}
	},
	computed: {
        getCodeLetters() {
            return Array(this.length).fill('');
		}
	}
}
</script>

<style lang="scss" scoped>
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
	}

	input[type=number] {
		-moz-appearance:textfield;
        height: 44px;
        width: 34px;
        border: 1px solid $Zinc-400;
        text-align: center;
        @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-900);
        @include _border-radius(8px);

        &:not(:last-child) {
             margin-right: 6px;
        }
        &:hover {
            outline: none;
            border-color: $Zinc-600;
        }

        &:focus {
            outline: none;
            border-color: $Indigo-400;
            .input-default__input:hover{
                border-color: $Indigo-400;
            }
        }

        &.error {
            border-color: $Rose-400;
        }
	}

    .error-message {
        @include _typography-ext(fn, 12, 16, 400, ls, $Rose-600);
        margin-bottom: 10px;
        opacity: 1!important;
    }
</style>