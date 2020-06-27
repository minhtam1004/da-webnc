import Vue from 'vue'
import Vuex from 'vuex'
import user from './modules/user'
import screen from'./modules/screen'
import chat from './modules/chat';
import loading from './modules/loading';
import game from './modules/game'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
  },
  mutations: {
  },
  actions: {
  },
  modules: {
    user,
    screen,
    chat,
    loading,
    game
  }
})