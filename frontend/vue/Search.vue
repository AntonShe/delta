<template>
    <div @click.stop>
        <div class="header__search-mobile">
            <div class="header__search-icon"></div>
            <div class="header__search-text">Поиск</div>
        </div>
        <div class="header__right-item header__search">
            <div class="header__search-top">
                <input
                    v-model.trim="search"
                    class="header__search-input input"
                    type="text"
                    maxlength="100"
                    name="search"
                    id="header-search"
                    placeholder="Название книги, курса, учебника"
                    autocomplete="off"
                    @click.stop="focused = true"
                    @keyup.enter="onEnter"
                >
                <span class="header__search-buttons">
                    <button v-if="search.length > 0" class="header__cross-icon" @click="search=''"></button>
                    <a class="header__search-icon" :href="link" @click="sendSearch"></a>
                </span>
            </div>
            <div class="header__search-error" v-if="error.length > 0 && search.length > 0">
                {{ error }}
            </div>
            <div
                v-show="isDropdownShow"
                class="header__search-dropdown dropdown ppp"
            >
                <ul class="dropdown__list">
                    <li v-for="item in result" class="dropdown__item" :key="item.id">
                        <a :href="`/product/${item.id}`" class="dropdown__item-link" @click="() => dropdownClick(item.title)">
                            {{ item.title }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script>
import * as Router from 'vue-router'

export default {
    name: "Search",
    data() {
        return {
            search: '',
            result: [],
            abortController: {},
            requestIsSend: false,
            focused: false,
            error: '',
            isMobile: window.innerWidth < 760
        }
    },
    computed: {
        link() {
            return `/search/${encodeURIComponent(this.search)}`
        },
        isSearchActive() {
            return this.search.length > 2
        },
        isMobileShow() {
            return this.isMobile && this.search.length > 0
        },
        isDropdownShow() {
            return this.isMobileShow || (this.result.length > 0 && this.focused)
        }
    },
	beforeMount() {
        window.addEventListener('resize', this.onResize)
    },
    mounted() {
        let that = this
        $('body').click(() => {
            that.focused = false
        })
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.onResize)
    },
    watch: {
        search() {
            if ((this.search.length <= 3 && this.requestIsSend) || this.requestIsSend) {
                this.abortController.abort()
                this.requestIsSend = false
                this.result = []
            }

            if (this.isSearchActive) {
                this.error = ''
                this.sendRequest()
            } else {
                this.error = 'Введите от 3х символов'
                this.result = []
            }


            if (!this.isMobile) return

            const bodyElement = document.querySelector('body')

            if (this.isMobileShow) {
                if (bodyElement && !bodyElement.classList.contains('overflow-hidden')) {
                    bodyElement.classList.add('overflow-hidden')
                }
            } else {
                if (bodyElement) bodyElement.classList.remove('overflow-hidden')
            }
        }
    },
    methods: {
        sendRequest() {
            this.abortController = new AbortController()
            this.result = []
            this.requestIsSend = true

            this.axios
                .get('/search/search-line/list', {
                    signal: this.abortController.signal,
                    params: {
                        search: this.search,
                        limit: 5,
                        active: 1,
                        is_new: 0,
                    }
                })
                .then((response) => {
                    if (!response.data.errors) {
                        this.result = response.data.products

                        if (this.result.length === 0) {
                            this.error = 'Ничего не найдено'
                        }
                    }
                    this.requestIsSend = false
                })
                .catch((error) => {
                    throw new Error('Ошибка в поиске товаров: ' + error)
                })
        },
        onEnter() {
            if (!this.isSearchActive) return

            dataLayerSearch(this.search)
            window.location.href = this.link
        },
        dropdownClick(search) {
            dataLayerSearch(search)
        },
        sendSearch(e) {
            if (!this.isSearchActive) e.preventDefault()
        },
        onResize() {
            this.isMobile = window.innerWidth < 760;
		}
    }
}
</script>

<style lang="scss" scoped>
.dropdown__list-title {
    color: $Zinc-400;
}
</style>