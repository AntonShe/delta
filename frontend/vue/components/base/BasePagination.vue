<template>
  <div class="pagination">
    <button
      class="pagination__button pagination__button-back button-back"
      :class="{'pagination__button_disabled': currentPage - 1 < 1}"
      @click.prevent="pageSelected(currentPage - 1)"
    ></button>
    <template v-if="startPage > 1">
      <div
        class="pagination__number"
        :class="{'pagination__number_active': currentPage === 1}"
        @click.prevent="pageSelected(1)"
      >
        1
      </div>
      <div class="pagination__number pagination__number_dot">...</div>      
    </template>
    <div
      v-for="i in arrayOfPages"
      class="pagination__number"
      :class="{'pagination__number_active': currentPage === i}"
      :key="i"
      @click.prevent="pageSelected(i)"
    >
      {{ i }}
    </div>
    <template v-if="endPage < pageCount">
      <div class="pagination__number pagination__number_dot">...</div>
      <div
        class="pagination__number"
        :class="{'pagination__number_active': currentPage === pageCount}"
        @click.prevent="pageSelected(pageCount)"
      >
        {{ pageCount }}
      </div>
    </template>
    <button
      class="pagination__button pagination__button-next button-back"
      :class="{'pagination__button_disabled': currentPage + 1 > pageCount}"
      @click.prevent="pageSelected(currentPage + 1)"
    ></button>    
  </div>
</template>

<script>
export default {
  name: 'BasePagination',
  props: {
    pagerParams: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      pageCount: this.pagerParams.pageCount
    }
  },
  computed: {
    arrayOfPages() {
      return [...Array(this.endPage - this.startPage + 1).keys()].map(item => item + this.startPage)
    },
    currentPage() {
      return this.pagerParams.currentPage
    },
    startPage() {
      return this.pagerParams.startPage
    },
    endPage() {
      return this.pagerParams.endPage
    }
  },
  methods: {
    pageSelected(value) {
      if (value > this.pageCount || value < 1) return

      this.$emit('pageSelected', value)
      window.scrollTo(0, 0)
    }
  }
}
</script>

<style>

</style>