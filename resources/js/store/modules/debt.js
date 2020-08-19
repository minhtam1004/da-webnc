const state = {
    notify: [],
}

const mutations = {
    ADD_NOTIFY(state, notify) {
        state.notify.unshift(notify);
    },
    SET_NOTIFY(state, notify) {
        state.notify = notify;
    },
    SET_READ(state, notify) {
        console.log("vo")
        const a = new Date();
        const month = a.getMonth() + 1;
        let date = "";
        if (a.getMonth() < 10) {
          if (a.getDate() < 10) {
            date = "0" + a.getDate() + "/0" +  month + '/' +  a.getFullYear();
          } else {
            date = a.getDate() + "/0" + month + "/" +   a.getFullYear();
          }
        } else {
          if (a.getDate() < 10) {
            date = "0" + a.getDate()  + "/" + month + "/" + a.getFullYear();
          } else {
            date = a.getDate()  + "/" + month + "/" +  + a.getFullYear();
          }
        }

        console.log(notify);
        console.log(date)
        const index = state.notify.findIndex(u => u.id == notify);
        if (index != -1) {
            console.log("Vo vo l")
            state.notify[index].readAt = date;
        }
    }
}

const actions = {
    addNotify: ({commit}, notify) => {
        commit('ADD_NOTIFY', notify);
    },
    setNotify: ({commit}, notify) => {
        commit('SET_NOTIFY', notify);
    },
    setRead: ({commit}, notify) => {
        commit('SET_READ', notify);
    },
}

export default {
    state, mutations, actions
}