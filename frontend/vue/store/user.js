import axios from "axios"
import UserApi from "../api/userApi";

export default {
  namespaced: true,
  state: {
    userInfo: null,
    activeTab: 1,
    saveErrors: []
  },
  mutations: {
    setUserInfo(state, data) {
      state.userInfo = data
      state.activeTab = data.isLegal ? 2 : 1
    },
    setErrors(state, data) {
      state.saveErrors = [...data]
    }
  },
  actions: {
    async getUserInfo({commit}) {
      const user = await UserApi.getUser()

      commit('setUserInfo', user)
    },
    async updateUserInfo({commit, dispatch}, data) {
      const res = await UserApi.updateUser(data)

      if (res.status === true) {
        commit('setErrors', [])
        dispatch('getUserInfo')
      } else {
        let errors = []

        if (_.isArray(res.errors)) {
          errors = [...res.errors]
        } else {
          errors.push(res.errors)
        }
        commit('setErrors', errors)
      }
    },
    async getUserOrders({commit}, data) {
      const orders = await UserApi.getUserOrders(data)
      
      return orders
    }
  }
}