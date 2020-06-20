require('./bootstrap');
window.Vue = require('vue');
import Vuetify from 'vuetify';
import VueRouter from 'vue-router'
import { routes }  from './index';
import App from './App.vue';
import * as VueGoogleMaps from "vue2-google-maps";
import '@fortawesome/fontawesome-free/css/all.min.css'
import 'bootstrap-css-only/css/bootstrap.min.css'
import 'mdbvue/lib/css/mdb.min.css'
import "@babel/polyfill";
import store from './store'
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';
import Vuelidate from 'vuelidate'
Vue.use(VueGoogleMaps, {
  load: {
    key: "NHAP-API-KEY-O-DAY",
    libraries: "places"
  }
});
Vue.use(VueToast);
Vue.use(VueRouter)
Vue.use(Vuetify);
Vue.use(Vuelidate)
const router = new VueRouter({
    mode: 'history',
    routes
})

new Vue({
    router,
    store,
    vuetify: new Vuetify(),
    render: h => h(App)
}).$mount('#app');
