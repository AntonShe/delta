<template>
    <div class="form">
        <div class="form__field-container form__checkbox">
            <input
                v-model=banner.isActive            
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
                :value="banner.title"
                @input="update('title', $event)"
            />
        </div>
        <div class="form__field-container">
            <label for="text" class="form__label">
                Текст
            </label>
            <base-field
                name="text"
                id="text"
                type="textarea"
                placeholder="Текст"
                :value="banner.text"
                @input="update('text', $event)"
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
                <img v-if="banner.image" class="banner__image" alt="" :src="formatImageUrl(banner.image)">
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
                <img v-if="banner.tabletImage" class="banner__image" alt="" :src="formatImageUrl(banner.tabletImage)">
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
                <img v-if="banner.mobileImage" class="banner__image" alt="" :src="formatImageUrl(banner.mobileImage)">
            </div>
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
                :value="this.banner.sort ? this.banner.sort.toString() : ''"
                @input="update('sort', $event)"
                :mask="{ mask: /^(?!(0))\d+$/ }"
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
                :value="this.banner.startDate"
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
                :value="this.banner.endDate"
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
                :value="this.banner.link || ''"
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
    name: "BannerForm",
    emits: ['create', 'update', 'delete'],
    props: {
        data: {
            type: Object,
            default: () => ({
                image: '',
                tablet_image: '',
                mobile_image: '',
                title: '',
                text: '',
                link: '',
                sort: '',
                startDate: '',
                endDate: '',
                isActive: true
            })
        }
    },
    data() {
        return {
            banner: {...this.data, isActive: !!this.data.isActive},
            showPreloader: false,
            hasChanges: false
        }
    },
    computed: {
        isEdit() {
            return this.banner.hasOwnProperty('id')
        },
    },
    watch: {
        data(value) {
            this.banner = value
            this.hasChanges = false
        },
    },
    methods: {
        update(prop, e) {
            this.hasChanges = true
            this.banner[prop] = e.target.value
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

            this.banner.isActive = Number(this.banner.isActive)

            for (let key in this.banner) {
                if (key === 'imageFile' || key === 'tablet_imageFile' || key === 'mobile_imageFile') {
                    formData.append(key, document.getElementById(key).files[0])
                } else {
                    formData.append(key, this.banner[key])
                }
            }

            return formData
        },
        formatImageUrl(fileName) {
            return `/admin/img/banner/${fileName}`
        }
    }
}
</script>

<style lang="scss" scoped>
.banner{
    &__image{
        width: 100px;
        height: 100px;
    }
}
</style>