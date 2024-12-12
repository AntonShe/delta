<template>
    <div class="form">
        <div class="form__row">
            <div class="form__field-container">
                <input type="checkbox"
                       id="active"
                       name="active"
                       value="1"
                       v-model="isActive"
                       :checked="isActive"
                       @change="setActive"
                >
                <label for="active" class="form__label">
                     Активность
                </label>
            </div>
        </div>
        <div class="form__row">
            <div class="form__field-container">
                <input type="checkbox"
                       id="popular"
                       name="popular"
                       value="1"
                       v-model="isPopular"
                       :checked="isPopular"
                       @change="setPopular"
                >
                <label for="popular" class="form__label">
                     Выводить в популярном
                </label>
            </div>
        </div>
        <div class="form__row">
            <div class="form__col">
                <div class="form__field-container">
                    <label for="title" class="form__label">
                        Название книги
                    </label>
                    <base-field
                        name="title"
                        id="title"
                        type="text"
                        placeholder="Название книги"
                        :value="String(data.title)"
                        @update-input-value="setTitle"
                    />
                </div>
                <div class="form__field-container">
                    <label for="authors" class="form__label">
                        Авторы
                    </label>
                    <base-field
                        name="authors"
                        id="authors"
                        type="text"
                        placeholder="Авторы"
                        :mask="textMask"
                        :value="String(data.authors)"
                        @update-input-value="setAuthors"
                    />
                </div>
                <div class="form__field-container">
                    <label for="publishingHouseId" class="form__label">
                        Издатель
                    </label>
                    <multiselect
                        id="publishingHouseId"
                        name="publishingHouseId"
                        :value="productPublishingHouse"
                        autocomplete="label"
                        :options="publishingHouses"
                        :object="true"
                        :canClear="false"
                        :canDeselect="false"
                        :searchable="true"
                        @change="setPublishingHouseId"
                    />
                </div>
                <div class="products-edit__image-container">
                    <img :src="data.cover" alt="" class="products-edit__image">
                </div>
            </div>
            <div class="form__col">
                <div class="products-edit__info">
                    <p>Артикул Лабиринта: {{ data.labirintId ? data.labirintId : 'Отсутствует' }}</p>
                    <p>ISBN: {{ data.isbn }}</p>
                    <p>Цена: {{ data.price }} руб</p>
                    <p>Цена с НДС: {{ data.additionalPrice }} руб</p>
                    <p>Кол-во: {{ data.quantity }} шт</p>
                    <p>Рейтинг: {{ data.rating }}</p>
                    <a :href="`/product/${data.id}`" class="button">
                        Ссылка на сайт
                    </a>
                </div>
            </div>
        </div>
        <div class="form__row">
            <div class="form__col">
                <div class="form__field-container">
                    <label for="publishingYear" class="form__label">
                        Дата выпуска
                    </label>
                    <base-field
                        name="publishingYear"
                        id="publishingYear"
                        type="text"
                        placeholder="Дата выпуска"
                        :mask="numberMask"
                        :value="String(data.publishingYear)"
                        @update-input-value="setPublishingYear"
                    />
                </div>
                <div class="form__field-container">
                    <label for="volumesCount" class="form__label">
                        Кол-во томов
                    </label>
                    <base-field
                        name="volumesCount"
                        id="volumesCount"
                        type="text"
                        placeholder="Кол-во томов"
                        :mask="numberMask"
                        :value="String(data.volumesCount)"
                        @update-input-value="setVolumesCount"
                    />
                </div>
                <div class="form__field-container">
                    <label for="pagesNumber" class="form__label">
                        Кол-во страниц
                    </label>
                    <base-field
                        name="pagesNumber"
                        id="pagesNumber"
                        type="text"
                        placeholder="Кол-во страниц"
                        :mask="numberMask"
                        :value="String(data.pagesNumber)"
                        @update-input-value="setPagesNumber"
                    />
                </div>
                <div class="form__field-container">
                    <label for="size" class="form__label">
                        Размер
                    </label>
                    <base-field
                        name="size"
                        id="size"
                        type="text"
                        placeholder="Размер"
                        :mask="textMask"
                        :value="String(data.size)"
                        @update-input-value="setSize"
                    />
                </div>
            </div>
            <div class="form__col">
                <div class="form__field-container">
                    <label for="color" class="form__label">
                        Цвет
                    </label>
                    <base-field
                        name="color"
                        id="color"
                        type="text"
                        placeholder="Цвет"
                        :mask="textMask"
                        :value="String(data.color)"
                        @update-input-value="setColor"
                    />
                </div>
                <div class="form__field-container">
                    <label for="ages" class="form__label">
                        Возрастная категория
                    </label>
                    <multiselect
                        id="ages"
                        name="ages"
                        :value="productAges"
                        mode="tags"
                        autocomplete="label"
                        :options="ages"
                        :object="true"
                        :closeOnSelect="false"
                        :closeOnDeselect="false"
                        :searchable="true"
                        @change="setAges"
                    />
                </div>
                <div class="form__field-container">
                    <label for="weight" class="form__label">
                        Вес
                    </label>
                    <base-field
                        name="weight"
                        id="weight"
                        type="text"
                        placeholder="Вес"
                        :mask="numberMask"
                        :value="String(data.weight)"
                        @update-input-value="setWeight"
                    />
                </div>
                <div class="form__field-container">
                    <label for="pageMaterial" class="form__label">
                        Тип бумаги
                    </label>
                    <base-field
                        name="pageMaterial"
                        id="pageMaterial"
                        type="text"
                        placeholder="Тип бумаги"
                        :mask="textMask"
                        :value="String(data.pageMaterial)"
                        @update-input-value="setPageMaterial"
                    />
                </div>
                <div class="form__field-container">
                    <label for="bindingMaterial" class="form__label">
                        Переплет
                    </label>
                    <base-field
                        name="bindingMaterial"
                        id="bindingMaterial"
                        type="text"
                        placeholder="Переплет"
                        :mask="textMask"
                        :value="String(data.bindingMaterial)"
                        @update-input-value="setBindingMaterial"
                    />
                </div>
            </div>
        </div>
        <div class="form__field-container">
            <label for="annotation" class="form__label">
                Аннотация
            </label>
            <textarea
                name="annotation"
                id="annotation"
                class="form__textarea"
                placeholder="Аннотация"
                v-model="annotation"
                @change="setAnnotation"
            ></textarea>
        </div>
        <div class="form__field-container">
            <label for="shortAnnotation" class="form__label">
                Краткая аннотация
            </label>
            <textarea
                name="shortAnnotation"
                id="shortAnnotation"
                class="form__textarea"
                placeholder="Аннотация"
                v-model="shortAnnotation"
                @change="setShortAnnotation"
            ></textarea>
        </div>
        <div class="form__separator"></div>
        <div class="form__field-container">
            <label for="languages" class="form__label">
                Язык
            </label>
            <multiselect
                id="languages"
                name="languages"
                :value="productLanguages"
                mode="tags"
                autocomplete="label"
                :options="languages"
                :object="true"
                :closeOnSelect="false"
                :closeOnDeselect="false"
                :searchable="true"
                @change="setLanguages"
            />
        </div>
        <div class="form__field-container">
            <label for="genres" class="form__label">
                Раздел(ы)
            </label>
            <multiselect
                id="genres"
                name="genres"
                :value="productGenres"
                mode="tags"
                autocomplete="label"
                :options="genres"
                :object="true"
                :closeOnSelect="false"
                :closeOnDeselect="false"
                :searchable="true"
                @change="setGenres"
            />
        </div>
        <div class="form__field-container">
            <label for="levels" class="form__label">
                Уровень
            </label>
            <multiselect
                id="levels"
                name="levels"
                :value="productLevels"
                mode="tags"
                autocomplete="label"
                :options="levels"
                :object="true"
                :closeOnSelect="false"
                :closeOnDeselect="false"
                :searchable="true"
                @change="setLevels"
            />
        </div>
        <div class="form__buttons">
            <button class="button"
                    :class="{'button_disabled': !hasChanges}"
                    @click="save">
                Сохранить
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "ProductEdit",
    emits: ['save', 'close', 'emptyChanges'],
    inject: ['publishingHouses', 'ages', 'languages', 'levels', 'genres', 'isNew'],
    props: {
        data: {
            type: Object
        },
    },
    data() {
        return {
            changes: {},
            textMask: {
                mask: /^[0-9A-Za-zА-Яа-яЁё\!\'\s]+$/,
            },
            numberMask: {
                mask: /^[0-9\s]+$/
            },
            isActive: !!this.data.active,
            isPopular: !!this.data.isPopular,
            annotation: this.data.annotation,
            shortAnnotation: this.data.shortAnnotation,
        }
    },
    computed: {
        productPublishingHouse() {
            return this.getSimpleField('publishingHouse')
        },
        productAges() {
            return this.getArrayField('ages')
        },
        productLanguages() {
            return this.getArrayField('languages')
        },
        productGenres() {
            return this.getArrayField('genres')
        },
        productLevels() {
            return this.getArrayField('levels')
        },
        hasChanges() {
            return _.keys(this.changes).length > 0
        }
    },
    watch: {
        data(newValue) {
            this.changes = {}
            this.isActive = !!newValue.active
            this.isPopular = !!newValue.isPopular
            this.annotation = newValue.annotation
            this.shortAnnotation = newValue.shortAnnotation
        }
    },
    methods: {
        getSimpleField(fieldName) {
            return {
                value: this.data[`${fieldName}Id`],
                label: this.data[fieldName],
            }
        },
        getArrayField(fieldName) {
            return _.map(this.data[fieldName], item => {
                return {
                    value: item.id,
                    label: item.name,
                }
            })
        },
        setActive() {
            this.setSimpleField(Number(this.isActive), 'active')
        },
        setPopular() {
            this.setSimpleField(Number(this.isPopular), 'isPopular')
        },
        setAnnotation() {
            this.setSimpleField(this.annotation, 'annotation')
        },
        setShortAnnotation() {
            this.setSimpleField(this.shortAnnotation, 'shortAnnotation')
        },
        setTitle(value) {
            this.setSimpleField(value, 'title')
        },
        setPublishingHouseId(value) {
            this.setSimpleField(value.value, 'publishingHouseId')
        },
        setPublishingYear(value) {
            this.setSimpleField(value, 'publishingYear')
        },
        setVolumesCount(value) {
            this.setSimpleField(value, 'volumesCount')
        },
        setPagesNumber(value) {
            this.setSimpleField(value, 'pagesNumber')
        },
        setPageMaterial(value) {
            this.setSimpleField(value, 'pageMaterial')
        },
        setBindingMaterial(value) {
            this.setSimpleField(value, 'bindingMaterial')
        },
        setSize(value) {
            this.setSimpleField(value, 'size')
        },
        setColor(value) {
            this.setSimpleField(value, 'color')
        },
        setAges(value) {
            this.setArrayField(value, 'ages')
        },
        setWeight(value) {
            this.setSimpleField(value, 'weight')
        },
        setLevels(value) {
            this.setArrayField(value, 'levels')
        },
        setLanguages(value) {
            this.setArrayField(value, 'languages')
        },
        setAuthors(value) {
            this.setSimpleField(value, 'authors')
        },
        setGenres(value) {
            this.setArrayField(value, 'genres')
        },
        setSimpleField(fieldValue, fieldName) {
            this.changes[fieldName] = fieldValue;
        },
        setArrayField(fieldValue, fieldName) {
            this.changes[fieldName] = _.map(fieldValue, 'value');
        },
        save() {
            if (!this.hasChanges) {
                this.$emit('emptyChanges')
                return
            }
            if (this.isNew) {
                this.changes.isNew = 0
            }
            this.$emit('save', {
                id: this.data.id,
                fields: this.changes,
            })
        }
    }
}
</script>

<style lang="scss">
    .products-edit{
        &__info {
            width: 250px;
            margin-left: auto;
        }

        &__image {
            max-width: 200px;
            max-height: 200px;
        }
    }
</style>

<style src="@vueform/multiselect/themes/default.css"></style>
