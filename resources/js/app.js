require('./bootstrap');

window.Vue = require('vue');

import Vuetify from 'vuetify';
import VueRouter from 'vue-router'
import { routes }  from './index';
import App from './App.vue';
Vue.use(VueRouter)
Vue.use(Vuetify);

const router = new VueRouter({
    mode: 'history',
    routes
})

new Vue({
    router,
    vuetify: new Vuetify(),
    render: h => h(App)
}).$mount('#app');
