<template>
	<!--card start-->
    <div class="favourite-card flex-between">
        <div class="favourite-card__top">
            <a :href="`/product/${product.id}`"
               class="favourite-card__top-link"
               @click="clickLink"
            >
                <img
                    :class="['favourite-card__img', {'disabled': !isAvailable}]" 
                    :src="getCover"
                    :alt="product.title"
                    @error="setEmptyCover"
                >
            </a>
            <ProductHeart
                :isFavourite="true"
                @toggle="toggleFavourite"
            />
        </div>
        <div class="favourite-card__bottom flex-between">
            <div class="favourite-card__text-wrapper">
                <div v-if="isAvailable" class="favourite-card__price flex">
                    <div class="favourite-card__price-new">{{ $filters.numberFormat(product.price) }}&nbsp;₽</div>
                    <div class="favourite-card__price-old">{{ $filters.numberFormat(product.oldPrice) }}&nbsp;₽</div>
                </div>
                <div v-else class="favourite-card__notsale">
                    <p>Нет в наличии</p>
                </div>
                <a :href="`/product/${product.id}`" class="favourite-card__title link">
                    {{ product.title }}
                </a>
                <div class="favourite-card__author">
                    <a
                        v-for="(person, index) in product.persons"
                        :href="`/person/${person.id}`"
                        class="favourite-card__author-link link"
                        :key="person.id"
                    >
                        {{`${person.name}${index < product.persons.length - 1 ? ', ' : ''}`}}
                    </a>
                </div>                               
                <div v-if="+product.rating > 0" class="favourite-card__rating-wrapper">
                    <div class="get-rating">
                        <star-rating
                            :current="product.rating"
                            :max="5"
                        />
                    </div>
                    <div class="favourite-card__number">{{ product.rating }}</div>
                </div>
            </div>
			<div v-if="isAvailable" class="favourite-card__available">
                <div class="favourite-card__button-wrap">
					<ProductAddToCart
						:inCart="product.isCart"
                        :loading="loading"
						v-if="!product.isCart"
						@add="addToCart"
					/>
                    <ProductQuantity
                        v-else
                        :quantity="product.cartQuantity"
                        :stock="product.quantity"
                        @changeQuantity="updateQuantity"
                    />
                </div>
			</div>
        </div>
    </div>
	<!--card end-->
</template>

<script>

import ProductAddToCart from "../product/ProductAddToCart"
import ProductHeart from "../product/ProductHeart"
import ProductQuantity from "../product/ProductQuantity"
import StarRating from "../product/StarRating"
import axios from "axios"
import { mapActions } from 'vuex'

export default {
    name: "FavouriteCard",
    components: {
        ProductQuantity,
        ProductHeart,
        ProductAddToCart,
        StarRating
    },
	props: {
        product: null,
        index: 0
	},
	data() {
        return {
			quantity: this.product.quantity,
            addTimeout: null,
            loading: false,
            dataEcommerce: {
                ...this.product,
                list_id: 'favorite',
                list_name: 'Избранное',
                index: Number(this.index),
                price: {
                    discount: this.product.oldPrice ? this.product.oldPrice - this.product.price : 0,
                    new: this.product.price
                }
            }
		}
	},
    watch: {
        product() {
            if (this.product.isCart) {
                this.loading = false
            }
        }
    },
	methods: {
        ...mapActions({
            setQuantity: 'cart/setQuantity',
            getCurrentCartCount: 'cart/getCurrentCartCount',
            removeItems: 'cart/removeItems',
            getItems: 'cart/getItems',
            addItemToCart: 'cart/addItemToCart'
        }),
        toggleFavourite(value) {
            if (value) {
                axios
                    .post('/favorite', {
                        productId: this.product.id
                    })
                    .then(() => {
                        dataLayerAddToWishlist(this.dataEcommerce, false);
                    })
                    .catch(error => {
                        throw new Error('Ошибка, ' + error )
                    })
            } else {
                axios
                    .delete('/favorite', {
                        data: {
                            productId: this.product.id
                        }
                    })
                    .then(() => {
                        this.getItems()
                    })
                    .catch(error => {
                        throw new Error('Ошибка, ' + error )
                    })
            }
		},
		addToCart() {
            this.loading = true
            this.addItemToCart({productId: this.product.id, quantity: 1, dataEcommerce: this.dataEcommerce})
		},
        updateQuantity(value) {
            if (this.addTimeout !== null) {
                clearTimeout(this.addTimeout)
            }

            if (value < 0) {
                this.product.cartQuantity = 0
            } else if (value > this.product.quantity){
                this.product.cartQuantity = this.product.quantity
            } else {
                if (this.product.cartQuantity < value) {
                    dataLayerAddCart(this.dataEcommerce, this.product.id, false);
                } else {
                    dataLayerRemoveCart(this.dataEcommerce, this.product.id, false);
                }
                this.product.cartQuantity = value
            }

            if (this.product.cartQuantity === 0) {
                this.removeItems({ids: [this.product.cartItemId], isCart: false})
            } else {
                this.setQuantity({id: this.product.cartItemId, quantity: this.product.cartQuantity})
            }
		},
        setEmptyCover(){
            this.product.cover = this.$emptyCoverUrl
        },
        clickLink() {
            dataLayerSelectItem('favorite', 'Избранное', this.dataEcommerce, false)
        }
	},
	computed: {
        isAvailable() {
            return this.product.active > 0 && this.product.quantity > 0
        },
        getNumber() {
            return Math.round(this.product.rating);
        },
        getCover() {
            return _.isEmpty(this.product.cover) ? this.$emptyCoverUrl : this.product.cover
        }
	}
}
</script>

