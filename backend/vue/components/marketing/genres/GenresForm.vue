<template>
    <div class="form">
        <div class="form__title">
            Редактирование жанра {{ selectGenre.id }}
        </div>
        <div class="form__field-container">
            <input type="checkbox"
                   id="isCourse"
                   name="isCourse"
                   value="1"
                   v-model="isCourse"
                   :checked="isCourse"
                   @change="setIsCourse"
            >
            <label for="isCourse" class="form__label">
                Это курс
            </label>
        </div>
        <div class="form__field-container">
            <input type="checkbox"
                   id="popular"
                   name="popular"
                   value="1"
                   v-model="popular"
                   :checked="popular"
                   @change="setPopular"
            >
            <label for="popular" class="form__label">
                Популярный
            </label>
        </div>
        <div class="form__row">
            <div class="form__col">
                <div class="form__field-container">
                    <input type="checkbox"
                           id="onMain"
                           name="onMain"
                           value="1"
                           v-model="onMain"
                           :checked="onMain"
                           @change="setOnMain"
                    >
                    <label for="onMain" class="form__label">
                        На главную
                    </label>
                </div>
            </div>
            <div class="form__col">
                <button class="button button_width_auto" v-if="onMain && isCourse" @click="showGenreOnMain = !showGenreOnMain">
                    {{ showGenreOnMain ? 'Скрыть' : 'Показать' }} данные для вывода на главную
                </button>
            </div>
        </div>
        <div class="form__col" v-if="onMain && showGenreOnMain && isCourse">
            <div class="form">
                <div class="form__field-container">
                    <label for="title" class="form__label">
                        Заголовок
                    </label>
                    <base-field
                        name="title"
                        id="title"
                        type="text"
                        placeholder="Заголовок"
                        :mask="textMask"
                        :value="selectGenre.onMainInfo && selectGenre.onMainInfo.title ? String(selectGenre.onMainInfo.title) : ''"
                        @update-input-value="setTitle"
                    />
                </div>
                <div class="form__field-container">
                    <label for="subtitle" class="form__label">
                        Подзаголовок
                    </label>
                    <base-field
                        name="subtitle"
                        id="subtitle"
                        type="text"
                        placeholder="Подзаголовок"
                        :mask="textMask"
                        :value="selectGenre.onMainInfo && selectGenre.onMainInfo.subtitle ? String(selectGenre.onMainInfo.subtitle) : ''"
                        @update-input-value="setSubtitle"
                    />
                </div>
                <div class="form__field-container">
                    <label for="products" class="form__label">
                        Товары (4 id через запятую, без пробелов)
                    </label>
                    <base-field
                        name="products"
                        id="products"
                        type="text"
                        placeholder="Товары (4 id через запятую, без пробелов)"
                        :value="products"
                        @update-input-value="setProducts"
                    />
                </div>
                <div class="form__field-container">
                    <label for="text" class="form__label">
                        Текст
                    </label>
                    <base-field
                        name="text"
                        id="text"
                        type="text"
                        placeholder="Текст"
                        :mask="textMask"
                        :value="selectGenre.onMainInfo && selectGenre.onMainInfo.text ? String(selectGenre.onMainInfo.text) : ''"
                        @update-input-value="setText"
                    />
                </div>
            </div>
        </div>
        <div class="form__field-container">
            <label for="name" class="form__label">
                Название
            </label>
            <base-field
                name="name"
                id="name"
                type="text"
                placeholder="Название"
                :mask="textMask"
                :value="String(selectGenre.name)"
                @update-input-value="setName"
            />
        </div>
        <div class="form__field-container">
            <label for="sort" class="form__label">
                Сортировка
            </label>
            <base-field
                name="sort"
                id="sort"
                type="text"
                placeholder="Сортировка"
                :mask="numberMask"
                :value="String(selectGenre.sort)"
                @update-input-value="setSort"
            />
        </div>
        <div class="form__field-container">
            <label for="level" class="form__label">
                Уровень вложенности
            </label>
            <base-field
                name="level"
                id="level"
                type="text"
                placeholder="Уровень вложенности"
                :mask="numberMask"
                :value="String(selectGenre.level)"
                @update-input-value="setLevel"
            />
        </div>
        <div class="form__field-container">
            <label for="description" class="form__label">
                Описание
            </label>
            <textarea
                name="description"
                id="description"
                class="form__textarea"
                placeholder="Описание"
                v-model="selectGenre.description"
                @change="setDescription"
            ></textarea>
        </div>
        <div class="form__buttons">
            <button 
                class="button"
                :class="{'button_disabled': !hasChanges}"
                @click="save"
            >
                Сохранить
            </button>
            <button 
                class="button"
                @click="$emit('close-form')"
            >
                Закрыть
            </button>
        </div>
    </div>
