<template>
    <Dialog
        v-model:visible="visible"
        :breakpoints="{ '960px': '75vw' }"
        :style="{ width: '60vw' }"
        :modal="true"
        :dismissableMask="true"
    >
        <template #header>
            <h5
                v-if="isNew"
                class="m-0">
                Создать заказ
            </h5>
            <h5
                v-else
                class="m-0">
                Редактировать заказ
            </h5>
        </template>
        <div v-if="!isEmpty && !loading" class="order-form">
            <h5>Номер заказа: {{ orderId }}</h5>
            <div class="mb-2">
                <span class="text-900 font-bold mr-1">Статус заказа:</span>
                <span class="text-900 font-medium">{{ isNew ? getStatus(0) : getStatus(order.status.status) }}</span>
            </div>
            <div class="order-form__base">
                <order-list ref="orderItems" class="order-form__items item-list"/>

                <user-selector/>

                <order-delivery class="order-form__delivery"/>

                <div class="order-form__payment payment">
                    <div class="h6 flex-shrink-0">Способ оплаты</div>
                    <div class="payment__person" v-if="isLegal">
                        <base-radio-group
                            :list="paymentLegal"
                            :defaultValue="order.paymentType"
                            additional-class="payment__radio"
                            input-name="paymentType"
                            :isEditable="isEditable"
                            @onChanged="updatePaymentType"
                        />
                        <order-files/>
                    </div>

                    <div class="payment__legal" v-else>
                        <base-radio-group
                            :list="paymentPerson"
                            :defaultValue="order.paymentType"
                            additional-class="payment__radio"
                            input-name="paymentType"
                            :isEditable="isEditable"
                            @onChanged="updatePaymentType"
                        />
                    </div>
                </div>

                <div class="order-form__wrapper">
                    <div class="flex justify-content-between w-100 order-form__finance finance">
                        <div class="h5">Финансы:</div>

                        <div class="finance__info">
                            <div class="flex justify-content-between mb-1">
                                <span class="mr-3 text-900">{{ financeCount.totalQuantity }} товаров на</span>
                                <span class="font-bold">{{ financeCount.totalPrice }} ₽</span>
                            </div>
                            <div class="flex justify-content-between mb-1">
                                <span class="mr-3 text-900">Скидка</span>
                                <span class="font-bold">-{{ financeCount.totalDiscount }} ₽</span>
                            </div>
                            <div class="flex justify-content-between mb-1">
                                <span class="mr-3 text-900">Доставка</span>
                                <span class="font-bold">{{ financeCount.totalDelivery }} ₽</span>
                            </div>
                            <div class="flex justify-content-between mb-1">
                                <span class="mr-3 text-900">Итого</span>
                                <span class="font-bold">{{ financeCount.totalOrderPrice }} ₽</span>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="!isNew"
                        class="order-form__status status-info">
                    </div>
                </div>
                <!-- <div v-if="!isEditable" class="order-form__disabled"></div> -->
            </div>

            <div class="">
                <h5>Комментарий менеджера</h5>
                <Textarea
                    class="w-100"
                    placeholder="Комментарий"
                    v-model="order.managerComment"
                    :autoResize="true"
                    rows="3"
                />
            </div>
        </div>
        <div v-else class="flex align-items-center justify-content-center" style="min-height: calc(100vh - 273px);">
            <i class="pi pi-spin pi-spinner" style="font-size: 5rem"></i>
        </div>
        <template #footer>
            <div class="flex flex-wrap gap-2 justify-content-center pt-3">
                <Button
                    @click.prevent="closePopup"
                    label="Отмена"
                    severity="danger"
                    raised
                />
                <Button
                    @click.prevent="saveOrder"
                    label="Сохранить"
                    :disabled="!isOrderValid || !isEditable"
                    raised
                />
                <Button
                    @click.prevent="rejectOrder"
                    label="Аннулировать"
                    :disabled="!canReject || !isEditable"
                    severity="danger"
                    raised/>
                <Button
                    @click.prevent="sendOrder"
                    label="Отправить вручную"
                    :disabled="!canManualySend || !isEditable"
                    raised
                />
            </div>
        </template>
    </Dialog>
</template>

