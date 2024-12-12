<template>
    <div class="col-12 order-form__product-item">
        isEditable: {{ isEditable }}
        <div class="flex flex-column md:flex-row align-items-center pb-3 pr-3 pt-3 w-full">
            <div class="mr-2">{{index + 1}}</div>
            <img v-if="cover" :src="cover" :alt="name" class="my-4 md:my-0 w-4 md:w-5rem shadow-2 mr-2"/>
            <div class="flex-1 text-center md:text-left mr-3">
                <div v-if="title" class="font-bold text-1xl">{{ title }}</div>
                <div v-if="authors" class="mb-1">{{ authors }}</div>
                <div class="mb-1">Артикул Лабиринта: {{ labirintId ? labirintId : 'Отсутствует' }}</div>
                <div class="mb-1">Количество на складе: {{ quantity }}</div>
                <div v-if="isbn" class="">ISBN: {{ isbn }}</div>
            </div>

            <div class="mr-3">
                <InputNumber
                    v-if="quantity !== 0"
                    v-model="quantityValue"
                    showButtons
                    buttonLayout="vertical"
                    style="width: 9rem; display: flex; flex-direction: row-reverse;"
                    decrementButtonClassName="p-button-secondary"
                    incrementButtonClassName="p-button-secondary"
                    incrementButtonIcon="pi pi-plus"
                    decrementButtonIcon="pi pi-minus"
                    :min="0"
                    :max="order.products[index].quantity"
                    :disabled="!isEditable"
                    @input="(event) => setQuantity(index, event)"
                />
                <div v-else><p style="width: 9rem;">Нет в наличии</p></div>
            </div>
            <div
                class="flex flex-row md:flex-column justify-content-between w-full md:w-auto align-items-center md:align-items-end mt-5 md:mt-0">
                <span v-if="price" class="text-2xl font-semibold align-self-center md:align-self-end">{{
                        price
                    }} ₽</span>
                <Button
                    :disabled="!isEditable"
                    @click.prevent="remove(index)"
                    icon="pi pi-trash"
                    severity="danger"
                    aria-label="Удалить"
                />
            </div>
        </div>
    </div>
</template>

<script>
import {inject} from "vue";

export default {
    name: "OrderItems",
    emits: ['remove'],
    props: {
        cover: {
            type: String,
            default: '',
        },
        name: {
            type: String,
            default: '',
        },
        title: {
            type: String,
            default: '',
        },
        authors: {
            type: String,
            default: '',
        },
        isbn: {
            type: String,
            default: '',
        },
        labirintId: {
            type: Number,
            default: 0,
        },
        price: {
            type: Number,
            default: 0,
        },
        index: {
            type: Number,
            default: 0,
        },
        quantity: {
            type: Number,
            default: 0,
        },
        defaultValue: {
            type: Number,
            default: 0,
        }
    },
    data() {
        return {
            quantityValue: this.defaultValue
        }
    },
    setup() {
        const setProductQuantity = inject('setProductQuantity')
        const order = inject('order')
        const isEditable = inject('isEditable')

        return {
            setProductQuantity,
            order,
            isEditable
        }
    },
    methods: {
        remove(index) {
            if (!this.isEditable) return

            this.$emit('remove', index)
        },
        setQuantity(index, event) {
            this.quantityValue = event.value
            this.setProductQuantity(index, this.quantityValue)
        },
    }
}
</script>

<style lang="scss">
.item-list {
    max-height: 375px;
    overflow-y: auto;

    &__container > .col-12 {
        border: solid #dee2e6;
        border-width: 0 0 1px 0;
    }
}

.cover {
    height: 100px;
    width: 100%;
    max-width: 100%;

    &__img {
        height: 100%;
        max-height: 100%;
        width: auto;
    }
}

.p-inputnumber-buttons-vertical .p-button.p-inputnumber-button-up {
    order: 1;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    width: 33%;
}

.p-inputnumber-buttons-vertical .p-button.p-inputnumber-button-down {
    order: 3;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    width: 33%;
}
.p-inputnumber-buttons-vertical .p-inputnumber-input {
    order: 2;
    border-radius: 0;
    text-align: center;
    width: 34%;
}
</style>