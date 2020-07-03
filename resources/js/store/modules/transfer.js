const state = {
    accountNumber: 0,
}

const mutations = {
    SET_ACCOUNT_NUMBER(state, number) {
        state.accountNumber = number;
    },
}

const actions = {
    setAccountNumber: ({commit}, number) => {
        commit('SET_ACCOUNT_NUMBER', number);
    },
}

export default {
    state, mutations, actions
}