<style lang="scss">
.favourite-card {
    width: 100%;
    height: 538px;
    padding: 10px 10px 16px 10px;
    flex-direction: column;
    box-shadow: 0 0 12px 1px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    background-color: $white;
    transition: box-shadow .2s ease-in-out;

    @media (max-width: $screen-xl-l) {
        height: 526px;
    }

    @media (max-width: $screen-md) {
        padding-bottom: 14px;
        width: 100%;
        height: 100%;
    }

    &:hover {
        @include _border-radius(8px);
        box-shadow: 0 0 50px 5px rgba(0, 0, 0, 0.1);
    }

    &__top {
        position: relative;
        padding: 0;
        margin-bottom: 8px;
        width: 100%;
        height: 292px;
        @include _border-radius(8px);
        transition: border-color .2s ease-in-out;

        @media (max-width: $screen-xl-l) {
            height: 279px;
        }

        @media (max-width: $screen-md) {
            margin-bottom: 6px;
            height: 216px;
        }
    }

    &__top-link {
        height: 292px;
        display: block;

        @media (max-width: $screen-xl-l) {
            height: 279px;
        }

        @media (max-width: $screen-md) {
            height: 216px;
        }    
    }

    &__img {
        width: 100%;
        height: 100%;
        object-fit: contain;

        &.disabled {
            opacity: 0.4;
        }
    }

    &__bottom {
        flex-direction: column;
        height: 100%;
    }

    &__available {
        margin-top: auto;
    }

    &__notsale {
        margin-top: auto;
    }

    &__title {
        margin-top: 10px;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-900);
        @include  _hover(bc, $Indigo-400, bgi, bgc);
        overflow: hidden;
        text-overflow: ellipsis;
        display: -moz-box;
        display: -webkit-box;
        -moz-box-orient: vertical;
        -webkit-box-orient: vertical;
        box-orient: vertical;

        @media (max-width: $screen-md) {
            margin-top: 8px;
            line-clamp: 1;
            -webkit-line-clamp: 1;
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

    &__price {
        margin-top: auto;
        align-items: center;
        width: 100%;

        @media (max-width: $screen-md) {
            justify-content: flex-start;
        }

        &-new {
            margin-right: 8px;
            @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);

            @media (max-width: $screen-md) {
                margin-right: 6px;
                @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-900);
            }
        }

        &-old {
            text-decoration: line-through;
            @include _typography-ext(fn, 16, 26, 400, ls, $Zinc-400);

            @media (max-width: $screen-md) {
                @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-400);
            }
        }
    }

    &__rating-wrapper {
        margin-top: 8px;
        display: flex;
        align-items: center;

        @media (min-width: $tablet-width) {
            margin-top: 10px;
        }    

        .favourite-card__number {
            @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-400);
        }    
    }

    &__notsale {
        margin-top: auto;
        @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);

        @media (max-width: $screen-md) {
            @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-900);
        }
    }

    &__button {
        margin-top: 16px;
        width: 100%;

        @media (max-width: $screen-md) {
            margin-top: 6px;
        }

        &-count {
            display: flex;
            @include _border-radius(8px);
            justify-content: space-between;

            &.button-tocart {
                background-color: $Red-300;

                @media (min-width: $screen-xl) {
                    padding: 8px 54px;
                }
            }

            &_minus {
                width: 24px;
                height: 24px;
                background: get-icon('minus', $Zinc-900) no-repeat center;
                @include _button-reset(0, bgc, b);
            }

            &-input {
                input {
                    @include _typography-ext(fn, 18, 24, 600, ls, $Zinc-900);
                }
            }

            &_plus {
                width: 24px;
                height: 24px;
                background: get-icon('plus', $Zinc-900) no-repeat center;
                @include _button-reset(0, bgc, b);

                &.disabled {
                    background: get-icon('plus', $Zinc-400) no-repeat center;
                    cursor: default;
                }
            }
        }
    }
}

.star {
    background: get-icon('star', $Zinc-200) no-repeat center/100%;
    width: 18px;
    height: 18px;

    &.active {
        background: get-icon('star', $Red-400) no-repeat center/100%;
    }
}
</style>