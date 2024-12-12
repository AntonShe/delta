<template>
	<div class="base-checkbox">
		<input type="checkbox"
			   @click="sendEmit"
               value="1"
               v-model="value"
			   :id="id"
		>
		<label :for="id" v-if="label">
			{{ label }}
		</label>
        <span v-if="!label"></span>
	</div>
</template>

<script>
export default {
    name: "BaseCheckbox",
	props: {
        value: {
            type: Boolean,
			required: true
		},
        name: {
            type: String,
            default: null
        },
        id: {
            type: String,
            default: null
        },
		label: {
            type: String,
			default: null
		}
	},
	methods: {
        sendEmit() {
            this.$emit('updateValue', !this.value, this.name);
		}
	}
}
</script>

<style lang="scss" scoped>
.base-checkbox {
    input {
        position: absolute;
        height: 20px;
        width: 20px;
        opacity: 0;
    }

    label {
        display: flex;

        &::before {
            content: '';
            margin-right: 8px;
        }
    }

    span,
    label::before {
        display: inline-block;
        vertical-align: sub;
        min-width: 20px;
        width: 20px;
        height: 20px;
        border: 1px solid $Zinc-400;
        @include _border-radius(6px);
        user-select: none;
        cursor: pointer;
    }

    input:checked + label::before,
    input:checked + span {
        background: get-icon('checkbox', $Red-400) no-repeat center;
        border: none;
    }
}
</style>