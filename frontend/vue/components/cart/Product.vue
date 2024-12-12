<template>
    <!-- // TODO: избавиться от product.productInfo -->
    <div class="product">
        <div class="product__checkbox" v-if="isCart">
            <BaseCheckbox @updateValue="checkForRemove" :value="isRemove"/>
        </div>
        <div v-else class="product__number">
            {{ getIndex }}
        </div>

        <div class="product__cover" v-if="isCart">
            <img :src="cover" :alt="product.productInfo.title" @error="setEmptyCover"/>
            <ProductHeart :isFavourite="product.productInfo.isFavourite" :isCart="isCart" @toggle="toggleFavourite"/>
        </div>
        <div class="product__cover" v-else>
            <img :src="cover" :alt="product.title" @error="setEmptyCover"/>
        </div>

        <div class="product__book">
            <a :href="getLink"
                target="_blank"
                class="product__book-title"
                @click="clickLink"
            >
                {{ `${isCart ? product.productInfo.title : product.title}` }}
            </a>
            <div class="product__author">
                <a
                    v-for="(person, index) in getAuthorsArray"
                    :href="`/person/${person.id}`"
                    class="product__author-link link"
                    :key="person.id"
                >
                    {{`${person.name}${index < getAuthorsArray.length - 1 ? ', ' : ''}`}}
                </a>
            </div>
        </div>

        <div class="product__quantity">
            <div v-if="isCart">
                <BaseCounter
                    v-if="isAvailableQuantity"
                    :counter="getQuantity"
                    name="quantity"
                    id="quantity"
                    :min="0"
                    :max="product.quantity.available"
                    @changeCounter="setQuantity"
                    @incrementClick="incrementClick"
                    @decrementClick="decrementClick"
                    :isError="product.quantity.isError"
                />
                <div v-else>
                    <div class="product__notavailable">Нет в наличии</div>
                </div>
            </div>
            <div v-else>
                {{ product.quantityCart }} шт.
            </div>
        </div>

        <div class="product__price" v-if="isCart">
            <div class="product__price-new">
                {{ $filters.numberFormat(product.productInfo.price.new) }} &#8381;
            </div>
            <div class="product__price-old">
                {{ $filters.numberFormat(product.productInfo.price.old) }} &#8381;
            </div>
        </div>
        <div class="product__price" v-else>
            <div class="product__price-new">
                {{ $filters.numberFormat(product.priceInOrder) }} &#8381;
            </div>
            <div class="product__price-old">
                {{ $filters.numberFormat(product.oldPrice) }} &#8381;
            </div>
        </div>

        <button v-if="isCart" class="product__cross" @click="remove"></button>
        <button class="product__more"
                v-if="isShowDots && isCart"
                @click="toggleDots"
                ></button>
        <div class="product__popup"
             @mousedown.self="toggleDots"
             :class="{
                    'product__popup_active': isShowPopup
			    }">
            <div v-if="isCart" class="product__popup-item">
                <ProductHeart
                    :isFavourite="product.productInfo.isFavourite"
                    :text="'В избранное'"
                    :isPopup="true"
                    @toggle="toggleFavourite"
                />
                <button class="product__popup-remove" @click="remove">Удалить</button>
            </div>
        </div>
    </div>
</template>

<script>
// import {inject} from "vue"
import axios from "axios";
import ProductAddToCart from "../profile/content/product/ProductAddToCart"
import ProductHeart from "../profile/content/product/ProductHeart"
import { mapActions } from 'vuex';

