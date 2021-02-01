/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import InfiniteLoading from 'vue-infinite-loading';

window.Vue = require('vue');
// Vue.config.devtools = false
// Vue.config.debug = false
// Vue.config.silent = true
Vue.mixin(require('./blade'));
Vue.use(InfiniteLoading, { /* options */ });
import router from './router';
import App from './App.vue';


Vue.config.productionTip = false

Vue.component('nav-bar', require('./components/NavbarComponent.vue').default);
const app = new Vue({
    router,
    render: h => h(App)
}).$mount('#app')