<template>
    <app-layout title="Акции">
        <div class="banners__add">
            <button
                @click="showCreate"
                class="control__btn btn btn_new-user">Добавить акцию
            </button>
        </div>
        <div class="banners__content">
            <table class="banners__table table">
                <thead class="table__header header">
                <tr class="header__row row">
                    <th class="header__col col">ID</th>
                    <th class="header__col col">Название</th>
                    <th class="header__col col">Дата начала</th>
                    <th class="header__col col">Дата окончания</th>
                    <th class="header__col col">Черновик</th>                    
                </tr>
                </thead>

                <tbody class="table__body body">
                <promotion-item v-for="promotion in promotions"
                                :key="promotion.id"
                                :data="promotion"
                                @click="editPromotion(promotion)"/>
                </tbody>
            </table>

            <base-pagination
                v-if="pagination"
                @change-page="changePage"
                :settings="pagination"
            />
        </div>

        <Dialog
            v-model:visible="showModal"
            :breakpoints="{ '960px': '75vw' }"
            :style="{ width: '60vw' }"
            :modal="true"
            :dismissableMask="true"
        >
            <template #header>
                <h5 v-if="this.editingPromotion.hasOwnProperty('id')" class="m-0">
                    Редактировать акцию {{ editingPromotion.id }}
                </h5>
                <h5 v-else class="m-0">
                    Создать новую акцию
                </h5>
            </template>

            <promotion-form
                v-if="showModal"
                :data="this.editingPromotion"
                @update="update"
                @create="create"
                @delete="this.delete"
            />
        </Dialog>
        <base-message :level="2"
                      :type="messagesType"
                      :messages="messages"
                      @close="closeMessage"
                      @close-all="closeMessages"/>
        <base-preloader :level="3" :is-show="showPreloader"/>
    </app-layout>
</template>

<script>
import AppLayout from "../../../components/layout/AppLayout";
import {PromotionsComponents} from '../../../components/marketing/promotions'

export default {
    name: "Promotions",
    components: {
        ...PromotionsComponents,
        AppLayout
    },
    data() {
        return {
            promotions: [],
            pagination: [],
            currentPage: 1,
            showModal: false,
            editingPromotion: {},
            messagesType: 'success',
            messages: [],
            showPreloader: false,
        }
    },
    beforeMount() {
        this.getPromotions()
    },
    methods: {
        getPromotions() {
            this.showPreloader = true

            axios
                .get('/admin/backend/promotion', {
                    params: {
                        page: this.currentPage
                    }
                })
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.promotions = response.data.data
                        this.pagination = response.data.pagination
                    }
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.showPreloader = false
                })
        },
        editPromotion(promotion) {
            this.editingPromotion = promotion
            this.showModal = true
        },
        showCreate() {
            this.editingPromotion = {}
            this.showModal = true
        },
        create(data) {
            this.showPreloader = true
            axios
                .post('/admin/backend/promotion', data,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    }
                )
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.hideModal()
                        this.getPromotions()
                        this.setSuccess('Успешно создано')
                    }
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() =>{
                    this.showPreloader = false
                })
        },
        update(data) {
            this.showPreloader = true
            axios
                .patch(`/admin/backend/promotion?id=${data.get('id')}`, data)
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.hideModal()
                        this.getPromotions()
                        this.setSuccess('Успешно сохранено')
                    }
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.showPreloader = false
                })
        },
        delete(id) {
            this.showPreloader = true
            axios
                .delete(`/admin/backend/promotion?id=${id}`)
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.hideModal()
                        this.getPromotions()
                        this.setSuccess('Успешно удалено')
                    }
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.showPreloader = false
                })
        },
        hideModal() {
            this.showModal = false
            this.editingBanner = {}
        },
        setError(messages) {
            this.messagesType = 'error'
            this.messages = messages
        },
        setSuccess(messages) {
            this.messagesType = 'success'
            this.messages = messages
        },
        closeMessage(key) {
            this.messages.splice(key, 1)
        },
        closeMessages() {
            this.messages = []
        },
        changePage(value) {
            this.currentPage = value
            this.getPromotions()
        },
    }
}
</script>

<style lang="scss" scoped>
.banners {
    &__add {
        display: flex;
        justify-content: right;
    }
}
</style>