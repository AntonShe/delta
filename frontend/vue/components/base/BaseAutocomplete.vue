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
                <base-svg-store render-svg-type="close" @click="clear"/>
            </div>
            <div class="base-autocomplete__close" v-if="closeShow" @click="clear"></div>
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
import BaseSvgStore from "./BaseSvgStore";
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
    components: {BaseSvgStore},
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
        closeShow: {
            type: Boolean,
            default: false
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
    $root: base-autocomplete;
    position: relative;

    &.accepted {
        & .#{$root}__input {
            padding-left: 42px;
            background: get-icon('check', $Green-600) no-repeat left;
        }
    }

    &__field {
        position: relative;
    }

    &__label {
        @include placeholder($Zinc-500, $inter, 16px, 26px);
        position: absolute;
        top: -3px;
        left: 13px;

        & span{
            @include placeholder($Rose-600, $inter, 16px, 26px);
        }

        opacity: 0;
        transition: .1s;

        &_show {
            opacity: 1;
        }
    }

    &__error {
        position: absolute;
        margin: 2px 0 0 12px;
        font-size: 12px;
        line-height: 17px;
        opacity: 0;
        transition: .3s;
        z-index: 999;

        &_active {
            opacity: 1;
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

        &:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
        }

        @include placeholders($Zinc-500, $inter, 16px, 26px);

        &:hover {
            outline: none;
            border-color: $Zinc-600;
        }

        &:focus {
            outline: none;
            border-color: $Indigo-400;

            .base-autocomplete:hover{
                border-color: $Indigo-400;
            }
        }

        &.error {
            border: 1px solid  $Rose-600;
        }
    }

    &__list {
        padding: 4px;
        position: absolute;
        top: calc(100% + 2px);
        width: 100%;
        max-width: 100%;
        box-shadow: 0 15px 20px rgba(0, 0, 0, 0.05);
        @include _border-radius(8px);
        background-color: $white;
        z-index: 100;
        list-style: none;
    }

    &__item {
        width: 100%;
        padding: 5px 16px;
        font-size: 15px;
        line-height: 21px;
        color: $black;
        cursor: pointer;
        transition: .3s;
        @include _border-radius(8px);
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);

        &:hover,
        &_selected {
            background-color: $Red-50;
        }
    }

    &__clear {
        position: absolute;
        height: 0;
        width: 0;
        opacity: 0;
        z-index: -1;
    }

    &__close {
        position: absolute;
        top: 9px;
        right: 10px;
        width: 25px;
        height: 25px;
        background: get-icon('close', $Zinc-400);
        background-size: contain;
        cursor: pointer;
    }
}
</style>