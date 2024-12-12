<template>
    <DataTable
        lazy
        paginator
        v-model:value="users"
        class="p-datatable-gridlines"
        :rows="15"
        :totalRecords="pagination.pageCount * 15"
        dataKey="id"
        :rowHover="true"
        filterDisplay="menu"
        :loading="loading"
        responsiveLayout="scroll"
        @page="changePage($event)"
        style="min-height: calc(100vh - 276px);"
    >
        <template #header>
            <div class="flex justify-content-between flex-column sm:flex-row">
                            <span class="p-input-icon-left mr-2">
                                <i class="pi pi-search"/>
                                <InputText
                                    v-model="search"
                                    placeholder="Поиск по id/ФИО/Телефону/E-mail"
                                />
                            </span>
                <Button
                    type="button"
                    label="Поиск"
                    class="p-button-outlined"
                    @click.prevent="getUsers"
                />
                <Button
                    class="ml-auto"
                    icon="pi pi-plus"
                    label="Создать пользователя"
                    severity="success"
                    @click.prevent="showForm"
                />
            </div>
        </template>
        <template v-if="!loading" #empty>Пользователи не найдены</template>
        <template #loading>
            <i class="pi pi-spin pi-spinner" style="font-size: 5rem"></i>
        </template>
        <Column style="min-width: 4rem">
            <template #body="{ data }">
                <Button severity="secondary" text rounded aria-label="Найстройки"
                        @click.prevent="getUser(data.id)" style="padding: 7px;">
                    <i class="pi pi-user-edit" style="font-size: 1.5rem;"/>
                </Button>
            </template>
        </Column>
        <Column field="id" header="ID" style="min-width: 5rem">
        </Column>
        <Column field="email" header="Почта" style="min-width: 16rem">
        </Column>
        <Column field="phone" header="Телефон" style="min-width: 11rem">
        </Column>
        <Column field="lastName" header="Фамилия" style="min-width: 10rem">
        </Column>
        <Column field="firstName" header="Имя" style="min-width: 10rem">
        </Column>
        <Column header="Тип плательщика" style="min-width: 9rem">
            <template #body="{ data }">
                {{ data.user_type === 4 ? 'Сотрудник' : 'Покупатель' }}
            </template>
        </Column>
        <Column field="date_create" header="Дата регистрации" style="min-width: 12rem">
        </Column>
        <Column field="date_update" header="Дата изменения" style="min-width: 12rem">
        </Column>
    </DataTable>

    <Dialog
        v-model:visible="isVisible"
        :breakpoints="{ '960px': '75vw' }"
        :style="{ width: '60vw' }"
        :modal="true"
        :dismissableMask="true"
    >
        <template #header>
            <div v-if="loadingForm"></div>
            <div v-else>
                <h5
                    v-if="user.id === 0"
                    class="m-0">
                    Карточка нового пользователя
                </h5>
                <h5
                    v-else
                    class="m-0">
                    Карточка пользователя {{ user.id }} (ID)
                </h5>
            </div>
        </template>
        <div v-if="!loadingForm">
            <user-form
                :user-data="user"
                @save-complete="userSaved"
                @save-error="userSaved"
            />
        </div>
        <div v-else>
            <div class="flex align-items-center justify-content-center" style="min-height: calc(100vh - 194px);">
                <i class="pi pi-spin pi-spinner" style="font-size: 5rem"></i>
            </div>
        </div>
    </Dialog>
    <base-notice
        ref="notice"
    />
</template>

<script>
import axios from "axios";
import {ref, provide, readonly} from "vue"
import UserForm from "./UserForm"
import BasePage from "../base/BasePage"
import BaseNotice from "../base/BaseNotice"

const emptyUser = {
    id: 0,
    email: '',
    phone: '',
    firstName: null,
    password: null,
    secondName: null,
    lastName: null,
    birthday: null,
    sex: null,
    isLegal: 0,
    payerSameGetter: true,
    profile: {
        payer: {
            legalForm: null,
            legalName: null,
            legalAddress: null,
            legalInn: null,
            legalKpp: null,
            legalCheckingAcc: null,
            legalBank: null,
            legalBik: null,
            legalCorAcc: null,
            legalBankBook: null,
            legalSignatoryPosition: null,
            legalSignatoryName: null,
            legalSignatoryBase: null,
        },
        getter: {
            legalForm: null,
            legalName: null,
            legalAddress: null,
            legalInn: null,
            legalKpp: null,
            legalCheckingAcc: null,
            legalBank: null,
            legalBik: null,
            legalCorAcc: null,
            legalBankBook: null,
            legalSignatoryPosition: null,
            legalSignatoryName: null,
            legalSignatoryBase: null,
        },
    },
    isEmployee: false,
    role: null
}