<script>
import {ref, provide, readonly, computed} from "vue"
import axios from "axios"
import OrderList from "./OrderList";
import UserSelector from "./UserSelector"
import BaseRadioGroup from "../base/BaseRadioGroup"
import OrderDelivery from "./OrderDelivery"
import OrderFiles from "./OrderFiles"

export default {
    name: "OrderForm",
    components: {
        UserSelector,
        BaseRadioGroup,
        OrderDelivery,
        OrderFiles,
        OrderList
    },
    props: {
        orderId: {
            type: Number,
            default: 0,
        },
        isVisible: {
            type: Boolean,
            default: false,
        }
    },
    emits: ['closePopup', 'orderCreate', 'orderUpdated', 'orderRejected'],
    data() {
        return {
            visible: this.isVisible,
            paymentPerson: [
                {
                    label: 'Оплата картой',
                    value: 1,
                    id: 'ps'
                },
                {
                    label: 'Оплата при получении',
                    value: 2,
                    id: 'pg'
                }
            ],
            paymentLegal: [
                {
                    label: 'Оплата на счёт',
                    value: 3,
                    id: 'pc'
                }
            ],
            isOrderValid: false,
            isShowUser: false,
            loading: true,
        }
    },
    setup() {
        const order = ref({})
        const emptyOrder = {
            id: 0,
            products: [],
            user: {},
            subUser: {
                subPhone: '',
                subLastName: '',
                subFirstName: '',
            },
            manageComment: '',
            paymentType: 0,
            deliveryDate: '',
            delivery: {
                city: 'Москва',
                address: '',
                flat: '',
                entry: '',
                flor: '',
                entryCode: '',
                courierComment: '',
                latitude: '',
                longitude: '',
                price: 0,
                pointId: null,
                type: 0
            }
        }

        const getOrder = (idOrder) => {
            if (idOrder > 0) {
                axios
                    .get('/admin/backend/order/full', {
                        params: {
                            id: idOrder
                        }
                    }).then(result => {
                    result.data.id = idOrder
                    order.value = {...result.data}
                })
            } else {
                order.value = JSON.parse(JSON.stringify(emptyOrder))
            }
        }
        //region products
        const addProduct = (product) => {
            order.value.products.push(product)
        }

        const removeProduct = (productKey) => {
            if (!_.isEmpty(order.value.products[productKey])) {
                order.value.products.splice(productKey, 1)
            }
        }

        const setProductQuantity = (productKey, newQuantity) => {
            if (order.value.products[productKey].quantity >= newQuantity) {
                order.value.products[productKey].quantityCart = newQuantity
            } else {
                order.value.products[productKey].quantityCart = order.value.products[productKey].quantity
            }
        }
        //endregion
        //region user
        const setUser = (user) => {
            order.value.user = user
        }

        const setSubUserField = (field, value) => {
            order.value.subUser[field] = value
        }

        //endregion
        //region delivery
        const setType = (type) => {
            order.value.delivery.type = type
        }

        const setCity = (city) => {
            if (order.value.delivery.city != city) {
                order.value.delivery.city = city
                order.value.delivery.address = ''
            }

        }
        const setAddress = (field, value) => {
            order.value.delivery[field] = value

            if (field == 'address' && order.value.delivery.type == 1) {
                order.value.delivery.pointId = null
                axios
                    .post('/admin/backend/courier/calculate', {
                        address: order.value.delivery.address,
                        latitude: order.value.delivery.latitude,
                        longitude: order.value.delivery.longitude,
                    })
                    .then(result => {
                        if (!_.isEmpty(result.data)) {
                            let rawDate = new Date(result.data.JSON_TXT[0].DateClose)
                            let prettyDate = [
                                rawDate.getFullYear(),
                                rawDate.getMonth() + 1,
                                rawDate.getDate()
                            ]

                            if (parseInt(prettyDate[1]) < 10) prettyDate[1] = '0' + prettyDate[1]
                            if (parseInt(prettyDate[2]) < 10) prettyDate[2] = '0' + prettyDate[2]


                            order.value.delivery.price = parseInt(result.data.JSON_TXT[0].SumCost)
                            order.value.deliveryDate = prettyDate.join('-')
                            this.financeCount
                        }
                    })
            }
        }

        const setPoint = (pointData) => {
            order.value.delivery.address = pointData.address
            order.value.delivery.pointId = pointData.pointId
            order.value.delivery.latitude = pointData.latitude
            order.value.delivery.longitude = pointData.longitude

            axios
                .post('/admin/backend/pvz/calculate', {
                    idPoint: pointData.pointId
                })
                .then(result => {
                    if (!_.isEmpty(result.data)) {
                        let rawDate = new Date(result.data.JSON_TXT[0].DateClose)
                        let prettyDate = [
                            rawDate.getFullYear(),
                            rawDate.getMonth() + 1,
                            rawDate.getDate()
                        ]

                        if (parseInt(prettyDate[1]) < 10) prettyDate[1] = '0' + prettyDate[1]
                        if (parseInt(prettyDate[2]) < 10) prettyDate[2] = '0' + prettyDate[2]


                        order.value.delivery.price = parseInt(result.data.JSON_TXT[0].SumCost)
                        order.value.deliveryDate = prettyDate.join('-')
                        this.financeCount
                    }
                })
        }
        //endregion

        const isEditable = computed(() => {
            if (!order.value || !order.value.status) return true

            return order.value.status.status < 2 || order.value.status.status === 100
        })

        provide('order', readonly(order))

        provide('addProduct', addProduct)
        provide('removeProduct', removeProduct)
        provide('setProductQuantity', setProductQuantity)

        provide('setUser', setUser)
        provide('setSubUserField', setSubUserField)

        provide('setType', setType)
        provide('setCity', setCity)
        provide('setAddress', setAddress)
        provide('setPoint', setPoint)

        provide('isEditable', isEditable)

        return {
            order,
            getOrder,
            isEditable
        }
    },
    async beforeMount() {
        await this.getOrder(this.orderId)
        this.loading = false;
    },

    watch: {
        visible() {
            this.closePopup()
        },
        order: {
            deep: true,
            handler() {
                let productValidation = false
                let userValidation = !_.isEmpty(this.order.user)
                let deliveryValidation = false
                let paymentValidation = false

                if (userValidation) {
                    if (this.isLegal && this.order.paymentType == 3) {
                        paymentValidation = true
                    } else if (!this.isLegal && (
                        this.order.paymentType == 1
                        || this.order.paymentType == 2
                    )) {
                        paymentValidation = true
                    }
                }

                if (!_.isEmpty(this.order.products) && this.order.products.length > 0) {
                    productValidation = true
                    _.forEach(this.order.products, (item) => {
                        if (item.active != 1 || item.quantity < 1 || item.quantity < item.quantityCart) {
                            productValidation = false
                        }
                    })
                }

                if (!_.isEmpty(this.order.delivery)) {
                    deliveryValidation = true

                    if (this.order.delivery.type == 1) {
                        if (
                            _.isEmpty(this.order.delivery.address)
                            || _.isEmpty(this.order.delivery.city)
                            || this.order.delivery.price < 1
                        ) {
                            deliveryValidation = false
                        }
                    } else if (this.order.delivery.type == 2) {
                        if (this.order.delivery.price < 1
                            || (this.order.delivery.pointId < 1 && this.order.delivery.pointId !== 0)
                        ) {
                            deliveryValidation = false
                        }
                    }
                }

                this.isOrderValid = (
                    productValidation
                    && deliveryValidation
                    && userValidation
                    && paymentValidation
                )
            }
        },
    },
    computed: {
        isNew() {
            return this.orderId === 0
        },
        isLegal() {
            if (!_.isEmpty(this.order.user)) {
                return this.order.user.profile[0].isLegal ? true : false
            }

            return false
        },
        isEmpty() {
            return _.isEmpty(this.order)
        },
        financeCount() {
            let finances = {
                totalQuantity: 0,
                totalPrice: 0,
                totalDiscount: 0,
                totalDelivery: 0,
                totalOrderPrice: 0
            }

            finances.totalDelivery = _.isEmpty(this.order.delivery) ? 0 : this.order.delivery.price

            if (!_.isEmpty(this.order.products)) {
                _.each(this.order.products, (product) => {
                    finances.totalQuantity += parseInt(product.quantityCart)
                    finances.totalPrice += product.quantityCart * (product.priceInOrder ? product.priceInOrder : product.price)
                    finances.totalDiscount += product.quantityCart * (product.oldPrice - product.price)
                })

                finances.totalOrderPrice = finances.totalDelivery + finances.totalPrice

            }

            return finances
        },
        canManualySend() {
            return !_.isEmpty(this.order)
                && !_.isEmpty(this.order.status)
                && ((this.order.status.status === 1 && this.order.status.orderPrice > 70000)
                    || this.order.status.status === 0
                    || this.order.status.status === 100)
        },
        canReject() {
            return !_.isEmpty(this.order)
                && !_.isEmpty(this.order.status)
                && this.order.status.status !== 7
                && this.order.status.status !== 8
        }
    },
    methods: {
        getStatus(number) {
            let statuses = {
                0: 'Новый, ожидает оплаты или ручного перевода',
                1: 'Создан (если оплата при получении) - ждет отправки в бд Logist',
                2: 'Оплачен - подтвержден',
                31: 'Создан - отправлен в бд Logist',
                32: 'Сборка',
                33: 'Отгружен, в пути',
                34: 'Принят в курьерской службе',
                36: 'Доступен к выдаче ПВЗ',
                7: 'Выполнен',
                8: 'Отменен',
                9: 'Срок оплаты истек',
                100: 'При отправке в Логист произошла ошибка',
            }

            return statuses[number]
        },
        updatePaymentType(value) {
            if (!this.isEditable) return
            
            this.order.paymentType = value
        },
        closePopup() {
            this.$emit('closePopup')
        },
        saveOrder() {
            if (this.order.delivery.type === 1) {
                this.order.delivery.address = this.order.delivery.address.replace(" обл, ", " область, ")
            }

            if (this.orderId == 0) {
                axios
                    .post('/admin/backend/order', {...this.order})
                    .then(result => {
                        this.$emit('orderCreate', result.data.status)
                    })
                    .catch(error => {
                        throw new Error('Ошибка, ' + error);
                    })
            } else {
                let dataOrder = {...this.order}
                dataOrder.id = this.orderId

                axios
                    .patch('/admin/backend/order', {...dataOrder})
                    .then(result => {
                        this.$emit('orderCreate', result.data.status)
                    })
                    .catch(error => {
                        throw new Error('Ошибка, ' + error);
                    })
            }

        },
        rejectOrder() {
            axios
                .delete(`/admin/backend/order/${this.order.id}`)
                .then(result => {
                    this.$emit('orderUpdated', result.data.status)
                })
                .catch(error => {
                    throw new Error('Ошибка, ' + error);
                })
        },
        sendOrder() {
            axios
                .patch(`/admin/backend/order/send/${this.order.id}`)
                .then(result => {
                    this.$emit('orderRejected', result.data.status)
                })
                .catch(error => {
                    throw new Error('Ошибка, ' + error);
                })
        },
    }
}
</script>

<style lang="scss">
.order-form {

    &__items {
        margin-bottom: 50px;
    }

    &__user {
        margin-bottom: 50px;
    }

    &__delivery {
        margin-top: 50px;
    }

    &__payment {
        margin-top: 50px;
        margin-bottom: 50px;
        display: flex;
        flex-wrap: nowrap;
        justify-content: flex-start;
        align-items: flex-start;
    }

    &__finance {
        margin-bottom: 50px;
        display: flex;
        flex-wrap: nowrap;
        justify-content: flex-start;
        align-items: flex-start;
    }

    &__comment {
        margin-bottom: 50px;
    }
}

.order-form__base {
    position: relative;
}

.order-form__disabled {
    position: absolute;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba($white, 0.6);
    z-index: 100;
}

.finance {
    &__info {
        margin-left: 20px;
    }
}

.p-inputtextarea-resizable {
    overflow: hidden;
    resize: none;
}

.payment {
    width: 100%;

    &__radio {
        margin-left: 20px;
    }

    &__legal {
        width: 100%;
        display: flex;
        flex-wrap: nowrap;
        align-items: flex-start;
        justify-content: space-between;
    }
}

.buttons {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-evenly;
}

.files {
    margin-right: 20px;
}
</style>