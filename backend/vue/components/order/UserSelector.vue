<template>
    <div class="user-info">
        <div class="user-info__user user mb-4">
            <div class="user__selected">
                <span class="mr-1 text-900">Пользователь:</span>
                <p v-if="isEmpty">Не выбран</p>
                <p v-else>{{order.user.lastName}} {{order.user.firstName}}</p>
            </div>
            <div class="user__type">
                <span class="mr-1 text-900">Тип плательщика:</span>
                <p v-if="isEmpty"> Не определено</p>
                <p v-else>
                    <span v-if="isLegal"> Юр. лицо</span>
                    <span v-else> Физ. лицо</span>
                </p>
            </div>
            <Button label="Изменить пользователя"
                    severity="warning"
                    :disabled="!isEditable"
                    @click="showForm"
            />
        </div>

        <div v-if="!isEmptyOrder">
            <div class="h6 mb-2">Получатель (если отличается от пользователя)</div>
            <div class="user-info__getter getter">
                <div class="flex flex-column flex-auto mr-4">
                    <label for="phone">Номер телефона</label>
                    <InputMask
                        id="phone"
                        v-model="order.subUser.subPhone"
                        date="phone"
                        mask="+7 (999) 999-9999"
                        placeholder="+7 (999) 999-9999"
                        :readonly="!isEditable"
                        @input="setSubUser('subPhone')"
                    />
                </div>
                <div class="flex flex-column flex-auto mr-4">
                    <label for="selectorLastName">Фамилия</label>
                    <InputText
                        id="selectorLastName"
                        type="text"
                        v-model="order.subUser.subLastName"
                        placeholder="Фамилия"
                        :readonly="!isEditable"
                        @input="setSubUser('subLastName')"
                    />
                </div>
                <div class="flex flex-column flex-auto">
                    <label for="selectorFirstName">Имя</label>
                    <InputText
                        id="selectorFirstName"
                        type="text"
                        v-model="order.subUser.subFirstName"
                        placeholder="Имя"
                        :readonly="!isEditable"
                        @input="setSubUser('subFirstName')"
                    />
                </div>
            </div>
        </div>
    </div>
    <Dialog
        v-model:visible="isVisible"
        :breakpoints="{ '960px': '75vw' }"
        :style="{ width: '60vw' }"
        :modal="true"
    >
        <user-search @selected="closeForm"/>
    </Dialog>

    <base-notice
        ref="notice"
    />
</template>

<script>
import UserSearch from "../users/UserSearch"
import BasePopup from "../base/BasePopup"
import BaseNotice from "../base/BaseNotice"
import {inject} from "vue"
import {IMaskDirective} from 'vue-imask'

export default {
    name: "UserSelector",
    components: {
        BasePopup,
        UserSearch,
        BaseNotice
    },
    directives: {
        imask: IMaskDirective
    },
    data() {
        return {
            isVisible: false,
            maskPhone: {
                mask: '+{7} (000) 000-00-00',
            },
        }
    },
    setup() {
        const order = inject('order')
        const setSubUserField = inject('setSubUserField')
        const isEditable = inject('isEditable')

        return {
            order,
            setSubUserField,
            isEditable
        }
    },
    computed: {
        isEmpty() {
            return _.isEmpty(this.order.user)
        },
        isEmptyOrder() {
            return _.isEmpty(this.order)
        },
        isLegal() {
            return this.order.user.profile[0].isLegal ? true : false
        }
    },
    methods: {
        showForm() {
            this.isVisible = true
        },
        closeForm() {
            this.isVisible = false
        },
        setSubUser(field) {
            this.setSubUserField(field, event.target.value)
        }
    }
}
</script>

<style lang="scss">
.user {
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    align-items: center;

    &__selected {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-between;
        align-items: center;
    }

    &__type {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-between;
        align-items: center;
    }
}

.getter {
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    align-items: center;
}
</style>