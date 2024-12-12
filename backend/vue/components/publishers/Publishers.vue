<template>
  <DataTable
    lazy
    paginator
    v-model:value="publishers"
    class="p-datatable-gridlines"
    :rows="40"
    :totalRecords="pagination.pageCount * 40"
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
            placeholder="Поиск по id/Названию"
          />
        </span>
        <Button
          type="button"
          label="Поиск"
          class="p-button-outlined"
          @click.prevent="getPublishers"
        />
        <Button
          label="Добавить издательство"        
          class="ml-auto"
          icon="pi pi-plus"
          severity="success"
          @click.prevent="newPublisher"
        />
      </div>
    </template>
    <template v-if="isEmpty" #empty>Издательства не найдены</template>
    <template #loading>
      <i class="pi pi-spin pi-spinner" style="font-size: 5rem"></i>
    </template>
    <Column style="min-width: 4rem; width: 4rem">
      <template #body="{ data }">
        <Button
          severity="secondary"
          text
          rounded
          aria-label="Найстройки"
          @click.prevent="getPublisher(data.id)"
          style="padding: 7px;"
        >
          <i class="pi pi-user-edit" style="font-size: 1.5rem;"/>
        </Button>
      </template>
    </Column>
    <Column field="id" header="ID издательства" style="min-width: 4rem; width: 8rem" />
    <Column field="name" header="Название" style="min-width: 10rem" />
    <Column header="Активность" style="min-width: 4rem; width: 4rem">
      <template #body="{ data }">
        {{ data.isActive ? 'Да' : 'Нет' }}
      </template>
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
          v-if="publisherData.id === 0"
          class="m-0">
          Карточка нового издательства
        </h5>
        <h5
          v-else
          class="m-0">
          Карточка издательства {{ publisherData.id }} (ID)
        </h5>
      </div>
    </template>
    <div v-if="!loadingForm">
      <PublisherForm
        :publisher-data="publisherData"
        @save-complete="publisherSaved"
        @save-error="publisherSaved"
        @close-publisher-form="closeForm"
        @save-temp-data="saveTempData"
      />
    </div>
    <div v-else>
      <div class="flex align-items-center justify-content-center" style="min-height: calc(100vh - 194px);">
        <i class="pi pi-spin pi-spinner" style="font-size: 5rem"></i>
      </div>
    </div>
  </Dialog>
</template>
  
<script>
import axios from 'axios'
import PublisherForm from './PublisherForm.vue'

export default {
  name: 'Publishers',
  components: {
    PublisherForm
  },
  data() {
    return {
      publishers: [],
      loading: false,
      loadingForm: false,      
      page: 1,
      pagination: [],
      isEmpty: true,
      search: '',
      isVisible: false,
      publisherData: {},
      publisherDataTemplate: {
        id: 0,
        name: "",
        isActive: 1,
        description: "",
        cover: "",
        labirintId: 0,
        seoTitle: "",
        seoMetaKeywords: "",
        seoMetaDescription: ""
      },
      isLastEventNew: false
    }
  },
  beforeMount() {
    if (!_.isEmpty(this.$route.query.page)) {
      this.page = this.$route.query.page
    }

    this.publisherData = {...this.publisherDataTemplate}
    this.getPublishers()
  },
  methods: {
    showSuccess(message) {
      this.$toast.add({severity: 'success', summary: 'Успех', detail: message, life: 3000});
    },
    showError(message) {
      this.$toast.add({severity: 'error', summary: 'Ошибка', detail: message, life: 3000});
    },
    getPublishers() {
      let url = '/admin/backend/publisher'
      const str = this.search.replace(/&/g, "%26").replace(/\+/g, "%2B")

      url += this.search == '' ? '' : '?search=' + str
      this.loading = true

      axios
        .get(url, {
          params: {
            page: this.page
          }
        })
        .then(response => {
          if (!_.isEmpty(response.data.data.publishers)) {
            this.publishers = [...response.data.data.publishers]
            this.pagination = response.data.data.pagination
            this.isEmpty = false
          } else {
            this.publishers = []
          }
          this.loading = false
        })
        .catch(error => {
          this.loading = false
          this.showError('Список персон не получен, ' + error)
        })
    },
    changePage(event) {
      this.page = ++event.page
      this.getPublishers()
    },
    newPublisher() {
      if (!this.isLastEventNew) {
        this.publisherData = {...this.publisherDataTemplate}
      }

      this.isLastEventNew = true
      this.showForm()
    },
    showForm() {
      this.isVisible = true
    },
    saveTempData(publisher) {
      this.publisherData = {...publisher}
    },
    closeForm() {
      this.isVisible = false
    },
    publisherSaved({status, error = ''}) {
      this.closeForm()

      if (status) {
        this.showSuccess('Пользователь  сохранен успешно!')
      } else {
        this.showError(`Не удалось сохранить пользователя ${error}`)
      }

      this.getPublishers()
    },
    getPublisher(id) {
      this.isLastEventNew = false
      this.showForm()

      if (this.publisherData.id === id) return

      this.loadingForm = true
      const url = '/admin/backend/publisher'

      axios
        .get(url, {
          params: {
            id
          }
        })
        .then(response => {
          if (!_.isEmpty(response.data.data.publishers)) {
            this.publisherData = {...response.data.data.publishers[0]}
          } else {
            this.closeForm()
          }
        })
        .finally(() => {
          this.loadingForm = false
        })
        .catch((error) => {
          this.closeForm()
          throw new Error('Ошибка, ' + error )
        })
    }
  }
}
</script>