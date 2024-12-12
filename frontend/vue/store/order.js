import orderApi from "../api/orderApi";

export default {
  namespaced: true,
  state: {
    cityList: [],
    deliveryProfile: {},
    orderData: {
      deliveryProfileId: null,
      deliveryPrice: null,
      deliveryDate: null,
      deliveryType: null,
      paymentType: 1,
      courierComment: ""
    },
    orderConfirm: {
      delivery: false,
      userProfile: false,
      userPhone: false,
    }
  },
  mutations: {
    setCityList(state, items) {
      state.cityList = [...items]
    },
    setOrderData(state, items) {
      items.forEach(element => {
        state.orderData[element.item] = element.value 
      })
    },
    setValidation(state, data) {
      state.orderConfirm[data.item] = data.value
    },
    setDeliveryProfile(state, data) {
      state.deliveryProfile = {...data}
    }
  },
  actions: {
    async getCityList({state, commit}) {
      const cityList = await orderApi.getCityList()
      commit('setCityList', cityList)
    },
    async getDeliveryProfile({state, commit}) {
      const data = await orderApi.getDeliveryProfile()

      const deliveryProfile = !_.isEmpty(data) ? {...data} : { courier: {}, point: {} }
      commit('setDeliveryProfile', deliveryProfile)
    }
  }
}