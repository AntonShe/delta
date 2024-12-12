<template>
    <app-layout title="Полки на главной">
        <div class="shelves__add">
            <button
                @click="showCreate"
                class="control__btn btn btn_new-user">Добавить полку
            </button>
        </div>
        <div class="shelves__content">
            <table class="shelves__table table">
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
                <shelf-item
                    v-for="shelf in shelves"
                    :key="shelf.id"
                    :data="shelf"
                    @click="editShelf(shelf)"
                />
                </tbody>
            </table>
        </div>

        <Dialog
            v-model:visible="showModal"
            :breakpoints="{ '960px': '75vw' }"
            :style="{ width: '60vw' }"
            :modal="true"
            :dismissableMask="true"
        >
            <template #header>
                <h5 v-if="this.editedShelf.hasOwnProperty('id')" class="m-0">
                    Редактировать полку {{ editedShelf.id }}
                </h5>
                <h5 v-else class="m-0">
                    Создать новую полку
                </h5>
            </template>

            <shelf-form
                v-if="showModal"
                :data="editedShelf"
                @create="create"
                @update="update"
                @delete="this.delete"
            />
        </Dialog>
        <base-message :level="2"
                      :type="messagesType"
                      :messages="messages"
                      @close="closeMessage"
                      @close-all="closeMessages"
        />
        <base-preloader :level="3" :is-show="showPreloader"/>
    </app-layout>
</template>

<script>
import AppLayout from "../../../components/layout/AppLayout";
import {ShelvesComponents} from "../../../components/marketing/shelves";

export default {
    name: "Shelves",
    components: {
        ...ShelvesComponents,
        AppLayout
    },
    data() {
        return {
            shelves: [],
            pagination: [],
            page: 1,
            showModal: false,
            editedShelf: {},
            messagesType: 'success',
            messages: [],
            showPreloader: false,
        }
    },
    beforeMount() {
        this.getShelves()
    },
    methods: {
        getShelves() {
            let params = {
                page: this.page
            }

            axios.get('/admin/backend/shelf', {params: params})
                .then(response => {
                    this.shelves = response.data.shelves
                    this.pagination = response.data.pagination
                })
                .catch(error => {
                    throw new Error('Ошибка, ' + error )
                })
        },
        showCreate() {
            this.editedShelf = {isActive: true}
            this.showModal = true
        },
        editShelf(shelf) {
            this.editedShelf = shelf
            this.showModal = true
        },
        hideModal() {
            this.showModal = false
            this.editedShelf = {}
        },
        create(data) {
            this.showPreloader = true
            axios
                .post('/admin/backend/shelf', data)
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.hideModal()
                        this.getShelves()
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
                .patch(`/admin/backend/shelf?id=${data.id}`, data)
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.hideModal()
                        this.getShelves()
                        this.setSuccess('Успешно сохранено')
                    }
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() =>{
                    this.showPreloader = false
                })
        },
        delete(id) {
            this.showPreloader = true
            axios
                .delete(`/admin/backend/shelf?id=${id}`)
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.hideModal()
                        this.getShelves()
                        this.setSuccess('Успешно удалено')
                    }
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() =>{
                    this.showPreloader = false
                })
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
    }
}
</script>

<style lang="scss" scoped>
.shelves {
    &__add {
        display: flex;
        justify-content: right;
    }
}
</style>