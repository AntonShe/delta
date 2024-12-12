<template>
    <div :class="containerClass">
        <label :for="id"
               :class="[defaultDynamicClasses, {'input-default__label_orders': isProfileOrders}, {'input-default__label_address-det': isAddressDetails}]"
               class="input-default__label">
            {{ placeholder }}<span v-if="isRequired">*</span>
        </label>
        <input :value="inputValue"
               class="input-default__input"
               :autocomplete="autocomplete"
               :id="id"
               :name="name"
               :type="isPassword ? viewableType : type"
               :class="[defaultDynamicClasses, {'input-default__input_orders': isProfileOrders}, {'input-default__input_address-det': isAddressDetails}]"
               :disabled="disabled"
               v-imask="mask"
               @focus="inputFocus"
               @blur="inputBlur"
               @accept="updateInput"
               @change="changeInput"
			   ref="input"
        />
        <div
            v-if="!isEmpty && withPencil && !isPassword"
            @click="() => {this.$refs.input.select()}"
            class="input-default__btn input-default__btn--pencil">
            <BaseSvgStore icon="pencil" />
        </div>
        <div
            v-if="!isEmpty && !isPassword && !withPencil"
            @click="clearInput"
            class="input-default__btn">
			<BaseSvgStore icon="x" />
        </div>
        <div v-if="(isActive || !isEmpty) && isPassword && !disabled" class="input-default__btn" @click="changeViewableType">
            <div v-if="viewableType === 'text'"><BaseSvgStore icon="eye" /></div>
            <div v-if="viewableType === 'password'"><BaseSvgStore icon="eye-off" /></div>
        </div>
        <div v-if="errorText" class="input-default__hint">
            <p v-html="errorText"></p>
        </div>
    </div>
</template>

<script>
    import {IMaskDirective, IMask} from 'vue-imask'

    export default {
        name: "BaseField",
        directives: {
            imask: IMaskDirective
        },
        emits: ['inputFocus', 'inputBlur', 'updateInputValue', 'changeInputValue'],
        props: {
            name: {
                type: String,
                required: true
            },
            id: {
                type: String,
                required: true
            },
            value: {
                default: ''
            },
            type: {
                type: String,
                default: 'text'
            },
            autocomplete: {
                type: String,
                default: 'off'
            },
            placeholder: {
                type: String,
            },
            labelText: {
                type: String
            },
            additionalContainerClass: {
                type: String,
                default: ''
            },
            error: {
                type: String
            },
            /**
             * Ожидает объект с данными для маски
             * см. https://github.com/uNmAnNeR/imaskjs/tree/master/packages/vue-imask#mask-directive-example
             */
            mask: {
                type: Object,
                default() {
                    return {
                        mask: /^.+$/
                    }
                },
                validator(value) {
                    return value.hasOwnProperty('mask') &&
                        ((value.mask instanceof RegExp) || (typeof value.mask === 'string')
                            || (typeof value.mask === 'function'))
                }
            },
            returnUnmaskedValue: {
                type: Boolean,
                default: true
            },
			isRequired: {
                type: Boolean,
                default: false
			},
			isNeedPencil: {
                type: Boolean,
                default: false
            },
            disabled: {
                type: Boolean,
                default: false
            },
            isProfileOrders: {
                type: Boolean,
                default: false
            },
            isAddressDetails: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                isActive: false,
                inputValue: this.value,
                inputUnmaskedValue: IMask.pipe(this.value, this.mask),
                errorText: this.error,
                containerClass: 'input-default' + ' ' + this.additionalContainerClass,
                isPassword: this.type === 'password',
                viewableType: this.type,
				withPencil: !this.isEmpty && this.isNeedPencil
            }
        },
        computed: {
            isEmpty() {
                return !(this.inputValue.length > 0)
            },
            defaultDynamicClasses() {
                return {
                    active: this.isActive,
                    error: !_.isEmpty(this.errorText),
                    empty: this.isEmpty,
					required: this.isRequired,
					pencil: this.withPencil
                }
            },
            returnValue() {
                return this.returnUnmaskedValue
                    ? this.inputUnmaskedValue
                    : this.inputValue
            }
        },
        watch: {
            value(newValue) {
                this.inputValue = newValue
            },
            error(newValue) {
                this.errorText = newValue
            }
        },
        methods: {
            inputFocus() {
                this.isActive = !this.isActive
                this.$emit('inputFocus', true)
            },
            inputBlur() {
                this.isActive = !this.isActive
                this.$emit('inputBlur', true)
            },
            updateInput(e) {
                if (this.inputValue === e.detail.value) {
                    return
                }
                this.inputValue = e.detail.value
                this.inputUnmaskedValue = e.detail.unmaskedValue
                this.$emit('updateInputValue', this.returnValue, this.name)
            },
            changeInput(e) {
                if (this.value === e.target.value) {
                    return
                }
                this.inputValue = e.target.value
                this.$emit('changeInputValue', this.returnValue, this.name)
            },
            clearInput() {
                this.errorText = ''
                this.inputValue = ''
                this.$refs.input.focus()
                // Генерируем пользовательское событие после того, как обработается событие accept
                this.$nextTick(() => {
                    this.$emit('updateInputValue', '', this.name)
                    this.$emit('changeInputValue', '', this.name)
                })
            },
            changeViewableType() {
                this.viewableType = this.viewableType === 'text' ? 'password' : 'text'
            },
        }
    }
