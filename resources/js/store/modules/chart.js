const state = {
    data: null,
}

const mutations = {
    SET_DATA_CHART(state, obj) {
        state.data = obj;
    },
}

const actions = {
    setDataChart: ({ commit }, obj) => {
        console.log("lulu", obj)
        commit('SET_DATA_CHART', obj);
    },
}

export default {
    state, mutations, actions
}