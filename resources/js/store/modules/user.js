import CryptoJS from 'crypto-js';
const key = "K23%757*$%re@#$$Na^^**!@$()";
const encryptedAccessToken = localStorage.getItem('accessToken');
const encryptedRefreshToken = localStorage.getItem('refreshToken');
const encryptedAuthUser = localStorage.getItem('authUser');
let decryptedAccessToken = null;
let decryptedRefreshToken = null;
let decryptedAuthUser = null;

if (typeof encryptedAccessToken === 'string') {
    decryptedAccessToken = CryptoJS.AES.decrypt(encryptedAccessToken, key).toString(CryptoJS.enc.Utf8);
    console.log(decryptedAccessToken);
}

if (typeof encryptedRefreshToken === 'string') {
    decryptedRefreshToken = CryptoJS.AES.decrypt(encryptedRefreshToken, key).toString(CryptoJS.enc.Utf8);
}

if (typeof encryptedAuthUser === 'string') {
    var decryptedAuthUserString = CryptoJS.AES.decrypt(encryptedAuthUser, key).toString(CryptoJS.enc.Utf8);
    decryptedAuthUser = JSON.parse(decryptedAuthUserString);
    console.log(decryptedAuthUser);
}
const state = {
    authUser: decryptedAuthUser,
    access_token: decryptedAccessToken,
    refresh_token: decryptedRefreshToken,
    language: null,
}

const mutations = {
    SET_AUTH_USER(state, userObj) {
        state.authUser = userObj;
        const encryptedAuthUser = CryptoJS.AES.encrypt(JSON.stringify(userObj), key).toString();
        localStorage.setItem('authUser', encryptedAuthUser);
    },

    SET_ACCESS_TOKEN(state, access_token) {
        state.access_token = access_token;
        const encryptedAccessToken = CryptoJS.AES.encrypt(access_token, key).toString();
        localStorage.setItem('accessToken', encryptedAccessToken);
    },

    SET_REFRESH_TOKEN(state, refresh_token) {
        state.refresh_token = refresh_token;
        const encryptedRefreshToken = CryptoJS.AES.encrypt(refresh_token, key).toString();
        localStorage.setItem('refreshToken', encryptedRefreshToken);
    },

    SET_LANGUAGE(state, language) {
        state.language = language;
        localStorage.setItem('language', language);
    },

    LOG_OUT(state) {
        state.authUser = null;
        state.access_token = null;
        state.refresh_token = null;
        localStorage.removeItem('authUser');
        localStorage.removeItem('accessToken');
        localStorage.removeItem('refreshToken');
    }
}

const actions = {
    setUserObject: ({ commit }, userObj) => {
        commit('SET_AUTH_USER', userObj);
    },

    setAccessToken: ({ commit }, access_token) => {
        console.log(access_token);
        commit('SET_ACCESS_TOKEN', access_token);
    },
    setRefreshToken: ({ commit }, refresh_token) => {
        commit('SET_REFRESH_TOKEN', refresh_token);
    },
    setLanguage: ({ commit }, language) => {
        commit('SET_LANGUAGE', language);
    },
    logOut: ({ commit }) => {
        commit('LOG_OUT');
    },
}

export default {
    state, mutations, actions
}