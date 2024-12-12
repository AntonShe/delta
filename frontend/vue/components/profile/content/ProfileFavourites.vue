<template>
	<div>
        <ProfileTitle title="Избранное" :isShowAlways=true v-if="needTitle"/>
        <template v-if="items && items.length > 0">
            <div class="cards">
                <div class="cards__item"
                    v-for="(item, index) in items"
                    :key="item.id"
                >
                    <FavouriteCard :product="item" :index="index"/>
                </div>
            </div>
            <BasePagination :pagerParams="pagination" @pageSelected="setPageNumber"/>
        </template>
        <div v-else-if="items && items.length === 0" class="cards-empty">
            <div class="cards-empty__wrapper">
                <div><img class="cards-empty__img" src="../../../../web/img/empty-favorite-capybara.svg" alt="Избранные товары"></div>
                <div class="cards-empty__title">Здесь будут ваши избранные товары</div>
                <div class="cards-empty__text">Добавляйте товары в избранное, нажав на&nbsp;сердечко</div>
            </div>

            <a class="cards-empty__btn" href="/">Вернуться на главную</a>
        </div>
	</div>
</template>

<script>
import FavouriteCard from "./favourite/FavouriteCard"
import ProfileTitle from "./ProfileTitle"
import BasePagination from "../../base/BasePagination"
import { mapActions, mapState } from 'vuex'

export default {
    name: "ProfileFavourites",
    components: {
        ProfileTitle,
        FavouriteCard,
        BasePagination
    },
    props: {
        needTitle: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            page: 1
        }
    },
	beforeMount() {
        this.getItems(this.page);
    },
    computed: {
        ...mapState({
            items: state => state.cart.favotiteItems,
            pagination: state => state.cart.favoritePagination
        })
    },
    methods: {
        ...mapActions({
            getItems: 'cart/getItems'
        }),
        setPageNumber(value) {
            this.page = value
            this.getItems(this.page)
        }
    }
}
</script>

<style lang="scss" scoped>
.cards {
    margin-bottom: 40px;
    display: -ms-grid;
    display: grid;
    grid-gap: 20px;
    grid-template-columns: repeat(auto-fill, 252px);
    align-items: center;

    @media (max-width: $screen-xl-l) {
        grid-gap: 18px;
        grid-template-columns: repeat(auto-fill, 230px);
    }

    @media (max-width: $screen-md) {
        margin-bottom: 20px;
        grid-template-columns: repeat(auto-fill, 156px);
        justify-content: center;
    }
}

.cards__item {
    height: 100%;
}

.cards-empty {
    @media (min-width: $screen-xl) {
        margin-top: -50px;
    }
}
.cards-empty__wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    text-align: center;

    @media (min-width: $screen-xl) {
        gap: 20px;
    }
}

.cards-empty__img {
    max-height: 158px;

    @media (min-width: $screen-xl) {
        max-height: 288px;
    }
}

.cards-empty__title {
    @include _typography-ext(fn, 24, 32, 600, ls, $Zinc-900);
    max-width: 250px;

    @media (min-width: $tablet-width) {
        max-width: 100%;
    }

    @media (min-width: $screen-xl) {
        @include _typography-ext(fn, 24, 32, 600, ls, $Zinc-900);
    }
}

.cards-empty__text {
    @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);

    @media (min-width: $screen-xl) {
        @include _typography-ext(fn, 18, 24, 400, ls, $Zinc-900);
    }
}

.cards-empty__btn {
    @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-700);
    @include _border-radius(8px);
    padding: 10px;
    height: 56px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: $Red-200;

    @media (min-width: $tablet-width) {
        @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-700);
        margin: 20px auto auto;
        max-width: 282px;
        height: 44px;
    }

    @media (min-width: $screen-xl) {
        @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-700);
        margin-top: 40px;
        max-width: 304px;
        height: 56px;

        &:hover {
            background-color: $Red-300;
        }        
    }
}
</style>