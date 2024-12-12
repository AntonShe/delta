<template>
    <div class="base-textarea">
		<textarea
                v-model="inputValue"
                class="i"
                :id="id"
                :name="name"
                :placeholder="placeholder"
                @change="changeInput"
                ref="input"
        >

		</textarea>
    </div>
</template>

<script>
import {IMask} from "vue-imask";

export default {
    name: "BaseTextarea",
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
        placeholder: {
            type: String,
        },
        isRequired: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            inputValue: this.value,
        }
	},
	methods: {
        changeInput(e) {
            if (this.value === e.target.value) {
                return
            }
            this.inputValue = e.target.value
            this.$emit('changeInputValue', this.inputValue, this.name)
        },
	}
}
</script>

<style lang="scss" scoped>
.base-textarea {
    height: 100%;

    textarea {
        padding: 8px 10px;
        width: 100%;
        height: 100%;
        @include _border-radius(8px);
        @include _typography-ext(fn, 16, 24, 400, ls, $Zinc-900);

        &::placeholder {
            color: $Zinc-500;
        }

        &:hover {
            outline: none;
            border-color: $Zinc-600;
        }

        &:focus {
            outline: none;
            border-color: $Indigo-400;
        }
    }
}
</style>