export default {
    name: "Users",
    components: {
        UserForm,
        BasePage,
        BaseNotice,
    },
    data() {
        return {
            users: [],
            pagination: [],
            isEmpty: true,
            page: 1,
            userPerPage: 20,
            isVisible: false,
            search: '',
            user: JSON.parse(JSON.stringify(emptyUser)),
            loading: false,
            loadingForm: false
        }
    },
    setup() {
        const roleList = ref([]);
        const getRoles = (role = null) => {
            axios
                .get('/admin/backend/role')
                .then(response => {
                    roleList.value = []

                    _.forEach(response.data, (item) => {
                        roleList.value.push({
                            value: item.name,
                            text: item.description,
                            selected: (!_.isNull(role) && role == item.name) ? true : false
                        })
                    })
                })
        }

        provide('roleList', readonly(roleList))
        provide('getRoles', getRoles)

        return {
            roleList,
            getRoles
        }
    },
    beforeMount() {
        if (!_.isEmpty(this.$route.query.page)) {
            this.page = this.$route.query.page
        }

        this.getUsers()
        this.getRoles()
    },
    methods: {
        showSuccess(message) {
            this.$toast.add({severity: 'success', summary: 'Успех', detail: message, life: 3000});
        },
        showError(message) {
            this.$toast.add({severity: 'error', summary: 'Ошибка', detail: message, life: 3000});
        },
        getUsers() {
            this.loading = true

            let url = '/admin/backend/user'

            url += this.search == '' ? '' : '?search=' + this.search

            axios
                .get(url, {
                    params: {
                        page: this.page
                    }
                })
                .then(response => {
                    if (response.data.code == 0 && !_.isEmpty(response.data.data.users)) {
                        this.users = [...response.data.data.users]
                        this.pagination = response.data.data.pagination
                        this.isEmpty = false
                    } else {
                        this.users = []
                    }
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                    this.showError('Список пользователей не получен')
                })
        },
        getUser(userId) {
            this.showForm()
            this.loadingForm = true

            let url = '/admin/backend/user'

            axios
                .get(url, {
                    params: {
                        id: userId
                    }
                })
                .then(response => {
                    if (response.data.code === 0 && !_.isEmpty(response.data.data.users)) {
                        this.prepareUserData(response.data.data.users[0])
                        this.getRoles(response.data.data.users[0].role)
                    } else {
                        this.loadingForm = false
                        this.closeForm()
                    }
                })
                .catch(error => {
                    this.loadingForm = false
                    this.closeForm()
                    throw new Error('Ошибка, ' + error )
                })
        },
        prepareUserData(data) {
            this.user = JSON.parse(JSON.stringify(emptyUser))
            this.user.id = data.id
            this.user.email = data.email
            this.user.phone = data.phone
            this.user.firstName = data.firstName
            this.user.secondName = data.secondName
            this.user.lastName = data.lastName
            this.user.isEmployee = data.isEmployee
            this.user.role = data.role

            let profiles = data.profile

            if (!_.isEmpty(profiles)) {
                this.user.isLegal = profiles[0].isLegal
                this.user.birthday = profiles[0].birthday
                this.user.sex = (profiles[0].sex === 1 || profiles[0].sex === 0)
                    ? profiles[0].sex
                    : null

                if (profiles[0].isLegal) {
                    if (profiles.length == 1) {
                        this.user.profile.payer = {...profiles[0]}
                        this.user.profile.gettter = {...profiles[0]}
                        this.user.payerSameGetter = 1
                    } else if (profiles.length == 2) {
                        this.user.profile.payer = profiles[0].isPayer == 1 ? {...profiles[0]} : {...profiles[1]}
                        this.user.profile.getter = profiles[0].isPayer == 0 ? {...profiles[0]} : {...profiles[1]}
                        this.user.payerSameGetter = 0
                    } else {
                        this.showError('Не удалось загрузить пользователя!')
                        this.$refs.notice.show(noticeType, noticeText)
                        this.closeForm()
                    }
                }
            }
            this.loadingForm = false;
        },
        showForm() {
            this.user = JSON.parse(JSON.stringify(emptyUser))
            this.isVisible = true
        },
        closeForm() {
            this.isVisible = false
            this.user = JSON.parse(JSON.stringify(emptyUser))
        },
        changePage(event) {
            this.page = ++event.page
            this.getUsers()
        },
        userSaved({status, error = ''}) {
            this.closeForm()

            if (status) {
                this.showSuccess('Пользователь  сохранен успешно!')
            } else {
                this.showError(`Не удалось сохранить пользователя ${error}`)
            }

            this.getUsers()
        },
    }
}
</script>

<style lang="scss"></style>