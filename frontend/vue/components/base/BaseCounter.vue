<template>
	<div class="base-counter">
        <button
            class="base-counter__btn base-counter__btn_decrement"
            :class="{'disable': isDecrementDisabled}"
            @click="decrement"
        />
		<div class="field">
            <BaseField 
                :id="id"
                :name="name"
                :value="value"
                :mask="mask"
                :class="{'error_text': isError}"
                :disabled="true"
                @updateInputValue="setCounter"
            />
		</div>
        <button 
            class="base-counter__btn base-counter__btn_increment"
            :class="{'disable': isIncrementDisabled}"
            @click="increment"            
        />
	</div>
</template>

<script>
import BaseField from "./BaseField.vue";
import BaseTooltip from "./BaseTooltip.vue";

export default {
    name: "BaseCounter",
    components: {BaseTooltip, BaseField},
	props: {
        counter: Number,
		name: String,
		id: String,
        min: {
            type: Number,
            default: 0
        },
		max: {
            type: Number,
			default: 1000
		},
		isError: {
            type: Boolean,
			default: false
		}
	},
    emits: ['changeCounter', 'incrementClick', 'decrementClick'],
	data() {
        return {
            value: String(this.counter),
            mask: {
                mask: Number,
				min: this.min,
				max: this.max,
            },
			isDecrementDisabled: false,
			isIncrementDisabled: this.max === this.counter,
        }
	},
    methods: {
        decrement() {
            if (this.min <= (Number(this.value) - 1)) {
                this.value--;

                this.isIncrementDisabled = false;
                this.$emit('decrementClick', this.value);
			}
		},
		setCounter(value) {
            if (Number(this.value) === this.max) {
                this.isIncrementDisabled = true;
			}

            if (Number(this.value) === this.min) {
                this.isDecrementDisabled = true;
            }

            this.value = value;

            this.$emit('changeCounter', value);
		},
        increment() {
            if (this.max >= (Number(this.value) + 1)) {
                this.value++;

                this.isDecrementDisabled = false;
                this.$emit('incrementClick', this.value);
			}
        }
	}
}
</script>

<style lang="scss">
.base-counter {
    display: flex;
    align-items: center;
    justify-content: space-between;

    .field {
        margin: 0 6px;
        position: relative;

        .input-default__input {
            max-width: 85px;
            height: 44px;
            text-align: center;
            padding: 0;
            border: 1px solid $Zinc-200;
            @include _border-radius(8px);
            @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-400);

            @media (max-width: $screen-xl) {
                height: 36px;
            }
        }

        .svg-store {
            display: none;
        }

        .error_text .input-default__input {
            color: red;
        }
    }
}

.base-counter__btn {
    width: 44px;
    height: 44px;
    background-repeat: no-repeat;
    background-position: center;
    border-radius: 8px;
    @include _button-reset(10px, $Zinc-50, 1px solid $Zinc-200);
    cursor: pointer;

    @media (max-width: $screen-xl) {
        width: 36px;
        height: 36px;
    }

    &.disable {
        display: block;
        cursor: default;
    }        
}

// themes
.base-counter__btn_decrement {
    background-image: get-icon('minus', $Zinc-900);

    &.disable {
        background-image: get-icon('minus', $Zinc-400);
    }
}

.base-counter__btn_increment {
    background-image: get-icon('plus', $Zinc-900);

    &.disable {
        background-image: get-icon('plus', $Zinc-400);
    }
}
</style>