<template>
    <div class="form">
        <div class="form__field-container form__checkbox">
            <input
                v-model=shelf.isActive
                type="checkbox"
                id="active"
                name="active"
                @change="hasChanges=true"
            >
            <label for="active" class="form__label form__checkbox-label">
                Активно
            </label>
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
                :value="shelf.name"
                @input="update('name', $event)"
            />
        </div>
        <div class="form__field-container">
            <label for="products" class="form__label">
                Список книг (через запятую, без пробелов)
            </label>
            <base-field
                name="products"
                id="products"
                type="textarea"
                placeholder="Список книг (через запятую, без пробелов)"
                :value="products"
                @change="updateProducts('products', $event)"
            />
        </div>
        <div class="form__field-container">
            <label for="sort" class="form__label">
                Приоритет
            </label>
            <base-field
                name="sort"
                id="sort"
                type="text"
                placeholder="Приоритет"
                :value="String(shelf.sort)"
                @input="update('sort', $event)"
            />
        </div>
        <div class="form__field-container">
            <label for="startDate" class="form__label">
                Дата начала
            </label>
            <base-field
                name="startDate"
                id="startDate"
                type="date"
                placeholder="Дата начала"
                :value="shelf.startDate"
                @input="update('startDate', $event)"
            />
        </div>
        <div class="form__field-container">
            <label for="endDate" class="form__label">
                Дата окончания
            </label>
            <base-field
                name="endDate"
                id="endDate"
                type="date"
                placeholder="Дата окончания"
                :value="shelf.endDate"
                @input="update('endDate', $event)"
            />
        </div>
        <div class="form__buttons">
            <div class="form__row">
                <div class="form__col">
                    <button class="button" :class="{'button_disabled': !hasChanges}" @click="save">
                        {{ isEdit ? 'Обновить' : 'Сохранить' }}
                    </button>
                </div>
                <div class="form__col">
                    <button v-if="isEdit" class="button button_bg-color_red" @click="this.delete">
                        Удалить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ShelfForm",
    emits: ['create', 'update', 'delete'],
    props: {
        data: {
            type: Object,
            default: () => ({
                id: '',
                name: '',
                sort: '',
                startDate: '',
                endDate: '',
                products: [],
                isActive: true
            })
        }
    },
    data() {
        return {
            shelf: {...this.data, isActive: !!this.data.isActive},
            hasChanges: false,
        }
    },
    computed: {
        products() {
            return this.shelf.products ? this.shelf.products.join(',') : ''
        },
        isEdit() {
            return this.shelf.hasOwnProperty('id')
        },
    },
    watch: {
        data(value) {
            this.shelf = value
            this.hasChanges = false
        },
    },
    methods: {
        update(attribute, event) {
            this.shelf[attribute] = event.target.value
            this.hasChanges = true
        },
        updateProducts(attribute, event) {
            this.shelf[attribute] = event.target.value.split(',')
            this.hasChanges = true
        },
        save() {
            if (!this.hasChanges) {
                return false
            }

            this.formatData()

            if (this.isEdit) {
                this.$emit('update', this.shelf)
            } else {
                this.$emit('create', this.shelf)
            }
        },
        delete() {
            this.$emit('delete', this.shelf.id)
        },
        formatData() {
            this.shelf.isActive = Number(this.shelf.isActive)
            this.shelf.products = [...new Set(this.shelf.products)]

            for (let key in this.shelf) {
                if (!this.shelf[key] && key !== 'isActive') {
                    delete this.shelf[key]
                }
            }
        },
    }
}
</script>

<style lang="scss" scoped>
.form {
    &__row {
        margin-top: 15px;
    }
}
</style>