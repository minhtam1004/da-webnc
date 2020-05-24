require('./bootstrap');

window.Vue = require('vue');

import Vuetify from 'vuetify';
import VueRouter from 'vue-router'
import { routes }  from './index';

Vue.use(VueRouter)
Vue.use(Vuetify);

const router = new VueRouter({
    mode: 'history',
    routes
})

const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
    router
});