</template>

<script>
import * as Vue from 'vue'
export default {
    name: 'GenresForm',
    emits: ['save', 'close-form'],
    provide() {
        return {
            onMainInfo : Vue.computed(() => this.selectGenre.onMainInfo ? this.selectGenre.onMainInfo : {}),
        }
    },
    inject: ['selectGenre'],
    data() {
        return {
            changes: {},
            textMask: {
                mask: /^[0-9A-Za-zА-Яа-яЁё\-\(\)\.\,\!\"\:\'\s]+$/,
            },
            numberMask: {
                mask: /^[0-9\s]+$/
            },
            showGenreOnMain: false,
            onMain: !!this.selectGenre.onMain,
            isCourse: !!this.selectGenre.isCourse,
            popular: !!this.selectGenre.popular,
        }
    },
    computed: {
        hasChanges() {
            return _.keys(this.changes).length > 0
        },
        products() {
            return this.selectGenre.onMainInfo.products ? this.selectGenre.onMainInfo.products.join(',') : ''
        }
    },
    watch: {
        selectGenre() {
            this.onMain = !!this.selectGenre.onMain
            this.isCourse = !!this.selectGenre.isCourse
            this.popular = !!this.selectGenre.popular
        }
    },
    methods: {
        setSimpleField(fieldValue, fieldName) {
            this.changes[fieldName] = fieldValue;
        },
        setOnMainField(fieldValue, fieldName) {
            if (!this.changes.onMainInfo) {
                this.changes.onMainInfo = this.selectGenre.onMainInfo
            }
            this.changes.onMainInfo[fieldName] = fieldValue;
        },
        setOnMain() {
            this.setSimpleField(Number(this.onMain), 'onMain')
        },
        //region On main info
        setTitle(value) {
            this.setOnMainField(value, 'title')
        },
        setSubtitle(value) {
            this.setOnMainField(value, 'subtitle')
        },
        setProducts(value) {
            this.setOnMainField(value.split(','), 'products')
        },
        setText(value) {
            this.setOnMainField(value, 'text')
        },
        //endregion
        //region Fields
        setIsCourse() {
            this.setSimpleField(Number(this.isCourse), 'isCourse')
        },
        setPopular() {
            this.setSimpleField(Number(this.popular), 'popular')
        },
        setName(value) {
            this.setSimpleField(value, 'name')
        },
        setSort(value) {
            this.setSimpleField(value, 'sort')
        },
        setLevel(value) {
            this.setSimpleField(value, 'level')
        },
        setDescription(event) {
            this.setSimpleField(event.target.value, 'description')
        },
        //endregion
        save() {
            if (!this.hasChanges) {
                return
            }
            this.$emit('save', {
                id: this.selectGenre.id,
                fields: this.changes,
            })
        }
    }
}
</script>

<style lang="scss">
.form__buttons {
    max-width: 400px;
    display: flex;
    justify-content: space-between;
    gap: 15px;
}
</style>

<style src="@vueform/multiselect/themes/default.css"></style>
