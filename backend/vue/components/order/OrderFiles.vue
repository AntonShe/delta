<template>
    <div class="files">
        <Button
            label="Документы для печати"
            @click="() => {this.isShow = !this.isShow}"
        />

        <div class="dock-list" v-if="isShow">
            <Dropdown v-model="fileType" :options="selected" optionLabel="name" class="w-full md:w-14rem" />
            <a class="dock-list__btn btn" target="_blank" :href="getUrl">Показать</a>
        </div>
    </div>
</template>

<script>
import {inject} from "vue"

export default {
    name: "OrderFiles",
    data() {
        return {
            isShow: false,
            fileType: {
                name: '',
                value: 0
            },
            url: '#',
            selected: [
                { name: 'Спецификация', value: '1' },
                { name: 'ИНН/ОГРН', value: '2' },
                { name: 'Устав', value: '3' }
            ]
        }
    },
    setup() {
        const order = inject('order')

        return {
            order
        }
    },
    computed: {
        isOrderExist() {
            return this.order.id > 0
        },
        getUrl() {
            let url = '#'

            switch (+this.fileType.value) {
                case 1:
                    url = '/order/get-specifications?token=' + this.getToken()
                    break
                case 2:
                    url = '/file/INN_OGRN_DELTABOOK.pdf'
                    break
                case 3:
                    url = '/file/Ustav_Deltabook.pdf'
                    break
            }

            return url
        }
    },
    methods: {
        getToken() {
            let idOrder = this.order.id + ''
            let middle = parseInt(Date.now() / 1000)

            return idOrder + 'o' + (middle * this.order.id)
        }
    }
}
</script>

<style lang="scss">
.files {

    &__btn {
        width: 100%;
        margin-bottom: 15px;
    }
}

.dock-list {
    &__btn {
        width: 100%;
        margin-top: 15px;
    }
}
</style>