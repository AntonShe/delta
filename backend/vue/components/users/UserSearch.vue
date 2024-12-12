<template>
    <div class="search-user">
        <DataTable
            lazy
            paginator
            v-model:value="users"
            class="p-datatable-gridlines"
            dataKey="index"
            :rows="15"
            :rowHover="true"
            filterDisplay="menu"
            :loading="loading"
            responsiveLayout="scroll"
            @row-click="(event) => setUser(event.index)"
            style="min-height: calc(100vh - 186px);"
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
                </div>
            </template>
            <template v-if="!loading" #empty>Пользователи не найдены</template>
            <template #loading>
                <i class="pi pi-spin pi-spinner" style="font-size: 5rem"></i>
            </template>
            <Column field="id" header="ID" style="min-width: 5rem">
            </Column>
            <Column field="email" header="Почта" style="min-width: 14rem">
            </Column>
            <Column field="phone" header="Телефон" style="min-width: 11rem">
            </Column>
            <Column field="lastName" header="Фамилия" style="min-width: 10rem">
            </Column>
            <Column field="firstName" header="Имя" style="min-width: 10rem">
            </Column>
            <Column header="Тип плательщика" style="min-width: 9rem">
                <template #body="{ data }">
                    {{ getType(data.profile[0].isLegal) }}
                </template>
            </Column>
        </DataTable>
    </div>
</template>

<script>
import axios from "axios"
import {inject} from "vue";

export default {
    name: "UserSearch",
    data() {
        return {
            users: [],
            search: '',
            loading: false
        }
    },
    setup() {
        const setUser = inject('setUser')

        return {
            setUser
        }
    },
    emits: ['selected'],
    computed: {
        isEmpty() {
            return _.isEmpty(this.users)
        }
    },
    methods: {
        getUsers() {
            if (!_.isEmpty(this.search)) {
                this.loading = true;
                axios
                    .get('/admin/backend/user', {
                        params: {
                            search: this.search
                        }
                    })
                    .then(response => {
                        if (!_.isEmpty(response.data.data.users)) {
                            this.users = response.data.data.users
                        }
                        this.loading = false;
                    })
            }
        },
        getType(isLegal) {
            return isLegal ? 'Юр. лицо' : 'Физ. лицо'
        },
        setUser(index) {
            this.setUser(this.users[index])
            this.$emit('selected')
        }
    }
}
</script>

<style lang="scss">
.list {
    width: 100%;
    margin: 0 20px;
}

.row {
    &_body {
        transition: .3s;
        border: 1px solid black;
        cursor: pointer;
        margin-bottom: 5px;

        &:hover {
            box-shadow: -2px 2px 5px 1px rgba(0, 0, 0, .5);
            border: 1px solid transparent;
        }
    }
}
</style>