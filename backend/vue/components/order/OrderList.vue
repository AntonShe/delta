<template>
    <div class="item-list">
        <div class="p-grid p-nogutter grid grid-nogutter item-list__container" v-if="!isEmptyProducts">
            <order-items
                v-for="(item, index) in order.products"
                :key="item.labirintId"
                :cover="item.cover"
                :name="item.name"
                :title="item.title"
                :authors="item.authors"
                :labirintId="item.labirintId"
                :isbn="item.isbn"
                :price="item.price"
                :index="index"
                :quantity="item.quantity"
                :defaultValue="order.products[index].quantityCart"
                @remove="remove"
            />
        </div>
        <div class="item-list__add-item add-item mt-2 mb-2">
            <div class="flex flex-wrap align-items-center flex-grow-1 add-item__field">
                <label for="new-product" class="add-item_label mr-2">
                    Введите id с
                    <a class="font-medium no-underline cursor-pointer" href="/admin/products" target="_blank"
                       style="color: var(--primary-color);">этой страницы</a>
                    (НЕ id лабиринта)
                </label>
                <InputText
                    v-model="newProductId"                
                    id="new-product"
                    name="new-product"
                    placeholder="id книги"
                    type="text"
                    :readonly="!isEditable"
                />
            </div>
            <Button
                class="flex-shrink-0"
                icon="pi pi-plus"
                label="Добавить"
                severity="success"
                :disabled="!isEditable"
                @click.prevent="addProductItem"
            />
        </div>
    </div>
</template>

<script>
import {inject} from "vue";
import axios from "axios";
import OrderItems from "./OrderItems";

export default {
    name: "OrderList",
    components: {OrderItems},
    data() {
        return {
            newProductId: 0,
        }
    },
    setup() {
        const order = inject('order')
        const addProduct = inject('addProduct')
        const removeProduct = inject('removeProduct')
        const setProductQuantity = inject('setProductQuantity')
        const isEditable = inject('isEditable')

        return {
            order,
            addProduct,
            removeProduct,
            setProductQuantity,
            isEditable
        }
    },
    computed: {
        isEmptyProducts() {
            return _.isEmpty(this.order.products)
        }
    },
    methods: {
        showError() {
            this.$toast.add({severity: 'error', summary: 'Ошибка', detail: 'Не удалось добавить товар', life: 3000});
        },
        remove(index) {
            this.removeProduct(index)
        },
        addProductItem() {
            let productId = parseInt(this.newProductId)
            let duplicates = 0

            _.forEach(this.order.products, (item, index) => {
                if (item.id == productId) {
                    duplicates++
                    this.setProductQuantity(index, item.quantityCart + 1)
                }
            })

            if (duplicates === 0) {
                axios
                    .get('/admin/backend/product', {
                        params: {
                            id: productId,
                            isNew: 0,
                            active: 1
                        }
                    })
                    .then(response => {
                        let result = response.data.products

                        if (result.length > 0) {
                            const item = {...result[0]}
                            
                            item.quantityCart = 1
                            this.addProduct(item)
                        } else {
                            this.showError()
                        }
                    })
                    .catch((e) => {
                        console.error('Ошибка добавления товара', e)
                        this.showError()
                    })
            }
        },
        setQuantity(index, event) {
            this.setProductQuantity(index, event.target.value)
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

.add-item {
    display: flex;
    flex-wrap: nowrap;
    justify-content: flex-end;
    align-items: center;

    &__field {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        align-items: flex-start;
        width: 335px;
        margin-right: 20px;
    }

    &__hint {
        color: darkred;
        font-size: 12px;
    }
}
</style>