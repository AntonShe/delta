<template>
    <div class="base-select"
         :class="additionalContainerClass"
         ref="container"
    >
        <div class="base-select__label">{{ label }}</div>
        <div class="base-select__field" :class="{'base-select__field_open': dropped}" @click.stop="toggle()">
            <div class="base-select__text" v-html="selectItemText"></div>
            <div class="base-select__placeholder" v-if="selectItemText === ''">{{ placeholder }}</div>

            <svg class="base-select__ico" :class="{'base-select__ico_down': dropped}" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.59 0.589844L6 5.16984L1.41 0.589844L0 1.99984L6 7.99984L12 1.99984L10.59 0.589844Z" fill="#505050"/>
            </svg>

        </div>
        <div v-if="isError" class="base-select__hint">{{ hint }}</div>
        <div class="base-select__dropdown"
             :class="{
                'base-select__dropdown_dropped': dropped,
                'base-select__dropdown_dropdown-above-select': isDropdownAboveSelect
             }"
             ref="dropdown"
        >
            <div v-for="option in options"
                class="base-select__item"
                v-html="option.text"
                :class="{selected: option.selected}"
                :key="option.value"
                @click="newSelect(option.value, option.text, event)"
            ></div>
        </div>
        <select class="base-select__select"
                v-bind:name="name"
                v-bind:id="id"
                v-bind:ref="id"
                v-model="value"
        >
            <option
                v-for="option in options"
                v-bind:value="option.value"
                :key="option.value"
            >
                {{option.text}}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "BaseSelect",
        props: {
            options: {
                type: Array,
                default: [{
                    value: 0,
                    text: "Нет элементов"
                }]
            },
            id: {
                type: String,
                default: "base-select"
            },
            name: {
                type: String,
                default: "base-select"
            },
            label: {
                type: String,
                default: "Выберите значение"
            },
            hint: {
                type: String,
                default: ""
            },
            placeholder: {
                type: String,
                default: "Выберите значение"
            },
            isError: {
                type: Boolean,
                default: false
            },
            additionalContainerClass: {
                type: String,
                default: ''
            }
        },
        emits: ['change'],
        data() {
            return {
                dropped: false,
                value: null,
                selectItemText: "",
                isDropdownAboveSelect: false,
            }
        },
        watch: {
            options() {
                this.checkSelected()
            }
        },
        beforeMount() {
            this.checkSelected()
        },
        mounted() {
            this.$refs.container.parentElement.addEventListener('scroll', this.checkDropdownPosition)
            this.checkDropdownPosition()
        },
        beforeUnmount() {
            this.$refs.container.parentElement.removeEventListener('scroll', this.checkDropdownPosition)
        },
        methods: {
            toggle() {
                this.closeOther(this.dropped)
                this.dropped = !this.dropped
                this.toggleEvent(this.dropped)
            },
            closeOther(isOpen) {
                let opened = document.querySelector(`.base-select__field_open`)
                if (!isOpen && opened) {
                    opened.click()
                }
            },
            closeList() {
                this.dropped = false
                this.toggleEvent()
            },
            toggleEvent(isRemoved = false) {
                let func = isRemoved ? 'addEventListener' : 'removeEventListener'
                document.querySelector(`#main`)[func]('click', this.closeList)
            },
            newSelect(key, text, event) {
                this.value = key
                this.selectItemText = text
                this.toggle()

                this.$emit('change', {
                    data: {
                        selectedValue: this.value,
                        id: this.id
                    }
                })
            },
            checkSelected() {
                this.options.some(e => {
                    if (e.selected === true) {
                        this.selectItemText = e.text
                        this.value = e.value
                        return true
                    }
                    return false
                })
            },
            checkDropdownPosition() {
                if (['visible', ''].includes(window.getComputedStyle(this.$refs.container.parentElement).overflow)
                    && ['visible', ''].includes(window.getComputedStyle(this.$refs.container.parentElement).overflowY))
                    return false

                let dropdown = this.$refs.dropdown.getBoundingClientRect(),
                    parent = this.$refs.container.parentElement.getBoundingClientRect()

                this.isDropdownAboveSelect = (this.isDropdownAboveSelect && (dropdown.top - parent.top >= 0))
                    || (parent.bottom - dropdown.bottom) < 0
            }
        }
    }
</script>

<style lang="scss" scoped>
    .base-select{
        padding-bottom: 13px;
        position: relative;

        &__label{
            position: absolute;
            padding: 0 4px;
            top: -9px;
            left: 12px;
            font-size: 12px;
            line-height: 17px;
            color: #505050;
            background-color: #fff;
            z-index: 1;
        }

        &__field{
            position: relative;
            border-radius: 12px;
            border: 1px solid rgba(200, 200, 200, 1);
            padding: 12.5px 36px 10.5px 12px;
            margin-bottom: 3px;
            cursor: pointer;
        }

        &__ico{
            position: absolute;
            right: 18px;
            top: 19px;
            transition: .3s;

            &_down{
                transform: rotateX(180deg);
            }
        }

        &__dropdown{
            background-color: #fff;
            box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.25);
            border-radius: 12px;
            border: 1px solid rgba(200, 200, 200, 1);
            position: absolute;
            top: calc(100% - 13px);
            width: 100%;
            left: 0;
            transition: .3s;
            z-index: -1;
            opacity: 0;
            pointer-events: none;

            &_dropped{
                pointer-events: all;
                z-index: 999;
                opacity: 1;
            }

            &_dropdown-above-select {
                transform: translate(0, calc(-100% - 63px));
            }
        }

        &__item{
            padding: 8px 16px;
            border-bottom: 1px solid rgba(244, 244, 244, 1);
            transition: .3s;
            cursor: pointer;

            &:hover{
                color: #ff2424;
            }

            &:first-child{
                border-radius: 12px 12px 0 0;
                padding: 16px 16px 8px 16px;
            }

            &:last-child{
                border-radius: 0 0 12px 12px;
                padding: 8px 16px 16px 16px;
                border-bottom: unset;
            }
        }

        &__hint{
            position: absolute;
            z-index: 999;
            bottom: 0;
            left: 0;
            padding-left: 12px;
            font-size: 12px;
            line-height: 17px;
            color: #AD0000;
        }

        &__select{
            display: none;
        }
    }
</style>