</script>

<style lang="scss" scoped>
.input-default {
    position: relative;

    &--error-top {
        .input-default__hint {
            margin: 0;
        }
    }

    &.accepted {
        .input-default__input {
            padding-left: 42px;
            background: get-icon('check', $Green-600) no-repeat left;
        }

        .input-default__label.empty {
            left: 53px;
        }
    }

    &__input {
        width: 100%;
        height: 44px;
        border: 0;
        border-bottom: 1px solid $Zinc-400;
        border-radius: unset;
        box-shadow: none;
        padding: 9px 12px;
        text-overflow: ellipsis;
        @include _typography-ext(fn, 16, 26, 400, ls, fc);
        @include placeholders($Zinc-500, $inter, 16px, 26px);        

        &:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
        }

        &:hover:not(:disabled) {
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

        &:disabled {
            background-color: transparent;
        }

        &.error {
            border-bottom: 1px solid  $Rose-600;
        }
    }

    &__input:focus + &__btn--pencil svg path{
        stroke: $Indigo-400;
    }

    &__btn--pencil svg{
        width: 20px;
        height: 20px;
    }

    &__hint {
        width: 100%;
        margin-top: -8px;
        margin-bottom: 10px;
        @include _typography-ext(fn, 14, 20, 400, ls, $Rose-600);
    }

    &__btn {
        position: absolute;
        top: 50%;
        right: 8px;
        transform: translateY(-50%);
        cursor: pointer;

        &:hover{
            path{
                transition: .3s;
            }
        }        

        &_big{
            right: 16px;
        }
    }
}

.input-default__label {
    @include placeholder($Zinc-500, $inter, 16px, 26px);
    position: absolute;
    left: 24px;

    &:not(.empty) {
        top: 2px;
        left: 0;
        font-size: 12px;
        line-height: 18px;
        color: $Zinc-400;

        span {
            font-size: 12px;
        }
    }

    & span {
        @include placeholder($Rose-600, $inter, 16px, 26px);
    }
}

// themes
.input-default__label_orders {
    white-space: nowrap;
    left: 38px;
}

.input-default__input_orders {
    padding: 9px 12px 9px 38px;
    border-radius: 10px;
    border: 1px solid $Zinc-400;
}

.input-default__label_address-det {
    @media (max-width: $screen-md) {
        padding: 8px 0;
        @include _typography-ext(fn, 14, 20, 600, ls, $Zinc-500);
        top: 0;
        left: 6px;        
    }
}

.input-default__input_address-det {
    @media (max-width: $screen-md) {
        padding: 0;
        @include _typography-ext(fn, 14, 20, 600, ls, $Zinc-900);
    }
}
</style>
