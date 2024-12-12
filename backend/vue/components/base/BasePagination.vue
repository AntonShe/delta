<template>
    <div class="pagination">
        <div class="pagination__button" @click="setCurrentPage(currentPage - 1)">&#60;</div>
        <ul class="pagination__list">
            <li class="pagination__item"
                v-for="page in pageList"
                :class="{'pagination__item_active': page == currentPage}"
                @click="setCurrentPage(page)"
            >
                {{ page }}
            </li>
        </ul>
        <div class="pagination__button" @click="setCurrentPage(currentPage + 1)">&#62;</div>
    </div>
</template>

<script>
    export default {
        name: "BasePagination",
        emits: ['changePage'],
        props: {
            settings: {
                type: Object,
                default: {
                    currentPage: 1,
                    pageCount: 1,
                    countDisplayPage: 7,
                },
            },
        },
        data() {
            return {
                currentPage: this.settings.currentPage ? this.settings.currentPage : 1,
                pageCount: this.settings.pageCount ? this.settings.pageCount : 1,
                countDisplayPage: this.settings.countDisplayPage ? this.settings.countDisplayPage : 7,
            }
        },
        computed: {
            pageList() {
                let array = []
                for (let i = this.startPage; i <= this.endPage; i++) {
                    array.push(i)
                }
                return array
            },
            countOnSide() {
                return _.floor(this.countDisplayPage / 2)
            },
            startPage() {
                if (
                    (this.currentPage - this.countOnSide < 1) ||
                    (this.currentPage - this.countOnSide - this.extraEnd < 1)
                ) {
                    return 1;
                }
                return this.currentPage - this.countOnSide - this.extraEnd
            },
            endPage() {
                if (
                    (this.currentPage + this.countOnSide > this.pageCount) ||
                    (this.currentPage + this.countOnSide + this.extraStart > this.pageCount)
                ) {
                    return this.pageCount;
                }
                return this.currentPage + this.countOnSide + this.extraStart
            },
            extraStart() {
                return this.currentPage - 1 - this.countOnSide < 0 ?
                    Math.abs(this.currentPage - 1 - this.countOnSide) :
                    0
            },
            extraEnd() {
                return this.currentPage + this.countOnSide > this.pageCount ?
                    Math.abs(this.pageCount - (this.currentPage + this.countOnSide)) :
                    0
            },
        },
        methods: {
            setCurrentPage(value) {
                if (value >= this.startPage && value <= this.endPage) {
                    this.currentPage = value
                    this.$emit('changePage', value)
                }
            },
        },
        watch: {
            settings(value) {
                this.currentPage = value.currentPage
                this.pageCount = value.pageCount
            }
        }
    }
</script>

<style lang="scss">
    .pagination {
        &__list {
            display: flex;
        }

        &__item {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: .3s;

            &:hover {
                background-color: $lightgray;
            }

            &_active{
                background-color: $gray;

                &:hover {
                    background-color: $gray;
                }
            }
        }

        &__button {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: .3s;

            &:hover {
                background-color: $lightgray;
            }
        }
    }
</style>