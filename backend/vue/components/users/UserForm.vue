<template>
    <form class="user-create-form">
        <div class="flex flex-wrap gap-4">
            <div class="user-create-form__item">
                <label for="lastName" class="text-900">Фамилия</label>
                <input
                    v-model="user.lastName"
                    @change="validateUpdatedData()"
                    type="text"
                    class="p-inputtext p-component"
                    id="lastName"
                    name="lastName">
            </div>
            <div class="user-create-form__item">
                <label for="firstName" class="text-900">Имя</label>
                <input
                    v-model="user.firstName"
                    @change="validateUpdatedData()"
                    type="text"
                    class="p-inputtext p-component"
                    id="firstName"
                    name="firstName">
            </div>
            <div class="user-create-form__item">
                <label for="secondName" class="text-900">Отчество</label>
                <input
                    v-model="user.secondName"
                    @change="validateUpdatedData()"
                    type="text"
                    class="p-inputtext p-component"
                    id="secondName"
                    name="secondName">
            </div>
            <div class="user-create-form__item">
                <label for="email" class="text-900">Почта</label>
                <input
                    v-imask="maskEmail"
                    :value="user.email"
                    @accept="onChange($event, 'email')"
                    @complete="onChange($event, 'email')"
                    type="text"
                    class="p-inputtext p-component"
                    id="email"
                    name="email">
                <!--            <button
                                @click.prevent=""
                                class="field__btn">Изменить/добавить почту</button>-->
                <div
                    v-if="errors.mail.status"
                    class="field__hint">{{ errors.mail.text }}
                </div>
            </div>
            <div class="user-create-form__item">
                <label for="password" class="text-900">Пароль</label>
                <input
                    v-model="user.password"
                    @change="validateUpdatedData()"
                    type="password"
                    class="p-inputtext p-component"
                    id="password"
                    name="password">
            </div>
            <div class="user-create-form__item">
                <label for="phone" class="text-900">Телефон</label>
                <input
                    v-imask="maskPhone"
                    :value="user.phone"
                    @accept="onChange($event, 'phone')"
                    @complete="onChange($event, 'phone')"
                    type="text"
                    class="p-inputtext p-component"
                    id="phone"
                    name="phone">
                <!--            <button
                                @click.prevent=""
                                class="field__btn">Изменить/добавить телефон</button>-->
                <div
                    v-if="errors.phone.status"
                    class="field__hint">{{ errors.phone.text }}
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-4 mt-4">
            <div class="user-create-form__item">
                <label for="birthday" class="text-900">День рождения</label>

                <Calendar
                    v-model="user.birthday"
                    :showIcon="true"
                    id="birthday"
                    name="birthday"
                    dateFormat="dd.mm.yy"
                    @change="validateUpdatedData()"
                />
            </div>

            <div class="user-create-form__item">
                <base-radio-group
                    :list="radioListUserSex"
                    :default-value="user.sex"
                    main-label="Пол"
                    additional-class="user-form-radios"
                    input-name="sexChoice"
                    @onChanged="updateSex"
                />
            </div>
        </div>
        <div class="flex flex-wrap mt-4">
            <div class="user-create-form__item w-100">
                <base-radio-group
                    :list="radioListUserLegal"
                    :default-value="user.isLegal"
                    main-label="Тип плательщика"
                    additional-class="user-form-radios"
                    input-name="isLegal"
                    @onChanged="updateLegal"
                />
            </div>
        </div>
        <!--Поля для юр. лиц-->
        <div
            v-if="isLegal"
            class="flex flex-wrap gap-4 mt-4">

            <div class="user-create-form__item user-create-form__item-payerSameGetter">
                <label for="payerSameGetter">
                    Организация-грузополучатель отличается от плательщика
                </label>
                <Checkbox
                    class=""
                    id="payerSameGetter"
                    v-model="this.isPayerSameGetter"
                    :binary="true"
                    @change="updateIsPayerSameGetter($event)"
                />
            </div>
                <!--Плательщик-->
                <div class="user-create-form__col">
                    <p
                        v-if="user.payerSameGetter"
                        class="user-create-form__title user-create-form__title">Данные Плательщика/Получателя</p>
                    <p
                        v-else
                        class="user-create-form__title">Данные Плательщика</p>

                    <div class="user-create-form__item">
                        <label for="legalForm">Форма юр. лица</label>

                        <input
                            v-model="user.profile.payer.legalForm"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalForm"
                            name="legalForm">
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalName">Название организации*</label>

                        <input
                            v-model="user.profile.payer.legalName"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalName"
                            name="legalName">

                        <div
                            v-if="errors.payer.legalName.status"
                            class="field__hint">{{ errors.payer.legalName.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalInn">ИНН*</label>

                        <input
                            v-imask="maskInn"
                            :value="user.profile.payer.legalInn"
                            @accept="onChange($event, 'legalInn', 'payer')"
                            @complete="onChange($event, 'legalInn', 'payer')"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalInn"
                            name="legalInn">

                        <div
                            v-if="errors.payer.legalInn.status"
                            class="field__hint">{{ errors.payer.legalInn.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalKpp">КПП*</label>

                        <input
                            v-imask="maskKpp"
                            :value="user.profile.payer.legalKpp"
                            @accept="onChange($event, 'legalKpp', 'payer')"
                            @complete="onChange($event, 'legalKpp', 'payer')"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalKpp"
                            name="legalKpp">

                        <div
                            v-if="errors.payer.legalKpp.status"
                            class="field__hint">{{ errors.payer.legalKpp.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalAddress">Юридический адрес*</label>

                        <input
                            v-model="user.profile.payer.legalAddress"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalAddress"
                            name="legalAddress">

                        <div
                            v-if="errors.payer.legalAddress.status"
                            class="field__hint">{{ errors.payer.legalAddress.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalCheckingAcc">Расчетный счет*</label>

                        <input
                            v-model="user.profile.payer.legalCheckingAcc"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalCheckingAcc"
                            name="legalCheckingAcc">

                        <div
                            v-if="errors.payer.legalCheckingAcc.status"
                            class="field__hint">{{ errors.payer.legalCheckingAcc.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalBank">Наименование банка*</label>

                        <input
                            v-model="user.profile.payer.legalBank"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalBank"
                            name="legalBank">

                        <div
                            v-if="errors.payer.legalBank.status"
                            class="field__hint">{{ errors.payer.legalBank.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalBik">БИК*</label>

                        <input
                            v-model="user.profile.payer.legalBik"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalBik"
                            name="legalBik">

                        <div
                            v-if="errors.payer.legalBik.status"
                            class="field__hint">{{ errors.payer.legalBik.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalCorAcc">Кор. счет</label>

                        <input
                            v-model="user.profile.payer.legalCorAcc"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalCorAcc"
                            name="legalCorAcc">
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalBankBook">Лицевой счет</label>

                        <input
                            v-model="user.profile.payer.legalBankBook"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalBankBook"
                            name="legalBankBook">
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalSignatoryPosition">Должность подписанта</label>

                        <input
                            v-model="user.profile.payer.legalSignatoryPosition"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalSignatoryPosition"
                            name="legalSignatoryPosition">
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalSignatoryName">ФИО подписанта</label>

                        <input
                            v-model="user.profile.payer.legalSignatoryName"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalSignatoryName"
                            name="legalSignatoryName">
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalSignatoryBase">Действует на основании</label>

                        <input
                            v-model="user.profile.payer.legalSignatoryBase"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalSignatoryBase"
                            name="legalSignatoryBase">
                    </div>
                </div>

                <!--Получатель-->
                <div
                    v-if="!user.payerSameGetter"
                    class="user-create-form__col">
                    <p
                        v-if="user.payerSameGetter"
                        class="user-create-form__title user-create-form__title"></p>
                    <p
                        v-else
                        class="user-create-form__title">Данные Получателя</p>

                    <div class="user-create-form__item">
                        <label for="legalForm">Форма юр. лица</label>

                        <input
                            v-model="user.profile.getter.legalForm"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalForm"
                            name="legalForm">
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalName">Название организации*</label>

                        <input
                            v-model="user.profile.getter.legalName"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalName"
                            name="legalName">

                        <div
                            v-if="errors.getter.legalName.status"
                            class="field__hint">{{ errors.getter.legalName.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalInn">ИНН*</label>

                        <input
                            v-imask="maskInn"
                            :value="user.profile.getter.legalInn"
                            @accept="onChange($event, 'legalInn', 'getter')"
                            @complete="onChange($event, 'legalInn', 'getter')"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalInn"
                            name="legalInn">

                        <div
                            v-if="errors.getter.legalInn.status"
                            class="field__hint">{{ errors.getter.legalInn.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalKpp">КПП*</label>

                        <input
                            v-imask="maskKpp"
                            :value="user.profile.getter.legalKpp"
                            @accept="onChange($event, 'legalKpp', 'getter')"
                            @complete="onChange($event, 'legalKpp', 'getter')"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalKpp"
                            name="legalKpp">

                        <div
                            v-if="errors.getter.legalKpp.status"
                            class="field__hint">{{ errors.getter.legalKpp.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalAddress">Юридический адрес*</label>

                        <input
                            v-model="user.profile.getter.legalAddress"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalAddress"
                            name="legalAddress">

                        <div
                            v-if="errors.getter.legalAddress.status"
                            class="field__hint">{{ errors.getter.legalAddress.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalCheckingAcc">Расчетный счет*</label>

                        <input
                            v-model="user.profile.getter.legalCheckingAcc"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalCheckingAcc"
                            name="legalCheckingAcc">

                        <div
                            v-if="errors.getter.legalCheckingAcc.status"
                            class="field__hint">{{ errors.getter.legalCheckingAcc.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalBank">Наименование банка*</label>

                        <input
                            v-model="user.profile.getter.legalBank"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalBank"
                            name="legalBank">

                        <div
                            v-if="errors.getter.legalBank.status"
                            class="field__hint">{{ errors.getter.legalBank.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalBik">БИК*</label>

                        <input
                            v-model="user.profile.getter.legalBik"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalBik"
                            name="legalBik">

                        <div
                            v-if="errors.getter.legalBik.status"
                            class="field__hint">{{ errors.getter.legalBik.text }}
                        </div>
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalCorAcc">Кор. счет</label>

                        <input
                            v-model="user.profile.getter.legalCorAcc"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalCorAcc"
                            name="legalCorAcc">
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalBankBook">Лицевой счет</label>

                        <input
                            v-model="user.profile.getter.legalBankBook"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalBankBook"
                            name="legalBankBook">
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalSignatoryPosition">Должность подписанта</label>

                        <input
                            v-model="user.profile.getter.legalSignatoryPosition"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalSignatoryPosition"
                            name="legalSignatoryPosition">
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalSignatoryName">ФИО подписанта</label>

                        <input
                            v-model="user.profile.getter.legalSignatoryName"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalSignatoryName"
                            name="legalSignatoryName">
                    </div>

                    <div class="user-create-form__item">
                        <label for="legalSignatoryBase">Действует на основании</label>

                        <input
                            v-model="user.profile.getter.legalSignatoryBase"
                            @change="validateUpdatedData()"
                            type="text"
                            class="p-inputtext p-component"
                            id="legalSignatoryBase"
                            name="legalSignatoryBase">
                    </div>
                </div>
        </div>
        <div class="user-create-form__wrapper">
            <div class="user-create-form__field field">
                <base-radio-group
                    :list="radioListUserEmployee"
                    :default-value="+user.isEmployee"
                    main-label="Уровень доступа"
                    additional-class="user-form-radios"
                    input-name="isEmployee"
                    @onChanged="updateEmployee"
                />
            </div>

            <div class="user-create-form__field field field_select">
                <base-select
                    v-if="+user.isEmployee"
                    :options="roleList"
                    @change="setNewRole"
                />
            </div>
        </div>
        <div class="flex justify-content-end">
            <Button
                label="Сохранить"
                :disabled="!isValid"
                @click.prevent="submitUser"
            />
        </div>
    </form>
</template>

<script>
import axios from "axios"
import BaseRadioGroup from "../base/BaseRadioGroup"
import BaseSelect from "../base/BaseSelect"
import {IMaskDirective} from 'vue-imask'
import {inject} from "vue"

export default {
    name: 'UserCreateForm',
    components: {
        BaseSelect,
        BaseRadioGroup
    },
    directives: {
        imask: IMaskDirective
    },
    props: {
        userData: {
            type: Object,
            require: true
        }
    },
    data() {
        return {
            user: {...this.userData},
            isPayerSameGetter: !this.userData.payerSameGetter,
            isValid: false,
            maskEmail: {
                mask: /[0-9a-zA-Z!@#$%&'*+\-/=?^_`{|}~]+/,
            },
            maskPhone: {
                mask: '+{7} (000) 000-00-00',
            },
            maskInn: {
                mask: '000000000000',
            },
            maskKpp: {
                mask: '000000000',
            },
            errors: {
                mail: {
                    status: false,
                    text: 'Некорректно заполнен телефон или почта!'
                },
                phone: {
                    status: false,
                    text: 'Некорректно заполнен телефон или почта!'
                },
                password: {
                    status: false,
                    text: 'Некорректно заполнен пароль'
                },
                payer: {
                    legalName: {
                        status: false,
                        text: 'Введите Название организации'
                    },
                    legalInn: {
                        status: false,
                        text: 'ИНН заполнен не верно'
                    },
                    legalKpp: {
                        status: false,
                        text: 'Введите КПП'
                    },
                    legalAddress: {
                        status: false,
                        text: 'Введите Юридический адрес'
                    },
                    legalCheckingAcc: {
                        status: false,
                        text: 'Введите Расчетный счет'
                    },
                    legalBank: {
                        status: false,
                        text: 'Введите Название банка'
                    },
                    legalBik: {
                        status: false,
                        text: 'Введите БИК'
                    }
                },
                getter: {
                    legalName: {
                        status: false,
                        text: 'Введите Название организации'
                    },
                    legalInn: {
                        status: false,
                        text: 'ИНН заполнен не верно'
                    },
                    legalKpp: {
                        status: false,
                        text: 'Введите КПП'
                    },
                    legalAddress: {
                        status: false,
                        text: 'Введите Юридический адрес'
                    },
                    legalCheckingAcc: {
                        status: false,
                        text: 'Введите Расчетный счет'
                    },
                    legalBank: {
                        status: false,
                        text: 'Введите Название банка'
                    },
                    legalBik: {
                        status: false,
                        text: 'Введите БИК'
                    }
                }
            }
        }
    },
    setup() {
        const roleList = inject('roleList')
        const getRoles = inject('getRoles')

        return {
            roleList,
            getRoles
        }
    },
    computed: {
        isEmpty() {
            return this.userData.id == 0
        },
        radioListUserSex() {
            return [
                {
                    label: 'Мужчина',
                    value: 1,
                    id: 'sexM'
                },
                {
                    label: 'Женщина',
                    value: 0,
                    id: 'sexF'
                }
            ]
        },
        radioListUserLegal() {
            return [
                {
                    label: 'Физическое лицо',
                    value: 0,
                    id: 'noLegal'
                },
                {
                    label: 'Юридическое лицо',
                    value: 1,
                    id: 'legal'
                }
            ]
        },
        radioListUserEmployee() {
            return [
                {
                    label: 'Клиент',
                    value: 0,
                    id: 'buyer'
                },
                {
                    label: 'Админ',
                    value: 1,
                    id: 'amdin'
                }
            ]
        },
        isLegal() {
            return this.user.isLegal == 1
        }
    },
    emits: ['saveComplete', 'saveError'],
    methods: {
        updateSex(data) {
            this.user.sex = data
            this.validateUpdatedData()
        },
        updateLegal(data) {
            this.user.isLegal = data
            this.validateUpdatedData()
        },
        updateEmployee(data) {
            this.user.isEmployee = data
            this.validateUpdatedData()
        },
        updateIsPayerSameGetter() {
            this.user.payerSameGetter = !this.isPayerSameGetter
            this.validateUpdatedData()
        },
        onChange(e, field, profile = null) {
            if (_.isNull(profile)) {
                this.user[field] = e.detail.value
            } else {
                this.user.profile[profile][field] = e.detail.value
            }

            this.validateUpdatedData()
        },
        setNewRole(value) {
            this.user.role = value.data.selectedValue;
            this.isValid = this.validateData()
        },
        validateUpdatedData() {
            this.isValid = this.validateData()
        },
        validateMail() {
            let mailVal = this.user.email.match(
                /[0-9a-zA-Z!@#$%&'*+\-\/=?^_`{|}~]+@[a-zA-Z0-9\._-]+\.[a-zA-Z0-9_-]+/
            )

            return !_.isNull(mailVal)
        },
        validatePhone() {
            let phoneVal = this.user.phone.match(
                /\+7\s\([0-9]{3}\)\s[0-9]{3}-[0-9]{2}-[0-9]{2}/
            )

            return !_.isNull(phoneVal)
        },
        validateInn(value) {
            let valLength = value.length

            switch (valLength) {
                case 10:
                    let numSum = (2 * parseInt(value[0]))
                        + (4 * parseInt(value[1]))
                        + (10 * parseInt(value[2]))
                        + (3 * parseInt(value[3]))
                        + (5 * parseInt(value[4]))
                        + (9 * parseInt(value[5]))
                        + (4 * parseInt(value[6]))
                        + (6 * parseInt(value[7]))
                        + (8 * parseInt(value[8]))

                    let valDiv = (numSum % 11).toString()

                    return value[9] == valDiv[valDiv.length - 1]
                    break
                case 12:
                    let firstControlSum = (7 * parseInt(value[0]))
                        + (2 * parseInt(value[1]))
                        + (4 * parseInt(value[2]))
                        + (10 * parseInt(value[3]))
                        + (3 * parseInt(value[4]))
                        + (5 * parseInt(value[5]))
                        + (9 * parseInt(value[6]))
                        + (4 * parseInt(value[7]))
                        + (6 * parseInt(value[8]))
                        + (8 * parseInt(value[9]))

                    let firstDiv = (firstControlSum % 11).toString()

                    let secondControlSum = (3 * parseInt(value[0]))
                        + (7 * parseInt(value[1]))
                        + (2 * parseInt(value[2]))
                        + (4 * parseInt(value[3]))
                        + (10 * parseInt(value[4]))
                        + (3 * parseInt(value[5]))
                        + (5 * parseInt(value[6]))
                        + (9 * parseInt(value[7]))
                        + (4 * parseInt(value[8]))
                        + (6 * parseInt(value[9]))
                        + (8 * parseInt(value[10]))

                    let secondDiv = (secondControlSum % 11).toString()

                    return (
                        value[10] == firstDiv[firstDiv.length - 1]
                        && value[11] == secondDiv[secondDiv.length - 1]
                    )
                    break
                default:
                    return false
                    break
            }


        },
        validateKpp(value) {
            return value.length == 9
        },
        validateData() {
            let isMailValid = false
            let isPhoneValid = false
            let isPasswordValid = false
            let isPayerLegalValid = true
            let isGetterLegalValid = true

            //Достаточно заполнить email ил телефон
            isMailValid = this.validateMail()

            if (isMailValid && _.isEmpty(this.user.phone)) {
                isPhoneValid = true
            } else {
                isPhoneValid = _.isEmpty(this.user.phone) ? false : this.validatePhone()
            }

            if (isPhoneValid && this.user.phone != '') isMailValid = isMailValid || this.user.email == ''

            if (
                (this.user.id == 0 && !_.isEmpty(this.user.password))
                || this.user.id != 0
            ) {
                isPasswordValid = true
            } else {
                isPasswordValid = false
            }

            this.errors.mail.status = !isMailValid
            this.errors.phone.status = !isPhoneValid
            this.errors.password.status = !isPasswordValid


            if (this.user.isLegal == 1) {
                let isLegalNameValid = this.user.profile.payer.legalName != ''
                let isLegalInnValid = this.validateInn(this.user.profile.payer.legalInn)
                let isLegalKppValid = this.validateKpp(this.user.profile.payer.legalKpp)
                let isLegalAddressValid = this.user.profile.payer.legalAddress != ''
                let isLegalCheckingAccValid = this.user.profile.payer.legalCheckingAcc != ''
                let isLegalBankValid = this.user.profile.payer.legalBank != ''
                let isLegalBikValid = this.user.profile.payer.legalBik != ''

                this.errors.payer.legalName.status = !isLegalNameValid
                this.errors.payer.legalInn.status = !isLegalInnValid
                this.errors.payer.legalKpp.status = !isLegalKppValid
                this.errors.payer.legalAddress.status = !isLegalAddressValid
                this.errors.payer.legalCheckingAcc.status = !isLegalCheckingAccValid
                this.errors.payer.legalBank.status = !isLegalBankValid
                this.errors.payer.legalBik.status = !isLegalBikValid

                isPayerLegalValid = isLegalNameValid
                    && isLegalInnValid
                    && isLegalKppValid
                    && isLegalAddressValid
                    && isLegalCheckingAccValid
                    && isLegalBankValid
                    && isLegalBikValid

                if (!this.user.payerSameGetter) {
                    let isGetterLegalNameValid = this.user.profile.getter.legalName != ''
                    let isGetterLegalInnValid = this.validateInn(this.user.profile.getter.legalInn)
                    let isGetterLegalKppValid = this.validateKpp(this.user.profile.getter.legalKpp)
                    let isGetterLegalAddressValid = this.user.profile.getter.legalAddress != ''
                    let isGetterLegalCheckingAccValid = this.user.profile.getter.legalCheckingAcc != ''
                    let isGetterLegalBankValid = this.user.profile.getter.legalBank != ''
                    let isGetterLegalBikValid = this.user.profile.getter.legalBik != ''

                    this.errors.getter.legalName.status = !isGetterLegalNameValid
                    this.errors.getter.legalInn.status = !isGetterLegalInnValid
                    this.errors.getter.legalKpp.status = !isGetterLegalKppValid
                    this.errors.getter.legalAddress.status = !isGetterLegalAddressValid
                    this.errors.getter.legalCheckingAcc.status = !isGetterLegalCheckingAccValid
                    this.errors.getter.legalBank.status = !isGetterLegalBankValid
                    this.errors.getter.legalBik.status = !isGetterLegalBikValid

                    isGetterLegalValid = isGetterLegalNameValid
                        && isGetterLegalInnValid
                        && isGetterLegalKppValid
                        && isGetterLegalAddressValid
                        && isGetterLegalCheckingAccValid
                        && isGetterLegalBankValid
                        && isGetterLegalBikValid
                }

            } else {
                this.errors.payer.legalName.status = false
                this.errors.getter.legalName.status = false
                this.errors.payer.legalInn.status = false
                this.errors.getter.legalInn.status = false
                this.errors.payer.legalKpp.status = false
                this.errors.getter.legalKpp.status = false
                this.errors.payer.legalAddress.status = false
                this.errors.getter.legalAddress.status = false
                this.errors.payer.legalCheckingAcc.status = false
                this.errors.getter.legalCheckingAcc.status = false
                this.errors.payer.legalBank.status = false
                this.errors.getter.legalBank.status = false
                this.errors.payer.legalBik.status = false
                this.errors.getter.legalBik.status = false
            }

            return isMailValid && isPhoneValid && isPasswordValid && isPayerLegalValid && isGetterLegalValid

        },
        submitUser() {
            if (this.validateData()) {
                if (this.user.id === 0) {
                    this.createUser()
                } else {
                    this.updateUser()
                }
            } else {
                this.isValid = false
            }
        },
        createUser() {
            // TODO: переписать два метода в один
            const birthdayDate = new Date(this.user.birthday)
            const birthdayString = this.user.birthday ? `${birthdayDate.getFullYear()}-${birthdayDate.getMonth() + 1}-${birthdayDate.getDate()}` : null

            axios
                .post('/admin/backend/user', {
                    ...this.user,
                    birthday: birthdayString
                })
                .then(response => {
                    if (response.data.status == 1) {
                        this.$emit('saveComplete', {status: true})
                    } else {
                        this.$emit('saveError', {status: false, error: response.data.errors ? response.data.errors.join('\n') : ''})
                    }
                })
                .catch(error => {
                    throw new Error('Ошибка, ' + error )
                })
        },
        updateUser() {
            const birthdayDate = new Date(this.user.birthday)
            const birthdayString = this.user.birthday ? `${birthdayDate.getFullYear()}-${birthdayDate.getMonth() + 1}-${birthdayDate.getDate()}` : null

            axios
                .patch('/admin/backend/user', {
                    ...this.user,
                    birthday: birthdayString
                })
                .then(response => {
                    if (response.data.status == 1) {
                        this.$emit('saveComplete', {status: true})
                    } else {
                        this.$emit('saveError', {status: false, error: response.data.errors ? response.data.errors.join('\n') : ''})
                    }
                })
                .catch(error => {
                    throw new Error('Ошибка, ' + error )
                })
        }
    }
}
</script>

<style lang="scss">
.user-create-form {
    &__title {
        text-align: center;
        font-weight: bold;
    }

    &__field {
        margin: 20px 0;
    }

    &__wrapper {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
    }

    &__col {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        gap: 20px;
        width: calc(50% - 1.5rem);

        & .user-create-form__item {
            width: 100%;
        }
    }

    &__item {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        gap: 5px;
        width: calc(50% - 1.5rem);

        & > input {
            flex-grow: 1;
            width: 100%;
        }

        &-payerSameGetter {
            flex-direction: row;
            width: 100%;
        }
    }

    .p-calendar-w-btn .p-inputtext {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .p-calendar-w-btn .p-datepicker-trigger {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

}

.field {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-start;
    position: relative;

    &_payerSameGetter {
        width: 100%;

        &__label {
            max-width: unset !important;
            width: unset !important;
        }

        &__input {
            width: unset !important;
        }
    }

    &_nowrap {
        flex-wrap: nowrap;
    }

    &_select {
        margin-left: 30px;
    }

    &__label {
        margin-right: 20px;
        max-width: 120px;
        width: 100%;
    }

    &__input {
        width: 100%;
        max-width: 300px;
        margin-right: 20px;
    }

    &__hint {
        position: absolute;
        top: 100%;
        left: 140px;
        font-size: 14px;
        color: red;
    }
}

.user-form-radios {

    .base-radio-group__list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }
}

</style>