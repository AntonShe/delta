<template>
    <app-layout title="Баннеры">
        <div class="banners__add">
            <button
                @click="showCreate"
                class="control__btn btn btn_new-user"
            >
                Добавить баннер
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
                    <th class="header__col col">Приоритет</th>
                    <th class="header__col col">Черновик</th>                    
                </tr>
                </thead>

                <tbody class="table__body body">
                <banner-item
                    v-for="banner in banners"
                    :key="banner.id"
                    :data="banner"
                    @click="editBanner(banner)"
                />
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
                <h5 v-if="this.editingBanner.hasOwnProperty('id')" class="m-0">
                    Редактировать баннер {{ editingBanner.id }}
                </h5>
                <h5 v-else class="m-0">
                    Создать новый баннер
                </h5>
            </template>

            <banner-form
                v-if="showModal"
                :data="this.editingBanner"
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
import {BannersComponents} from '../../../components/marketing/banners'

export default {
    name: "Banners",
    components: {
        ...BannersComponents,
        AppLayout
    },
    data() {
        return {
            banners: [],
            pagination: [],
            currentPage: 1,
            showModal: false,
            editingBanner: {},
            messagesType: 'success',
            messages: [],
            showPreloader: false,
        }
    },
    beforeMount() {
        this.getBanners()
    },
    methods: {
        getBanners() {
            this.showPreloader = true
            let params = {
                page: this.currentPage
            }

            axios
                .get('/admin/backend/banner', {
                    params: params
                })
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.banners = response.data.banners
                        this.pagination = response.data.pagination
                    }
                })
                .catch(error => {
                    this.setError(error)
                    throw new Error('Ошибка, ' + error )
                })
                .finally(() => {
                    this.showPreloader = false
                })
        },
        editBanner(banner) {
            this.editingBanner = banner
            this.showModal = true
        },
        showCreate() {
            this.editingBanner = {}
            this.showModal = true
        },
        create(data) {
            this.showPreloader = true
            axios
                .post('/admin/backend/banner', data,
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
                        this.getBanners()
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
                .patch(`/admin/backend/banner?id=${data.get('id')}`, data)
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.hideModal()
                        this.getBanners()
                        this.setSuccess('Успешно сохранено')
                    }
                })
                .catch(error => {
                    this.setError(error)
                    throw new Error('Ошибка, ' + error )
                })
                .finally(() => {
                    this.showPreloader = false
                })
        },
        delete(id) {
            this.showPreloader = true
            axios
                .delete(`/admin/backend/banner?id=${id}`)
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.hideModal()
                        this.getBanners()
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