export default {
    name: "Product",
    components: {
        ProductHeart,
        ProductAddToCart
    },
    props: {
        product: {
            type: Object,
            default: null
        },
        isRemove: Boolean,
        isCart: {
            type: Boolean,
            default: true
        },
        index: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            authors: null,
            title: null,
            isFavourites: null,
            isShowDots: false,
            isShowPopup: false,
            favorites: [],
            cover: null,
            removeClick: false,
            dataEcommerce: Object.assign({}, this.isCart ? this.product.productInfo : this.product, {
                list_id: this.isCart ? 'cart' : 'profile',
                list_name: this.isCart ? 'Корзина' : 'Заказы в профиле',
                index: Number(this.index),
                price: this.isCart ? this.product.productInfo.price : {
                    discount: this.product.oldPrice ? this.product.oldPrice - this.product.price : 0,
                    new: this.product.price
                }
            })
        }
    },
    beforeMount() {
        this.getFavorites()
        this.cover = this.getCover
    },
    mounted() {
        this.onResize();
        window.addEventListener('resize', this.onResize)
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.onResize)
    },
    computed: {
        getQuantity() {
            return (this.product.quantity.cart > this.product.quantity.available)
                ? this.product.quantity.available
                : this.product.quantity.cart
        },
        isAvailableQuantity() {
            return this.product.quantity.available !== 0
        },
        getIndex() {
            return Number(this.index) + 1
        },
        getLink() {
            return '/product/' + ( this.isCart ? this.product.productInfo.id : this.product.id)
        },
        getCover() {
            if (this.isCart) {
                return _.isEmpty(this.product.productInfo.cover) ? this.$emptyCoverUrl : this.product.productInfo.cover
            } else {
                return _.isEmpty(this.product.cover) ? this.$emptyCoverUrl : this.product.cover
            }
        },
        getAuthorsArray() {
            const authorsArray = this.isCart ? this.product.productInfo.persons : this.product.persons

            return authorsArray
        }
    },
    methods: {
        ...mapActions({
            setupQuantity: 'cart/setQuantity',
            removeItems: 'cart/removeItems'
        }),
        getFavorites() {
            axios
                .get('/favorite')
                .then(result =>  {
                    this.favorites = result.data
                })
        },
        remove() {
            if (!this.removeClick) {
                this.removeItems({ids: [this.product.id], isCart: true})
                this.removeClick = true;

                dataLayerRemoveCart(this.dataEcommerce, this.product.productInfo.id, false, this.product.quantity.cart);
            }
        },
        checkForRemove(value) {
            this.$emit('changeRemoveList', {product: this.product, isAdd: value})
        },
        toggleFavourite(value) {
            if (value) {
                axios
                    .post('/favorite', {
                        productId: this.product.product_id
                    })
                    .then(response => {
                        dataLayerAddToWishlist(this.dataEcommerce, false);
                    })
                    .catch(error => {
                        throw new Error('Ошибка, ' + error )
                    })
            } else {
                axios
                    .delete('/favorite', {
                        data: {
                            productId: this.product.product_id
                        }
                    })
                    .then(response => {})
                    .catch(error => {
                        throw new Error('Ошибка, ' + error )
                    })
            }
        },
        setQuantity(value) {
            if (value < 1) {
                this.remove();
                return;
            }
            this.setupQuantity({id: this.product.id, quantity: parseInt(value)})
        },
        addToCart() {
            this.product.inCart = !this.product.inCart
            this.$emit('toCart', {id: this.product.id})
        },
        removeFromCart() {
            this.product.inCart = !this.product.inCart
            this.$emit('fromCart', {id: this.product.id})
        },
        onResize() {
            this.isShowDots = (window.innerWidth <= 760)
        },
        toggleDots() {
            this.isShowPopup = !this.isShowPopup
        },
        setEmptyCover() {
            this.cover = this.$emptyCoverUrl
        },
        incrementClick(value) {
            if (value <= this.product.quantity.available){
                dataLayerAddCart(this.dataEcommerce, this.product.productInfo.id, false);
            }
        },
        decrementClick(value) {
            if (value >= 0 && value < this.product.quantity.available) {
                dataLayerRemoveCart(this.dataEcommerce, this.product.productInfo.id, false);
            }
        },
        clickLink() {
            dataLayerSelectItem(this.dataEcommerce.list_id, this.dataEcommerce.list_name, this.dataEcommerce, false)
        }
    }
}
</script>


