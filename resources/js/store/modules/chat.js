/**
 * @enum {number}
 */
export const FriendStatus = {
    IDLE: -1,
    OFFLINE: 0,
    ONLINE: 1,
};

if (typeof Array.prototype.removeIf !== 'function')
{
    Array.prototype.removeIf = function(callback) {
        let i = 0;

        while (i < this.length) {
            if (callback(this[i], i)) 
            {
                this.splice(i, 1);
            }
            else 
            {
                ++i;
            }
        }
    }
}

/**
 * 
 */
const state = {
    /**
     * @type {{ 
     *      userId: number, 
     *      name: string, 
     *      avatar: string, 
     *      messages: { messageId: string|number, message: string, createdAt: Date, isRead?: boolean }[],
     * }[]
     * }
     */
    privateMessages: [],

    /**
     * // Có senderId tự lấy name + avatar từ API
     * @type {{ 
     *      groupId: number, 
     *      name: string, 
     *      avatar: string,
     *      messages: Array<{ messageId: string|number, message: string, senderId: number, createdAt: Date }>,
     * }[]
     * }
     */
    groupMessages: [],

    isConnected: false,
    isAuthenticated: false,

    /**
     * @type {Array<{hash: string, type: 'user'|'group', time: Date, receiverId: number }>}
     */
    pendingMessages: [],
    
    turnOffChat: false,

    /**
     * @type {{userId: number, name: string, avatar: string, isFriend: boolean, id: number}}
     */
    friends: [],
}

const mutations = {
    CHAT_CONNECTED(state, value) {
        state.isConnected = value;
    },
    CHAT_AUTHENTICATED(state, value) {
        state.isAuthenticated = value;
    },
    CHAT_ADD_PENDING(state, data)
    {
        if (!state.pendingMessages.some(t => t.hash === data.hash))
        {
            state.pendingMessages.push({
                ...data
            });
        }
    },
    CHAT_REMOVE_PENDING(state, data) {
        
        let message = null;

        for(const [index, m] of state.pendingMessages.entries())
        {
            if (m.hash === data.hash)
            {
                message = m;
                state.pendingMessages.splice(index, 1);
                break;
            }
        }

        if (message)
        {
            if (message.type === 'user')
            {
                if (!state.privateMessages[message.receiverId])
                {
                    state.privateMessages[message.receiverId] = []
                }

                state.privateMessages[message.receiverId].push(message);
            }
            else
            {
                if (!state.privateMessages[message.receiverId])
                {
                    state.privateMessages[message.receiverId] = { messages: [] }
                }

                state.groupMessages[message.receiverId].messages.push(message);
            }
        }
    },

    CHAT_NEW_PRIVATE_MESSAGE(state, data) {
        if (!state.privateMessages[data.receiverId]) {
            state.privateMessages[data.receiverId] = { messages: [] }
        }
    },

    CHAT_FRIENDS(state, data) {

        // state.users.forEach(u => u.isFriend = false);
        state.friends.length = 0;

        data.forEach(function(friend){
            state.friends.push({
               ...friend,
               status: FriendStatus.OFFLINE, 
            });
        });
    },

    FRIEND_STATUS(state, data) {
        const f = state.friends.find(f => f.id === data.userId);

        if (f) {
            f.status = data.status;
        }
    }
}

const actions = {
    chatAuthenticate(_, { $socket, access_token }) {
        $socket.emit('authentication', { access_token });
    },
    sendPrivateMessage({ commit }, { $socket, hash, message, receiverId }) {
        const data = { hash, message, receiverId };
        $socket.emit('chat', data);
        data.type = 'user';
        commit('CHAT_ADD_PENDING', data);
    },
    getFriends(_, { $socket }) {
        $socket.emit('friends');
    },

    CHAT_connect({ commit }) {
        commit('CHAT_CONNECTED', true);
    },

    CHAT_authentication({ commit }){
        commit('CHAT_AUTHENTICATED', true);
    },

    CHAT_disconnect({ commit }){
        commit('CHAT_CONNECTED', false);
        commit('CHAT_AUTHENTICATED', false);
    },

    CHAT_friends({ commit }, data) {
        if (Array.isArray(data))
        {
            commit('CHAT_FRIENDS', data);
        }

        console.log(data);

    },
    CHAT_user_status({ commit }, data) {
        commit('FRIEND_STATUS', data);
    }
}

export default {
    state, mutations, actions, namespaced: true
}