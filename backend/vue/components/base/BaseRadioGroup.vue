<template>
    <div
        :class="subClass"
        class="base-radio-group"
    >
        <label
            v-if="haveMainLabel"
            for="">{{ generalLabel }}
        </label>

        <div class="base-radio-group__list">
            <div
                v-for="item in items"
                :class="getAdditionalClass('item')"
                class="base-radio-group__item radio-item flex gap-2"
                :key="item.id"
            >
                <RadioButton
                    v-model="activeElement"
                    :inputId="item.id"
                    :name="name"
                    :value="item.value"
                    :disabled="!isEditable"
                    @change="changeValue(item.value)"
                />
                <label :for="item.id">{{ item.label }}</label>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "BaseRadioGroup",
    props: {
        list: {
            type: Object,
            require: true
        },
        inputName: {
            type: String,
            default: 'radioGroup'
        },
        mainLabel: {
            type: String,
            default: ''
        },
        additionalClass: {
            type: String,
            default: ''
        },
        defaultValue: {
            default: null
        },
        isEditable: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            activeElement: this.defaultValue,
            items: this.list,
            generalLabel: this.mainLabel,
            subClass: this.additionalClass,
            name: this.inputName,
        }
    },
    computed: {
        haveMainLabel() {
            return  !_.isEmpty(this.mainLabel)
        }
    },
    methods: {
        getAdditionalClass(suffix) {
            return _.isEmpty(this.subClass) ? '' : this.subClass + '__' + suffix
        },
        changeValue(newValue) {
            this.$emit('onChanged', newValue)
        }
    },
    emits: ['onChanged']
}
</script>