import { createStore } from "vuex"
import cart from "./cart"
import order from "./order"
import user from "./user"

export default createStore({
  state: {
    mainCounter: 100
  },
  modules: {
    cart: cart,
    order: order,
    user: user
  }
})