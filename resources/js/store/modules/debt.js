const state = {
    notify: [],
}

const mutations = {
    ADD_NOTIFY(state, notify) {
        state.notify.push(notify);
    },
    SET_NOTIFY(state, notify) {
        state.notify = notify;
    },
}

const actions = {
    addNotify: ({commit}, notify) => {
        commit('ADD_NOTIFY', notify);
    },
    setNotify: ({commit}, notify) => {
        commit('SET_NOTIFY', notify);
    },
}

export default {
    state, mutations, actions
}