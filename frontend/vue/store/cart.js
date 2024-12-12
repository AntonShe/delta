import cartApi from "../api/cartApi";
import favoriteApi from "../api/favoriteApi";

export default {
  namespaced: true,
  state: {
    cartData: null,
    favotiteItems: null,
    favoritePagination: null
  },
  mutations: {
    setCartData(state, data) {
      if (!_.isEqual(state.cartData, data)) {
        state.cartData = data
      }
    },
    setFavoriteData(state, data) {
      state.favotiteItems = data.products
      state.favoritePagination = data.pagination
    }
  },
  actions: {
    async addItemToCart({dispatch}, {productId, quantity, dataEcommerce}) {
      const data = await cartApi.addToCart(productId, quantity)

      if (data && data.status === true) {
        dataLayerAddCart(dataEcommerce, productId, false);
        dispatch('getItems')
        dispatch('getCurrentCartCount')
      }
    },
    async getItems({commit}, page) {
      const favorite = await favoriteApi.getFavorite(page)
      commit('setFavoriteData', favorite)
    },
    async getCurrentCartCount({}) {
      const total = await cartApi.getTotalCart()

      if (total > 0) {
        $('.js-cart-count')
          .removeClass('hidden')
          .text(total)
      } else {
        $('.js-cart-count').addClass('hidden')
      }
    },
    async getCartData({commit}) {
      const cart = await cartApi.getCart()
      commit('setCartData', cart)

      return true
    },
    async setQuantity({dispatch}, {id, quantity}) {
      const data = await cartApi.updateItemCart(id, quantity)
      if (data && data.status) {
        dispatch('getCartData')
        dispatch('getCurrentCartCount')
      }
    },
    async removeItems({dispatch}, {ids, isCart}) {
      const data = await cartApi.removeItemsCart(ids)

      if (data && data.status) {
        isCart ? dispatch('getCartData') : dispatch('getItems')
        dispatch('getCurrentCartCount')
      }
    }
  }
}