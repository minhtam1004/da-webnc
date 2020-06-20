const state = {
  status: false
}

const mutations = {
  SET_LOADING_STATUS (state, status){
    state.status = status;
    localStorage.setItem('loadingStatus', status);
},
}

const actions = {
  setLoadingStatus: ({commit}, status) => {
    commit('SET_LOADING_STATUS', status);
},
}

export default {
  state, mutations, actions
}