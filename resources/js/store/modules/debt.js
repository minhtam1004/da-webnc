const state = {
    notify: [],
}

const mutations = {
    SET_NOTIFY(state, notify) {
        state.notify.push(notify);
    },
}

const actions = {
    setNotify: ({commit}, notify) => {
        commit('SET_NOTIFY', notify);
    },
}

export default {
    state, mutations, actions
}