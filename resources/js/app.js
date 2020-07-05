import Dashboard from "./components/Dashboard";


require('./bootstrap');

window.Vue = require('vue');


import VueRouter from 'vue-router'
import Profile from "./components/Profile";
import Users from "./components/Users";
import { Form, HasError, AlertError } from 'vform';

import Gate from './Gate';

Vue.prototype.$gate = new Gate(window.user);

import Swal from 'sweetalert2'
window.Swal = Swal
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})

window.Toast = Toast

window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

Vue.use(VueRouter)

let routes = [
    { path: '/dashboard', component: Dashboard },
    { path: '/developer', component: Developer },
    { path: '/profile', component: Profile },
    { path: '/users', component: Users },
    { path: '*', component: NotFound },
]

const router = new VueRouter({
    mode: 'history',
    routes,
    linkActiveClass: 'active'
})

import moment from 'moment';
Vue.filter('upText', function(text){
    return text.toUpperCase();
})
Vue.filter('myDate', function(created){
    return moment(created).format('MMMM Do YYYY');
})


import VueProgressBar from 'vue-progressbar';
import Developer from "./components/Developer";
import NotFound from "./components/NotFound";
Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '2px'
})

window.Fire = new Vue();

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

Vue.component(
    'not-found',
    require('./components/Notfound.vue').default
);

Vue.component('pagination', require('laravel-vue-pagination'));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

const app = new Vue({
    el: '#app',
    router,
    data(){
        return{
            search: ''
        }
    },
    methods:{
        searchit: _.debounce(() => {
            Fire.$emit('searching')
        },1000),

        printme(){
            window.print()
        }
    }
});
