<template>
    <div class="base-autocomplete">
        <div class="base-autocomplete__field">
            <label class="base-autocomplete__label"
                   :class="{
                       'base-autocomplete__label_active': isActive,
                       'base-autocomplete__label_show': label,
                       'base-autocomplete__label_error': errorText
                   }"
                   :for="id">
                {{label}}
            </label>
            <input
                    ref="input"
                    class="base-autocomplete__input"
                    :name="name"
                    :id="id"
                    type="text"
                    :placeholder="placeholder"
                    autocomplete="off"
                    v-model="search"
                    :readonly="!isEditable"
                    @keydown.down="onArrowDown"
                    @keydown.up="onArrowUp"
                    @keydown.enter="onEnter"
                    @input="onChange"
                    @focus="this.isActive = true"
            />
            <div class="base-autocomplete__error"
                 :class="{'base-autocomplete__error_active': errorText}"
            >
                {{errorText}}
            </div>
            <div class="base-autocomplete__clear" v-if="!empty">
<!--                <base-svg-store render-svg-type="close" @click="clear"/>-->
            </div>
        </div>
        <ul v-show="isOpen" class="base-autocomplete__list">
            <li
                    v-for="(result, key) in results" :key="key"
                    class="base-autocomplete__item"
                    @click="setResult(result)"
                    :class="{'base-autocomplete__item_selected': isSelected(key)}"
            >
                {{ result }}
            </li>
        </ul>
    </div>
</template>

<script>
    const keyRel = {
        "q": "й",
        "w": "ц",
        "e": "у",
        "r": "к",
        "t": "е",
        "y": "н",
        "u": "г",
        "i": "ш",
        "o": "щ",
        "p": "з",
        "[": "х",
        "{": "х",
        "]": "ъ",
        "a": "ф",
        "s": "ы",
        "d": "в",
        "f": "а",
        "g": "п",
        "h": "р",
        "j": "о",
        "k": "л",
        "l": "д",
        ";": "ж",
        ":": "ж",
        "'": "э",
        '"': "э",
        "z": "я",
        "x": "ч",
        "c": "с",
        "v": "м",
        "b": "и",
        "n": "т",
        "m": "ь",
        ",": "б",
        "<": "б",
        ".": "ю",
        ">": "ю",
        "`": "ё"
    }

    export default {
        name: "BaseAutocomplete",
        props: {
            name: {
                type: String,
                required: true
            },
            id: {
                type: String,
                required: true
            },
            items: {
                type: Array,
                required: true
            },
            minLength: {
                type: Number,
                default: 0,
            },
            placeholder: {
                type: String,
                default: '',
            },
            defaultValue: {
                type: String,
                default: '',
            },
            errorText: {
                type: String,
                default: ''
            },
            label: {
                type: String,
                default: ""
            },
            isEditable: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                isOpen: false,
                isActive: false,
                search: this.defaultValue,
                arrowCounter: -1,
                empty: this.defaultValue < 1
            };
        },
        watch: {
            defaultValue(){
                this.search = this.defaultValue
                this.empty = this.defaultValue.length < 1
            },
            search(){
                this.empty = this.search.length < 1
            }
        },
        emits: ['changeValue', 'setValue', 'cleared'],
        computed: {
            results() {
                let array = [],
                    search = this.search.toLowerCase()

                if (search.length > 0) {
                    for (let i = 0; i < search.length; i++) {
                        if (keyRel[search[i]] !== undefined) {
                            search = search.replace(search[i], keyRel[search[i]])
                        }
                    }
                    array = this.items
                        .filter((item) => {
                            return item.toLowerCase().indexOf(search) > -1
                        })
                        .sort((a, b) => {
                            if (a.toLowerCase().indexOf(search) < b.toLowerCase().indexOf(search)) {
                                return -1
                            }
                            if (a.toLowerCase().indexOf(search) > b.toLowerCase().indexOf(search)) {
                                return 1
                            }
                            return 0
                        }).slice(0, 5)
                }

                return array
            }
        },
        methods: {
            onChange() {
                this.isOpen = this.search.length > this.minLength
                this.$emit('changeValue', this.search)
            },
            clear(){
                this.search = ''
                this.isOpen = false
                this.isActive = false
                this.arrowCounter = -1
                this.$refs.input.focus()
                this.$emit('cleared')
            },
            setResult(result) {
                this.search = result
                this.isOpen = false
                this.isActive = false
                this.arrowCounter = -1
                this.$emit('setValue', this.search)
            },
            onArrowDown() {
                if (this.isOpen) {
                    if (this.arrowCounter < this.results.length - 1) {
                        this.arrowCounter = this.arrowCounter + 1
                    }
                }
            },
            onArrowUp() {
                if (this.isOpen) {
                    if (this.arrowCounter > 0) {
                        this.arrowCounter = this.arrowCounter - 1
                    }
                }
            },
            onEnter() {
                if (this.results.length && this.isOpen && this.arrowCounter > -1) {
                    this.setResult(this.results[this.arrowCounter])
                } else if (this.search === this.results[0]) {
                    this.setResult(this.results[0])
                } else {
                    this.arrowCounter = 0
                }
            },
            handleClickOutside(evt) {
                if (!this.$el.contains(evt.target)) {
                    this.isActive = false
                    this.isOpen = false
                }
            },
            isSelected(key) {
                return key === this.arrowCounter
            }
        },
        mounted() {
            if (this.value) {
                this.search = this.value
            }
            document.addEventListener('click', this.handleClickOutside)
        },
        destroyed() {
            document.removeEventListener('click', this.handleClickOutside)
        }
    }
</script>

<style lang="scss" scoped>
    .base-autocomplete {
        position: relative;

        &__field {
            position: relative;
        }

        &__label {
            position: absolute;
            top: -8px;
            left: 8px;
            padding: 0px 4px;
            background-color: #fff;
            font-size: 12px;
            line-height: 17px;
            color: #505050;
            opacity: 0;
            transition: .1s;

            &_show {
                opacity: 1;
            }

            &_active {
                //color: $purple;
            }

            &_error {
                //color: $error-color;
            }
        }

        &__error {
            position: absolute;
            margin: 2px 0px 0px 12px;
            font-size: 12px;
            line-height: 17px;
            //color: $error-color;
            opacity: 0;
            transition: .3s;
            z-index: 999;

            &_active {
                opacity: 1;
            }
        }

        &__input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 12px;
            border: 1px solid #C8C8C8;
            font-size: 15px;
            line-height: 21px;
            color: $black;
            box-shadow: none;
            outline: none;
            transition: .1s;

            &:focus {
                //border: 1px solid $purple;
            }

            &_error {
                //border: 1px solid $error-color;
            }
        }

        &__list {
            position: absolute;
            top: calc(100% + 2px);
            width: 100%;
            max-width: 100%;
            box-shadow: 0px 0px 4px 0px #00000040;
            border-radius: 16px;
            z-index: 1000;
        }

        &__item {
            width: 100%;
            padding: 8px 16px;
            background-color: #fff;
            font-size: 15px;
            line-height: 21px;
            color: $black;
            cursor: pointer;
            transition: .3s;

            &:hover,
            &_selected {
                //background-color: $purple;
                color: #fff;
            }

            &:first-child {
                border-radius: 16px 16px 0px 0px;
            }

            &:last-child {
                border-radius: 0px 0px 16px 16px;
            }

            &:only-child {
                border-radius:16px 16px 16px 16px;
            }
        }

        &__clear {
            position: absolute;
            cursor: pointer;
            top: calc(50% - 12px);
            right: 15px;
            transition: .3s;
        }
    }
</style>