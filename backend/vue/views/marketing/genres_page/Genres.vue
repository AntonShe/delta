<template>
    <app-layout title="Жанры (курсы)">
        <div class="genres__row">
            <div class="genres__col">
                <genres-form
                    v-if="showForm"
                    @save="save"
                    @close-form="unsetGenre"
                />
                <div v-else>
                    Выберите жанр для редактирования
                </div>
            </div>
            <div class="genres__small-col">
                <base-genres
                    @set-genre="setGenre"
                    @unset-genre="unsetGenre"
                    @create-genre="create"
                    @delete-genre="this.delete"
                    @set-errors="setError"
                />
            </div>
        </div>

        <base-message :level="1" :type="messagesType"
                      :messages="messages"
                      @close="closeMessage"
                      @close-all="closeMessages"/>
        <base-preloader :level="2" :is-show="showPreloader"/>
    </app-layout>
</template>

<script>
import AppLayout from "../../../components/layout/AppLayout";
import {GenresComponents} from '../../../components/marketing/genres'
import * as Vue from 'vue'

export default {
    name: "Genres",
    components: {
        ...GenresComponents,
        AppLayout
    },
    provide() {
        return {
            selectGenre : Vue.computed(() => this.selectGenre),
            genresForTree : Vue.computed(() => this.genres),
        }
    },
    beforeMount() {
        this.getGenres()
    },
    data() {
        return {
            showPreloader: false,
            messagesType: 'success',
            messages: [],
            genres: [],
            selectGenre: {},
        }
    },
    computed: {
        showForm() {
            return _.keys(this.selectGenre).length > 0
        }
    },
    methods: {
        setGenre(genre) {
            this.selectGenre = genre
        },
        unsetGenre() {
            this.selectGenre = {}
        },
        //region CRUD
        getGenres() {
            this.showPreloader = true
            axios
                .get('/admin/backend/genre', {
                    params: {
                        buildTree: 1
                    }
                })
                .then(response => {
                    if (!response.errors) {
                        this.genres = response.data
                    } else {
                        this.setError(response.errors)
                    }
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.showPreloader = false
                })
        },
        create() {
            this.setSuccess('Успешно создано')
            this.unsetGenre()
            this.getGenres()
        },
        save(data) {
            if (!data.id) {
                this.setError('Что-то пошло не так')
                return
            }

            this.showPreloader = true
            axios
                .patch(`/admin/backend/genre?id=${data.id}`, data.fields)
                .then(response => {
                    if (!response.errors) {
                        this.setSuccess('Успешно сохранено')
                        this.unsetGenre()
                    } else {
                        this.setError(response.errors)
                    }
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.getGenres()
                })
        },
        delete() {
            this.setSuccess('Успешно удален')
            this.unsetGenre()
            this.getGenres()
        },
        //endregion
        // region Messages
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
        // endregion
    }
}
</script>

<style src="../../../../node_modules/@vueform/multiselect/themes/default.css"></style>
<style lang="scss">
.genres {
    &__row {
        display: flex;
    }

    &__col {
        flex-grow: 1;
    }

    &__small-col{
        width: 460px;
    }
}
</style>