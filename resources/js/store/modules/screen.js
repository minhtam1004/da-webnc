const state = {
    width: window.innerWidth,
    height: window.innerHeight
}

const mutations = {
    SET_WIDTH(state, width) {
        state.width = width;
    },
    SET_HEIGHT(state, height) {
        state.height = height;
    }
}

const actions = {
    setWidth({ commit }, width) {
        commit('SET_WIDTH', width);
    },
    setHeight: ({ commit }, height) => {
        commit('SET_HEIGHT', height);
    }
}

export default {
    state, mutations, actions
}