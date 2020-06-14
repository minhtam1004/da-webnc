const state = {
    GameDetail: null,
}

const mutations = {
    SET_GAME_DETAIL (state, gameObj) {
        state.GameDetail = gameObj;
    },
}

const actions = {
    setGameObject: ({commit}, gameObj) => {
        commit('SET_GAME_DETAIL', gameObj);
    },
}

export default {
    state, mutations, actions
}