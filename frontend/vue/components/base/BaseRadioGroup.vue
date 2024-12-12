<template>
<div
    :class="subClass"
    class="base-radio-group"
>
    <label
        v-if="haveMainLabel"
        for="">{{generalLabel}}</label>

    <div
        v-for="item in items"
        :class="getAdditionalClass('item')"
        class="base-radio-group__item radio-item"
        :key="item.name"
    >
        <input
            :name="name"
            :value="item.value"
            :id="item.id"
            :class="getAdditionalClass('input')"
            :checked="this.defVal == item.value"
            @change="changeValue(item.value)"
            type="radio" class="radio-item__input">
        <label
            :for="item.id"
            :class="getAdditionalClass('label')"
            class="radio-item__label">{{item.label}}</label>
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
        }
    },
    data() {
        return {
            items: this.list,
            generalLabel: this.mainLabel,
            subClass: this.additionalClass,
            name: this.inputName,
            defVal: this.defaultValue
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
            this.$emit('onChanged', newValue, this.name)
        }
    },
    emits: ['onChanged']
}
</script>

<style lang="scss">
.base-radio-group {
    display: flex;
    align-items: center;
    gap: 36px;
    @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-500);
}

.radio-item {
    position: relative;
    display: flex;
    align-items: center;

    &__input {
        width: 20px;
        height: 20px;
        margin-right: 8px;
        opacity: 0;

        &:checked + .radio-item__label::before {
            border: none;
            background: get-icon('radio-btn', $Red-400);
        }
    }

    &__label {
        color: $Zinc-900;
        cursor: pointer;    

        &::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 0;
            display: block;
            width: 20px;
            height: 20px;
            border-radius: 50px;
            border: 1px solid $Red-400;
        }
    }
}
</style>