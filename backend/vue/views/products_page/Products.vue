<template>
    <app-layout
        :title="isNew ? 'Новые книги' : 'Все книги'"
    >
        <div class="products__filters">
            <div class="products__filter">
                <base-field
                    name="search"
                    id="search"
                    class="products__input"
                    type="text"
                    placeholder="Поиск по названию/артикулу Лабиринта/ISBN/..."
                    :mask="{mask: /^[0-9A-Za-zА-Яа-яЁё\!\'\.\,\s]+$/}"
                    :value="String(search)"
                    @update-input-value="setSearch"
                    @keyup.enter="sendSearch"
                />
                <button class="button products__button" @click="sendSearch">
                    Поиск
                </button>
            </div>
            <div v-if="!isNew" class="products__filter">
                <base-field
                    name="genres"
                    id="genres"
                    class="products__input"
                    type="text"
                    placeholder="Поиск по ID раздела..."
                    :mask="{mask: /^[0-9]+$/}"
                    :value="String(genresFilter)"
                    @update-input-value="setGenreFilter"
                />
                <button class="button products__button" @click="getGenres">
                    Поиск
                </button>
            </div>
        </div>
        <div class="products__content">
            <div class="products__left">
                <ul class="products__list">
                    <product-item v-if="products.length" v-for="product in products"
                                  :key="product.id"
                                  :data="product"
                                  @click="setEditedProduct(product)"
                    />
                    <div v-else class="products__empty">
                        Ничего не найдено
                    </div>
                </ul>
                <div class="products__pagination">
                    <base-pagination
                        v-if="pagination"
                        @change-page="changePage"
                        :settings="pagination"
                    />
                </div>
            </div>
            <div v-if="!isNew" class="products__right">
                <base-genres
                    @set-genre="setGenre"
                    @unset-genre="unsetGenre"
                    @create-genre="createGenre"
                    @delete-genre="deleteGenre"
                    @set-errors="setError"
                />
            </div>
        </div>

        <Dialog
            v-model:visible="showModal"
            :breakpoints="{ '960px': '75vw' }"
            :style="{ width: '60vw' }"
            :modal="true"
            :dismissableMask="true"
        >
            <template #header>
                <h5 class="m-0">
                    Карточка товара {{ editedProduct.id }}
                </h5>
            </template>

            <product-edit
                :data="editedProduct"
                @save="saveProduct"
                @empty-changes="setError('Изменения не были внесены')"
            />
        </Dialog>
        <base-message :level="2" :type="messagesType" :messages="messages" @close="closeMessage"
                      @close-all="closeMessages"/>
        <base-preloader :level="3" :is-show="showPreloader"/>

    </app-layout>
</template>

<script>
import AppLayout from "../../components/layout/AppLayout";
import {ProductsComponents} from '../../components/products'
import * as Vue from 'vue'

