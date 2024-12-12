<template>
    <div :class="containerClass">
        <label :for="id"
               :class="defaultDynamicClasses"
               class="input-default__label">
            {{ labelText }}
        </label>
        <input :value="inputValue"
               class="input-default__input"
               :autocomplete="autocomplete"
               :id="id"
               :name="name"
               :type="isPassword ? viewableType : type"
               :class="defaultDynamicClasses"
               :placeholder="placeholder"
               v-imask="mask"
               @focus="inputFocus"
               @blur="inputBlur"
               @accept="updateInput"
               @change="changeInput"
               :readonly="!isEditable"
        />
        <div v-if="!isEmpty && !isPassword" class="input-default__btn" @click="clearInput">
            <div class="input-default__button-line"></div>
            <div class="input-default__button-line input-default__button-line_revert"></div>
        </div>
        <div v-if="(isActive || !isEmpty) && isPassword" class="input-default__btn" @click="changeViewableType">
            <svg class="svg-store__svg-passeye" width="16" height="11" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.99984 1.99996C9.22566 1.99588 10.4278 2.33782 11.4679 2.98644C12.5081 3.63507 13.3441 4.56407 13.8798 5.66663C12.7798 7.91329 10.5265 9.33329 7.99984 9.33329C5.47317 9.33329 3.21984 7.91329 2.11984 5.66663C2.65558 4.56407 3.49159 3.63507 4.53176 2.98644C5.57192 2.33782 6.77401 1.99588 7.99984 1.99996ZM7.99984 0.666626C4.6665 0.666626 1.81984 2.73996 0.666504 5.66663C1.81984 8.59329 4.6665 10.6666 7.99984 10.6666C11.3332 10.6666 14.1798 8.59329 15.3332 5.66663C14.1798 2.73996 11.3332 0.666626 7.99984 0.666626ZM7.99984 3.99996C8.44186 3.99996 8.86579 4.17555 9.17835 4.48811C9.49091 4.80068 9.6665 5.2246 9.6665 5.66663C9.6665 6.10865 9.49091 6.53258 9.17835 6.84514C8.86579 7.1577 8.44186 7.33329 7.99984 7.33329C7.55781 7.33329 7.13389 7.1577 6.82133 6.84514C6.50876 6.53258 6.33317 6.10865 6.33317 5.66663C6.33317 5.2246 6.50876 4.80068 6.82133 4.48811C7.13389 4.17555 7.55781 3.99996 7.99984 3.99996ZM7.99984 2.66663C6.3465 2.66663 4.99984 4.01329 4.99984 5.66663C4.99984 7.31996 6.3465 8.66663 7.99984 8.66663C9.65317 8.66663 10.9998 7.31996 10.9998 5.66663C10.9998 4.01329 9.65317 2.66663 7.99984 2.66663Z" fill="#797979"/></svg>
        </div>
        <div v-if="errorText" class="input-default__hint">
            <p>{{ errorText }}</p>
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
            isEditable: {
                type: Boolean,
                default: true
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
                viewableType: this.type
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
                    empty: this.isEmpty
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
                // Генерируем пользовательское событие после того, как обработается событие accept
                this.$nextTick(() => {
                    this.$emit('updateInputValue', '', this.name)
                    this.$emit('changeInputValue', '', this.name)
                })
            },
            changeViewableType() {
                this.viewableType = this.viewableType === 'text' ? 'password' : 'text'
            }
        }
    }
</script>

<style lang="scss">
.input-default {
    position: relative;

    &__input {
        width: 100%;
        height: 40px;
        border: 1px solid #C8C8C8;
        border-radius: 4px;
        box-shadow: none;
        padding-left: 12px;
        padding-right: 40px;
        text-overflow: ellipsis;

        &:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
        }

        @include placeholders(#505050, Golos-R, 15px, 21px);

        &:focus {
            outline: none;
            border: 1px solid #d1d5db;
        }

        &.error {
            border: 1px solid $error-color;
        }
    }

    &__label {
        opacity: 1;
        position: absolute;
        font-family: Golos-R, sans-serif;
        font-size: 12px;
        line-height: 17px;
        transition: .3s;
        top: -8px;
        left: 12px;
        background-color: #ffffff;
        padding: 0 4px;
        color: #505050;

        &.empty {
            opacity: 0;
        }

        &.active {
            color: #5B2599;
        }

        &.error {
            color: $error-color;
        }
    }

    &__hint {
        width: 100%;
        margin-left: 12px;
        margin-top: 2px;
        font-family: Golos-R, sans-serif;
        font-size: 12px;
        line-height: 17px;
        color: $error-color;
    }

    &__btn {
        position: absolute;
        top: 16px;
        right: 19px;
        cursor: pointer;

        &_big{
            right: 16px;
        }

        &:hover{
            path{
                //fill: $purple;
                transition: .3s;
            }
        }
    }
}
</style>