<style lang="scss">
.product {
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    align-items: center;

    @media (max-width: $screen-md) {
        display: -ms-grid;
        display: grid;
        grid-template-areas:
        "a b i"
        "a d ."
        "a c .";
        grid-template-columns: 98px minmax(195px, 557px) 16px;
        column-gap: 10px;
        row-gap: 6px;
    }

    &__checkbox {
        @media (max-width: $screen-md) {
            display: none;
        }
    }

    &__number {
        max-width: 42px;
        width: 100%;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
    }

    &__price {
        display: flex;
        grid-area: d;

        &-new {
            margin-right: 8px;
            @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-900);
            white-space: nowrap;

            @media (max-width: $screen-xl) {
                font-size: 16px;
                line-height: 26px;
                white-space: nowrap;
            }
        }

        &-old {
            text-decoration: line-through;
            @include _typography-ext(fn, 18, 24, 400, ls, $Zinc-400);
            white-space: nowrap;

            @media (max-width: $screen-xl) {
                font-size: 16px;
                line-height: 26px;
                white-space: nowrap;
            }
        }
    }

    &__cover {
        grid-area: a;
        position: relative;
        padding: 10px;
        height: 146px;
        width: 116px;
        display: flex;
        @include _border-radius(8px);
        border: 1px solid $Zinc-200;

        @media (max-width: $screen-xl) {
            padding: 8px;
            height: 124px;
            width: 98px;
        }

        img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    }

    &__book {
        grid-area: b;
        max-width: 320px;
        width: 100%;

        @media (max-width: $screen-xl) {
            max-width: 204px;
        }

        @media (max-width: $screen-md) {
            max-width: unset;
        }

        &-title {
            @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
            margin-bottom: 6px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -moz-box;
            display: -webkit-box;
            -moz-box-orient: vertical;
            -webkit-box-orient: vertical;
            box-orient: vertical;
            line-clamp: 2;
            -webkit-line-clamp: 2;

            @media (max-width: $screen-xl) {
                @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-900);
            }

            @media (max-width: $screen-md) {
                margin-bottom: 2px;
                line-clamp: 2;
                -webkit-line-clamp: 2;
            }
        }
    }

    &__author {
        margin-top: 4px;
        white-space: nowrap;
        overflow-x: hidden;
        text-overflow: ellipsis;
        color: $Zinc-400;
    }

    &__author-link {
        line-clamp: 1;
        -webkit-line-clamp: 1;
        @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-400);
    }

    &__quantity {
        grid-area: c;
        width: 185px;

        @media (max-width: $screen-xl) {
            width: 156px;
        }
    }

    &__notavailable {
        width: 100%;
        background-color: $Red-25;
        @include _typography-ext(fn, 14, 20, 400, ls, $Rose-600);
        text-align: center;
        @include _border-radius(4px);
    }

    &__more {
        grid-area: i;
        width: 16px;
        height: 16px;
        align-self: start;
        background: get-icon('more', $Zinc-900) no-repeat center;
        @include _button-reset(p, bgc, none);
    }

    &__cross {
        width: 24px;
        height: 24px;
        @include _button-reset(p, bgc, none);
        background: get-icon('cross', $Zinc-400) no-repeat center;
        @include _hover(bc, tc, get-icon('cross', $Zinc-900) no-repeat center, bgc);

        @media (max-width: $screen-md) {
            display: none;
        }
    }

    &__popup {
        position: fixed;
        z-index: 101;
        bottom: 0;
        left: 0;
        background-color: $white;
        width: 100%;
        height: 100px;
        padding: 20px 15px 50px 15px;
        transform: translateY(100%);
        opacity: 0;
        transition: transform .2s ease-in-out;

        &_active {
            transform: translateY(0);
            opacity: 1;

            &::before {
                content: '';
                position: fixed;
                top: -100vh;
                left: 0;
                right: 0;
                bottom: 100px;
                background: rgba(0, 0, 0, 0.1);
                backdrop-filter: blur(2px);
            }
        }

        &-item {
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid $Zinc-200;
            @include _typography-ext(fn, 12, 16, 600, ls, $Zinc-900);
        }

        &-remove {
            @include _button-reset(p, bgc, none);
            @include _typography-ext(fn, 12, 16, 600, ls, $Zinc-900);
        }
    }
}

</style>