export default {
    name: "Products",
    components: {
        ...ProductsComponents,
        AppLayout
    },
    props: {
        isNew: {
            type: Boolean
        }
    },
    watch: {
        isNew() {
            this.getProducts()
        },
        showModal(value) {
            if (!value) {
                return;
            }
            if (this.publishingHouses.length === 0) {
                this.getPublishingHouses()
            }
            if (this.ages.length === 0) {
                this.getAges()
            }
            if (this.languages.length === 0) {
                this.getLanguages()
            }
            if (this.levels.length === 0) {
                this.getLevels()
            }
        }
    },
    provide() {
        return {
            publishingHouses: Vue.computed(() => this.simplePublishingHouses),
            ages: Vue.computed(() => this.simpleAges),
            languages: Vue.computed(() => this.simpleLanguages),
            levels: Vue.computed(() => this.simpleLevels),
            genres: Vue.computed(() => this.simpleGenres),
            selectGenre: Vue.computed(() => this.selectGenre),
            genresForTree: Vue.computed(() => this.genres),
            isNew: Vue.computed(() => this.isNew),
        }
    },
    beforeMount() {
        this.getProducts()
        this.getGenres()
    },
    data() {
        return {
            countRequests: 0,
            products: [],
            pagination: [],
            search: '',
            currentPage: 1,
            showModal: false,
            editedProduct: {},
            publishingHouses: [],
            ages: [],
            languages: [],
            levels: [],
            genres: [],
            messagesType: '',
            messages: [],
            genresWithoutChildren: [],
            selectGenre: {},
            genresFilter: '',
        }
    },
    computed: {
        simplePublishingHouses() {
            return this.simplifyCollectionForSelect(this.publishingHouses)
        },
        simpleAges() {
            return this.simplifyCollectionForSelect(this.ages)
        },
        simpleLanguages() {
            return this.simplifyCollectionForSelect(this.languages)
        },
        simpleLevels() {
            return this.simplifyCollectionForSelect(this.levels)
        },
        simpleGenres() {
            return this.simplifyCollectionForSelect(this.genresWithoutChildren, true)
        },
        showPreloader() {
            return this.countRequests > 0
        }
    },
    methods: {
        // region Get data
        getProducts() {
            let params = {
                page: this.currentPage,
                isNew: Number(this.isNew),
            }

            if (this.search) {
                params.simpleSearch = this.search
            }

            if (_.keys(this.selectGenre).length > 0) {
                params.genres = [this.selectGenre.id]
            }

            this.countRequests++
            axios
                .get('/admin/backend/product', {
                    params: params
                })
                .then(response => {
                    this.products = response.data.products
                    this.pagination = response.data.pagination
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.countRequests--
                })
        },
        getPublishingHouses() {
            this.countRequests++
            axios
                .get('/admin/backend/publishing-house')
                .then(response => {
                    this.publishingHouses = response.data
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.countRequests--
                })
        },
        getAges() {
            this.countRequests++
            axios
                .get('/admin/backend/age')
                .then(response => {
                    this.ages = response.data
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.countRequests--
                })
        },
        getLanguages() {
            this.countRequests++
            axios
                .get('/admin/backend/language')
                .then(response => {
                    this.languages = response.data
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.countRequests--
                })
        },
        getLevels() {
            this.countRequests++
            axios
                .get('/admin/backend/level')
                .then(response => {
                    this.levels = response.data
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.countRequests--
                })
        },
        getGenres() {
            this.countRequests++
            axios
                .get('/admin/backend/genre', {
                    params:
                        this.genresFilter ? {
                            id: this.genresFilter,
                            buildTree: 0
                        } : {
                            buildTree: 1
                        }
                })
                .then(response => {
                    if (!this.genresFilter) {
                        this.allGenres = response.data
                    }
                    this.genres = response.data
                    this.setGenresWithoutChildren(this.allGenres)
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.countRequests--
                })
        },
        updateProduct(id) {
            let index = _.findIndex(this.products, {id: id})
            if (index === -1) {
                this.setError('Что-то пошло не так')
                return
            }

            this.countRequests++
            axios
                .get('/admin/backend/product', {
                    params: {id: id}
                })
                .then(response => {
                    if (response.data.products[0]) {
                        this.products[index] = response.data.products[0]
                    } else {
                        this.setError('Что-то пошло не так')
                    }
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.countRequests--
                })
        },
        //endregion
        // region Send data
        saveProduct(data) {
            if (!data.id) {
                this.setError('Что-то пошло не так')
                return
            }

            this.countRequests++
            axios
                .patch(`/admin/backend/product?id=${data.id}`, data.fields)
                .then(response => {
                    if (response.data.errors) {
                        this.setError(response.data.errors)
                    } else {
                        this.showModal = false
                        if (this.isNew) {
                            this.setSuccess('Успешно сохранено. Продукт больше не новый')
                            this.getProducts()
                        } else {
                            this.setSuccess('Успешно сохранено')
                            this.updateProduct(data.id)
                        }
                    }
                })
                .catch(error => {
                    this.setError(error)
                })
                .finally(() => {
                    this.countRequests--
                })
        },
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
        // region Setters
        setEditedProduct(product) {
            this.editedProduct = product
            this.showModal = true
        },
        setGenre(genre) {
            this.selectGenre = genre
            this.getProducts(true)
        },
        unsetGenre() {
            this.selectGenre = {}
            this.getProducts(true)
        },
        setGenreName(name) {
            this.genreName = name
        },
        setSearch(value) {
            this.search = value
        },
        sendSearch() {
            if (this.search.length > 2 || this.search.length === 0) {
                this.getProducts()
            }
        },
        setGenreFilter(value) {
            this.genresFilter = value
        },
        setGenresWithoutChildren(collection) {
            collection.forEach((item) => {
                if (item.children) {
                    this.genresWithoutChildren.push(item)
                    this.setGenresWithoutChildren(item.children)
                } else {
                    this.genresWithoutChildren.push(item)
                }
            })
        },
        changePage(number) {
            this.currentPage = number
            this.getProducts(true)
        },
        hideEditProductModal() {
            this.showModal = false
        },
        // endregion
        simplifyCollectionForSelect(array, asTree = false) {
            let result = []
            array.forEach((item) => {
                result.push({
                    value: item.id,
                    label: asTree ? `${'---'.repeat(item.level)} ${item.name}` : item.name
                })
            })
            return result
        },
        createGenre() {
            this.unsetGenre()
            this.getGenres()
            this.setSuccess('Успешно создано')
        },
        deleteGenre() {
            this.unsetGenre()
            this.getGenres()
            this.setSuccess('Успешно удален')
        }
    }
}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
<style lang="scss">
.products {
    &__content {
        display: flex;
    }

    &__left {
        flex-grow: 1;
        border: solid 1px $gray;
    }

    &__right {
        min-width: 500px;
        padding: 30px;
        border: solid 1px $gray;
        border-left: none;
    }

    &__pagination {
        display: flex;
        justify-content: center;
        margin: 10px 0;
    }

    &__filters {
        display: flex;
        justify-content: space-between;
        margin-bottom: 50px;
    }

    &__filter {
        display: flex;
        width: 500px;
    }

    &__input {
        flex-grow: 1;

        &_small {
            width: 440px;
        }

        &_mb {
            margin-bottom: 10px;
        }
    }

    &__button {
        margin-left: 20px;
    }

    &__genre-form {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        width: 440px;
        margin-bottom: 20px;
    }

    &__genre-create-button {
        margin-right: 20px;
    }

    &__empty {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 60px;
        padding: 30px;
    }
}
</style>