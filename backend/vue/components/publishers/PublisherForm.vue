<template>
  <form class="publisher-form">
    <div class="flex flex-wrap gap-4">
    </div>
    <div class="flex flex-wrap gap-4 mt-4">
      <div class="publisher-form__item">
        <label for="name-pub" class="text-900">Название *</label>
        <input
          v-model="publisher.name"
          type="text"
          class="publisher-form__input p-inputtext p-component"
          id="name-pub"
          name="name-pub"
          required
        >
      </div>
      <div class="publisher-form__item">
        <!-- <base-radio-group
          :list="activeList"
          :default-value="publisher.isActive"
          main-label="Активный"
          additional-class="publisher-form__radios"
          input-name="is-active"
          @onChanged="updateActive"
        /> -->
      </div>
    </div>
    <div class="flex flex-wrap gap-4 mt-4">
      <div class="publisher-form__item">
        <label for="description" class="text-900">Описание</label>
        <textarea
          v-model="publisher.description"
          class="publisher-form__input p-inputtext p-component"
          id="description"
          name="description"
        />
      </div>      
      <div class="publisher-form__item">
        <label for="seo-title" class="text-900">SEO: title (110 знаков)</label>
        <input
          v-model="publisher.seoTitle"
          type="text"
          maxlength="110"
          class="publisher-form__input p-inputtext p-component"
          id="seo-title"
          name="seo-title"
        >
      </div>
      <div class="publisher-form__item">
        <label for="seo-keyword" class="text-900">SEO: keywords (3000 знаков)</label>
        <input
          v-model="publisher.seoMetaKeywords"
          type="text"
          maxlength="3000"
          class="publisher-form__input p-inputtext p-component"
          id="seo-keyword"
          name="seo-keywords"
        >
      </div>
      <div class="publisher-form__item">
        <label for="seo-description" class="text-900">SEO: description (250 знаков)</label>
        <textarea
          v-model="publisher.seoMetaDescription"
          maxlength="250"
          class="publisher-form__input p-inputtext p-component"
          id="seo-description"
          name="seo-description"
        />
      </div>
      <div class="publisher-form__item publisher-form__file">
        <div v-if="success"><i class="pi pi-check-circle" style="font-size: 1.8rem; color: green;"></i> Изображение успешно загружено</div>
        <label for="files" class="text-900 publisher-form__add-file">Лого издательства</label>
        <input
          id="files"
          class="publisher-form__input-file"
          type="file"
          @change="onFileChange"
        />
        <img v-if="src" :src="src" class="publisher-form__logo" alt="">
        <p :class="{'publisher-form__error': !src}">Максимальный объем файла 2 Мб,<br>Форматы: JPG, JPEG, PNG</p>
      </div>      
      <div v-if="publisher.id > 0" class="publisher-form__item">
        <label class="text-900">Серии издательства:</label>
        <div class="publisher-form__series-wrapper">
          <div v-if="seriesOfPublisher.length === 0">Нет серий</div>
          <div v-for="name in seriesOfPublisher" :key="name">{{ name }}</div>
        </div>
      </div>
    </div>
    <div class="flex justify-content-center gap-4 mt-4">
      <Button
        label="Сохранить"
        :disabled="!isValid"
        @click.prevent="submitPublisher"
      />
      <Button
        label="Отмена"
        class="p-button-outlined"
        @click.prevent="$emit('close-publisher-form')"
      />
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
    publisherData: {
      type: Object,
      require: true
    }
  },
  data() {
    return {
      publisher: {...this.publisherData},
      src: this.publisherData.id > 0 ? this.publisherData.cover : '',
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
    this.$emit('saveTempData', this.publisher)
  },  
  computed: {
    isValid() {
      return this.publisher.name.length > 2
    },
    seriesOfPublisher() {
      if (this.publisher.series.length === 0) return []

      return this.publisher.series.map(item => item.name)
    }
  },
  methods: {
    updateActive(value) {
      this.publisher.isActive = value
    },
    submitPublisher() {
      const method = this.publisher.id === 0 ? 'post' : 'patch'

      const formData = new FormData()

      Object.entries(this.publisher).forEach(([key, value]) => {
        formData.append(key, value)
      })

      axios({
        method,
        url: '/admin/backend/publisher',
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
      this.publisher.file = file
    }
  }
}
</script>
  
<style lang="scss">
.publisher-form__item {
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  gap: 5px;
  width: calc(50% - 1.5rem);

  & > .publisher-form__input {
    flex-grow: 1;
    width: 100%;
  }
}

.publisher-form__logo {
  max-width: 150px;
  max-height: 150px;
}

.publisher-form__radios {
  display: flex;
  flex-direction: column;
  gap: 10px;

  .base-radio-group__list {
    display: flex;
    gap: 5px;
  }
}

.publisher-form__error {
  color: $error-color;
}

.publisher-form__series-wrapper {
  max-height: 200px;
  overflow: hidden auto;
  background-color: #f2fff4;
}
</style>