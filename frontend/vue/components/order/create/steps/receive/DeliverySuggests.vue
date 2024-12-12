<template>
  <div v-if="suggestOptions.length > 0" class="suggest__wrapper">
    <div v-for="option in suggestOptions"
      :key="option.address"
      class="suggest__item"
      @click="clickSuggestItem(option)"
    >
      {{ option.address }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'DeliverySuggests',
  props: {
    query: {
      type: String,
      default: ""
    }
  },
  watch: {
    query() {
      this.getSuggestItems()
    }
  },
  data() {
    return {
      suggestOptions: [],
      isOpen: true
    }
  },
  mounted() {
    this.getSuggestItems()
    document.querySelector('body').addEventListener('click', () => {this.clearItems()})
  },
  methods: {
    getSuggestItems() {
      const options = {
        method: "POST",
        mode: "cors",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "Authorization": "Token " + this.$dadataToken
        },
        body: JSON.stringify({
          query: this.query,
          count: 8,
          locations: [{
            country: 'Россия'
          }]       
        })
      }

      fetch(this.$dadataUrl, options)
      .then(response => response.text())
      .then(result => {
        const newArray = JSON.parse(result).suggestions.map(item => item = {address: item.value, city: item.data.city})
        this.suggestOptions = [...newArray]
      })
      .catch(error => console.log("error", error)); 
    },
    clearItems() {
      this.suggestOptions = []
    },
    clickSuggestItem(option) {
      this.$emit('suggest-choice', option)
      this.clearItems()
    }
  }
}
</script>

<style lang="scss" scoped>
.suggest__wrapper {
  @include _border-radius(0 0 8px 8px);
  margin-top: 42px;
  padding: 4px;
  width: calc(100% - 40px);
  position: absolute;
  background-color: $white;
  border: 1px solid rgba(0, 0, 0, 0.05);
  box-shadow: 0 15px 20px rgba(0, 0, 0, 0.05);
  transition: backgroung .2s ease-in-out;
  z-index: 1;

  @media (min-width: 626px) {
    width: calc(100% - 52px);
  }

  @media (min-width: $tablet-width) {
    width: 484px;
  }
}

.suggest__item {
  @include _border-radius(8px);
  @include _typography-ext(fn, 14, 26, 400, ls, $Zinc-900);
  padding: 5px 16px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  cursor: pointer;

  &:hover {
    background-color: $Red-50;
  }
}
</style>