<template>
  <form class="person-form">
    <div class="flex flex-wrap gap-4">
      <!-- <div class="person-form__item">
        <base-radio-group
          :list="activeList"
          :default-value="person.active"
          main-label="Активный"
          additional-class="person-form__radios"
          input-name="is-active"
          @onChanged="updateActive"
        />
      </div> -->
    </div>
    <div class="flex flex-wrap gap-4 mt-4">
      <div class="person-form__item">
        <label for="fio" class="text-900">ФИО автора *</label>
        <input
          v-model="person.nameFull"
          type="text"
          class="person-form__input p-inputtext p-component"
          id="fio"
          name="fio"
          required
        >
      </div>
      <div class="person-form__item">
        <label for="fio-rus" class="text-900">ФИО на русском</label>
        <input
          v-model="person.nameFullRu"
          type="text"
          class="person-form__input p-inputtext p-component"
          id="fio-rus"
          name="fio-rus"
        >
      </div>
    </div>
    <div class="flex flex-wrap gap-4 mt-4">
      <div class="person-form__item">
        <label for="synonyms" class="text-900">Синонимы</label>
        <textarea
          v-model="person.alternativeName"
          type="text"
          class="person-form__input p-inputtext p-component"
          id="synonyms"
          name="synonyms"
        />
      </div>
      <div class="person-form__item">
        <label for="description" class="text-900">Описание</label>
        <textarea
          v-model="person.description"
          class="person-form__input p-inputtext p-component"
          id="description"
          name="description"
        />
      </div>      
      <div class="person-form__item">
        <label for="seo-title" class="text-900">SEO: title (110 знаков)</label>
        <input
          v-model="person.seoTitle"
          type="text"
          maxlength="110"
          class="person-form__input p-inputtext p-component"
          id="seo-title"
          name="seo-title"
        >
      </div>
      <div class="person-form__item">
        <label for="seo-keyword" class="text-900">SEO: keywords (3000 знаков)</label>
        <input
          v-model="person.seoMetaKeywords"
          type="text"
          maxlength="3000"
          class="person-form__input p-inputtext p-component"
          id="seo-keyword"
          name="seo-keywords"
        >
      </div>
      <div class="person-form__item">
        <label for="seo-description" class="text-900">SEO: description (250 знаков)</label>
        <textarea
          v-model="person.seoMetaDescription"
          maxlength="250"
          class="person-form__input p-inputtext p-component"
          id="seo-description"
          name="seo-description"
        />
      </div>
      <div class="person-form__file">
        <div v-if="success"><i class="pi pi-check-circle" style="font-size: 1.8rem; color: green;"></i> Изображение успешно загружено</div>
        <label for="files" class="text-900 person-form__add-file">Фото автора</label>
        <input
          id="files"
          class="input-file"
          type="file"
          @change="onFileChange"
        />
        <img v-if="src" :src="src" class="input-file-img" alt="">
        <p :class="{'person-form__error': !src}">Максимальный объем файла 2 Мб,<br>Форматы: JPG, JPEG, PNG</p>
      </div>      
      <div class="flex justify-content-end gap-4">
        <Button
          label="Сохранить"
          :disabled="!isValid"
          @click.prevent="submitPerson"
        />
        <Button
          label="Отмена"
          class="p-button-outlined"
          @click.prevent="$emit('close-person-form')"
        />
      </div>
    </div>
</form>
</template>

<script>
import BaseRadioGroup from "../base/BaseRadioGroup"

export default {
  components: {
    BaseRadioGroup
  },
  props: {
    personData: {
      type: Object,
      require: true
    }
  },
  data() {
    return {
      person: {...this.personData},
      src: this.personData.id > 0 ? this.personData.cover : '',
      activeList: [{
          label: 'Да',
          value: 1,
          id: 'activeYes'       
        },
        {
          label: 'Нет',
          value: 0,
          id: 'activeNo' 
        }
      ],
      allowedExt: ['jpg', 'jpeg', 'png'],
      maxImageSize: '2097152', // 2 mb,
      success: false
    }
  },
  beforeUnmount() {
    this.$emit('saveTempData', this.person)
  },
  computed: {
    isValid() {
      return this.person.nameFull.length > 2
    }
  },
  methods: {
    updateActive(value) {
      this.person.active = value
    },
    submitPerson() {
      const method = this.person.id === 0 ? 'post' : 'patch'

      const formData = new FormData()

      Object.entries(this.person).forEach(([key, value]) => {
        formData.append(key, value)
      })

      axios({
        method,
        url: '/admin/backend/person',
        data: formData
      })
      .then(response => {
        if (response.data.status === 1) {
          this.$emit('saveComplete', {status: true})
        } else {
          this.$emit('saveError', {status: false, error: response.data.errors ? response.data.errors.join('\n') : ''})
        }
      })
      .catch(error => {
        throw new Error('Ошибка, ' + error )
      })   
    },
    onFileChange($event) {
      this.success = false
      const file = $event.target.files[0]

      file.ext = file.name.split('.').at(-1)
      // если размер файла больше разрешенного или неверное разрешение файла
      if (file.size > this.maxImageSize || !this.allowedExt.includes(file.ext)) {
        this.src = ''
        return false
      }

      this.src = URL.createObjectURL(file)
      this.person.file = file
    }
  }
}
</script>

<style lang="scss">
.person-form__item {
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  gap: 5px;
  width: calc(50% - 1.5rem);

  & > .person-form__input {
    flex-grow: 1;
    width: 100%;
  }
}

.person-form__file {
  margin-top: 18px;
}

.person-form__radios {
  .base-radio-group__list {
    display: flex;
    gap: 5px;
  }
}

.person-form__error {
  color: $error-color;
}

.input-file-img {
  max-width: 200px;
  max-height: 200px;
}
</style>