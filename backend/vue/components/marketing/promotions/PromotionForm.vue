<template>
    <div class="form">
        <div class="form__field-container form__checkbox">
            <input
                v-model=promotion.isActive            
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
            <label for="title" class="form__label">
                Название
            </label>
            <base-field
                name="title"
                id="title"
                type="text"
                placeholder="Название"
                :value="promotion.title"
                @input="update('title', $event)"
            />
        </div>
        <div class="form__field-container">
            <label for="annotation" class="form__label">
                Аннотация
            </label>
            <base-field
                name="annotation"
                id="annotation"
                type="textarea"
                placeholder="Аннотация"
                :value="promotion.annotation"
                @input="update('annotation', $event)"
            />
        </div>
        <div class="form__row">
            <div class="form__col">
                <div class="form__field-container">
                    <label for="imageFile" class="form__label">
                        Обложка
                    </label>
                    <base-field
                        class="form__input-file-container"
                        name="imageFile"
                        id="imageFile"
                        type="file"
                        placeholder="Обложка"
                        @change="update('imageFile', $event)"
                    />
                </div>
                <img v-if="promotion.image" class="promotion__image" :src="formatImageUrl(promotion.image)">
            </div>
            <div class="form__col">
                <div class="form__field-container">
                    <label for="tablet_imageFile" class="form__label">
                        Обложка планшет
                    </label>
                    <base-field
                        class="form__input-file-container"
                        name="tablet_imageFile"
                        id="tablet_imageFile"
                        type="file"
                        placeholder="Обложка планшет"
                        @change="update('tablet_imageFile', $event)"
                    />
                </div>
                <img v-if="promotion.tabletImage" class="promotion__image" :src="formatImageUrl(promotion.tabletImage)">
            </div>
            <div class="form__col">
                <div class="form__field-container">
                    <label for="mobile_imageFile" class="form__label">
                        Обложка телефон
                    </label>
                    <base-field
                        class="form__input-file-container"
                        name="mobile_imageFile"
                        id="mobile_imageFile"
                        type="file"
                        placeholder="Обложка телефон"
                        @change="update('mobile_imageFile', $event)"
                    />
                </div>
                <img v-if="promotion.mobileImage" class="promotion__image" :src="formatImageUrl(promotion.mobileImage)">
            </div>
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
                :value="this.promotion.startDate"
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
                :value="this.promotion.endDate"
                @input="update('endDate', $event)"
            />
        </div>
        <div class="form__field-container">
            <label for="link" class="form__label">
                Ссылка
            </label>
            <base-field
                name="link"
                id="link"
                type="text"
                placeholder="Ссылка"
                :value="this.promotion.link || ''"
                @input="update('link', $event)"
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
    name: "PromotionForm",
    emits: ['create', 'update', 'delete'],
    props: {
        data: {
            type: Object,
            default: () => ({
                image: '',
                tablet_image: '',
                mobile_image: '',
                title: '',
                annotation: '',
                link: '',
                startDate: '',
                endDate: '',
                isActive: true
            })
        }
    },
    data() {
        return {
            promotion: {...this.data, isActive: !!this.data.isActive},
            showPreloader: false,
            hasChanges: false
        }
    },
    computed: {
        isEdit() {
            return this.promotion.hasOwnProperty('id')
        },
    },
    watch: {
        data(value) {
            this.promotion = value
            this.hasChanges = false
        },
    },
    methods: {
        update(prop, e) {
            this.hasChanges = true
            this.promotion[prop] = e.target.value
        },
        save() {
            if (!this.hasChanges) {
                return false
            }
            let data = this.getFormData()
            if (this.isEdit) {
                this.$emit('update', data)
            } else {
                this.$emit('create', data)
            }
        },
        delete() {
            this.$emit('delete', this.getFormData().get('id'))
        },
        getFormData() {
            let formData = new FormData()

            this.promotion.isActive = Number(this.promotion.isActive)

            for (let key in this.promotion) {
                if (key === 'imageFile' || key === 'tablet_imageFile' || key === 'mobile_imageFile') {
                    formData.append(key, document.getElementById(key).files[0])
                } else {
                    formData.append(key, this.promotion[key])
                }
            }

            return formData
        },
        formatImageUrl(fileName) {
            return `/admin/img/promotions/${fileName}`
        }
    }
}
</script>

<style lang="scss" scoped>
.promotion{
    &__image{
        width: 100px;
        height: 100px;
    }
}
</style>