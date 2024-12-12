<template>
    <DataTable
        lazy
        paginator
        v-model:value="orders"
        :paginator="true"
        class="p-datatable-gridlines"
        :rows="40"
        :totalRecords="pagination.pageCount * 40"
        dataKey="id"
        :rowHover="true"
        filterDisplay="menu"
        :loading="loading"
        responsiveLayout="scroll"
        @page="changePage($event)"
        style="min-height: calc(100vh - 276px);"
    >
        <template #header>
            <div class="flex justify-content-between flex-column sm:flex-row">
                            <span class="p-input-icon-left mr-2">
                                <i class="pi pi-search"/>
                                <InputText
                                    v-model="search"
                                    placeholder="Поиск по номеру заказа"
                                />
                            </span>
                <Button
                    type="button"
                    label="Поиск"
                    class="p-button-outlined"
                    @click.prevent="getOrders"
                />
                <Button
                    class="ml-auto"
                    icon="pi pi-plus"
                    label="Добавить"
                    severity="success"
                    @click.prevent="showForm"
                />
            </div>
        </template>
        <template v-if="isEmpty" #empty>Заказы не найдены</template>
        <template #loading>
            <i class="pi pi-spin pi-spinner" style="font-size: 5rem"></i>
        </template>
        <Column style="min-width: 4rem">
            <template #body="{ data }">
                <Button severity="secondary" text rounded aria-label="Найстройки"
                        @click.prevent="setIdOrder(data.id)" style="padding: 7px;">
                    <i class="pi pi-file-edit" style="font-size: 1.5rem;"/>
                </Button>
            </template>
        </Column>
        <Column field="id" header="ID" style="min-width: 5rem">
        </Column>
        <Column field="order_number" header="№ заказа" style="min-width: 8rem">
        </Column>
        <Column field="date_create" header="Дата" style="min-width: 12rem">
        </Column>
        <Column field="getter_name" header="Покупатель" style="min-width: 14rem">
        </Column>
        <Column header="Тип плательщика" style="min-width: 9rem">
            <template #body="{ data }">
                {{ data.payment_type == 3 ? 'Юр. лицо' : 'Физ. лицо' }}
            </template>
        </Column>
        <Column field="getter_phone" header="Телефон" style="min-width: 11rem">
        </Column>
        <Column header="Статус" style="min-width: 7rem">
            <template #body="{ data }">
                {{ getStatusText(data.status) }}
            </template>
        </Column>
        <Column field="date_create" header="Оплачен" style="min-width: 6rem">
            <template #body="{ data }">
                <div class="flex justify-content-center align-items-center">
                    <i class="pi" :class="(data.status === 7 || data.status === 2) ? 'pi-check-circle' : 'pi-times-circle'"
                       style="font-size: 1.8rem;"
                       :style="(data.status === 7 || data.status === 2) ? 'color: green;' : 'color: red;'"/>
                </div>
            </template>
        </Column>
        <Column field="order_price" header="Сумма" style="min-width: 5rem">
        </Column>
    </DataTable>

    <order-form
        v-if="isVisible"
        :isVisible="isVisible"
        ref="orderForm"
        :orderId="idOrder"
        @closePopup="closeForm"
        @orderCreate="orderSaved"
        @orderUpdated="orderUpdated"
        @orderRejected="orderRejected"
    />
</template>

<script>
import axios from "axios";
import {ref, provide, readonly} from "vue"
import BasePopup from "../base/BasePopup"
import BasePage from "../base/BasePage"
import BaseNotice from "../base/BaseNotice"
import BasePagination from "../base/BasePagination"
import OrderForm from "./OrderForm"

const statusList = {
    0: 'Новый',
    1: 'Создан',
    2: 'Оплачен',
    31: 'Отправлен в бд Logist',
    32: 'Сборка',
    33: 'Отгружен',
    34: 'Принят в курьерской службе',
    36: 'Доступен к выдаче ПВЗ',
    7: 'Выполнен',
    8: 'Отменен',
    9: 'Срок оплаты истек',
    100: 'Ошибка',
}

export default {
    name: "Order",
    components: {
        BasePopup,
        BasePage,
        BaseNotice,
        BasePagination,
        OrderForm,
    },
    data() {
        return {
            pagination: [],
            loading: false,
            isError: false,
            isEmpty: false,
            page: 1,
            isVisible: false,
            search: '',
            orders: [],
            idOrder: 0
        }
    },
    setup() {
        const roleList = ref([]);
        const getRoles = () => {
        }

        provide('roleList', readonly(roleList))
        provide('getRoles', getRoles)

        return {
            roleList,
            getRoles
        }
    },
    beforeMount() {
        if (!_.isEmpty(this.$route.query.page)) {
            this.page = this.$route.query.page
        }

        this.getOrders()
    },
    computed: {
        isPaid() {
            return this.data.status === 7 || ((this.data.payment_type === 3 || this.data.payment_type === 1) && this.data.status > 0)
        }
    },
    methods: {
        showSuccess(message) {
            this.$toast.add({severity: 'success', summary: 'Успех', detail: message, life: 3000});
        },
        showError(message) {
            this.$toast.add({severity: 'error', summary: 'Ошибка', detail: message, life: 3000});
        },
        getStatusText(id) {
            return statusList[id]
        },
        getOrders() {
            let params = {
                page: this.page
            }

            if (!_.isEmpty(this.search)) params.search = this.search

            this.loading = true;

            axios
                .get('/admin/backend/order', {
                    params: {...params}
                })
                .then(response => {
                    if (!_.isEmpty(response.data.orders)) {
                        this.orders = [...response.data.orders];
                        this.pagination = response.data.pagination
                        this.isEmpty = false
                    } else {
                        this.orders = []
                        this.isEmpty = true
                    }
                    this.loading = false;
                })
                .catch(() => {
                    this.isError = this.isEmpty = true
                    this.loading = false;
                })
        },
        setIdOrder(id) {
            this.idOrder = id
            this.isVisible = true
        },
        showForm() {
            this.isVisible = true
        },
        closeForm() {
            this.idOrder = 0
            this.isVisible = false
        },
        changePage(event) {
            this.page = ++event.page
            this.getOrders()
        },
        orderSaved(status) {

            if (status) {
                this.showSuccess('Заказ  сохранен успешно!')
            } else {
                this.showError('Не удалось сохранить заказ!')
            }

            if (status) {
                this.getOrders()
                this.closeForm()
            }
        },
        orderUpdated(status) {

            if (status) {
                this.showSuccess('Заказ отправлен успешно!')
            } else {
                this.showError('Не удалось отправить заказ!')
            }

            if (status) {
                this.getOrders()
                this.closeForm()
            }
        },
        orderRejected(status) {

            if (status) {
                this.showSuccess('Заказ отменен успешно!')
            } else {
                this.showError('Не удалось отменить заказ!')
            }

            if (status) {
                this.getOrders()
                this.closeForm()
            }
        },
    }
}
</script>

<style lang="scss">
.orders {
    max-width: 90%;
    width: 100%;
    margin: 0 auto;
}

.control {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-between;
}

.search {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: flex-start;

    &__btn {
        margin-left: 25px;
    }
}

.body {
    &__row {
        cursor: pointer;
    }
}

.btn {
    border: 1px solid gray;
    padding: 2px 10px;

    &:hover {
        border: 1px solid gray;
    }
}

.col {
    padding: 0;
}
</style>