
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import { Form, HasError, AlertError } from 'vform'
window.form = Form
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

import VueSimpleAlert from "vue-simple-alert";
import moment from 'moment'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import VueFuse from 'vue-fuse'
import V_Session from 'v-session'
import draggable from 'vuedraggable'
import VueHead from 'vue-head'
import AudioVisual from 'vue-audio-visual'
import infiniteScroll from 'vue-infinite-scroll'

Vue.use(VueSimpleAlert)
Vue.use(moment)
Vue.use(Vuex)
Vue.use(VueRouter)
Vue.use(VueFuse)
Vue.use(V_Session)
Vue.use(draggable)
Vue.use(VueHead)
Vue.use(AudioVisual)
Vue.use(infiniteScroll)

let routes = [
    {path: '/all-music', component: require('./components/AllMusic.vue').default},
    {path: '/playlist', component: require('./components/AllMusic.vue').default},
    {
        path: '/playlist/:playlist_name',
        name: 'playlist',
        component: require('./components/AllMusic.vue').default
    },
    {path: '/wishlist', component: require('./components/Wishlist.vue').default},
    {
        path: '/wishlist/:wishlist_name',
        name: 'wishlist',
        component: require('./components/Wishlist.vue').default
    },
    {path: '/log', component: require('./components/Log.vue').default},
    {path: '/report', component: require('./components/Report.vue').default},
    {path: '/manage-users', component: require('./components/Users.vue').default},
]

Vue.filter('upText', function(text){
    if(typeof(text) !== 'undefined' || text !== null){
        return text.charAt(0).toUpperCase() + value.slice(1);
    }else{
        return text;
    }
});

Vue.filter('capitalize', function (str) {
    if(typeof(str) !== 'undefined' || str !== null){
        let splitStr = str.toLowerCase().split(' ');
        for (var i = 0; i < splitStr.length; i++) {
        splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);     
        }
        
        return splitStr.join(' ');
    }else{
        return str;
    }
})

let store = new Vuex.Store({
    state: {
        selectedPlaylist: [],
        playlistParentID: Number
    },
    mutations: {
        SET_SELECTED_PLAYLIST(state, item){
            let alreadyInArray = state.selectedPlaylist.indexOf(item);
            if(alreadyInArray == -1){
                state.selectedPlaylist.push(item);
            }else{
                state.selectedPlaylist.splice(alreadyInArray, 1);
            }
        },
        RESET_SELECTED_PLAYLIST(state){
            state.selectedPlaylist = [];
        },
        SET_PLAYLIST_PARENT_ID(state, id){
            state.playlistParentID = id;
        }
    },
    actions: {

    },
    getters: {
        getSelectedPlaylist(state){
            return state.selectedPlaylist
        }
    }
});

Vue.component('upload-music', require('./components/Upload.vue').default);
Vue.component('create-playlist', require('./components/CreatePlaylist.vue').default);
Vue.component('add-playlist', require('./components/reusables/AddPlaylist.vue').default);
Vue.component('playlist-sidebar', require('./components/PlaylistSidebar.vue').default);

Vue.component('pagination', require('laravel-vue-pagination'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

/*$.AdminLTE.tree = function (menu) {
    var _this = this;
    var animationSpeed = $.AdminLTE.options.animationSpeed;
    $(document).off('click', menu + ' li a')
        .on('click', menu + ' li a', function (e) {
            //Get the clicked link and the next element
            var $this = $(this);
            var checkElement = $this.next();

            //If the menu is not visible open it
            if ((checkElement.is('.treeview-menu')) && (!checkElement.is(':visible'))) {
                //Get the parent menu
                var parent = $this.parents('ul').first();
                //Get the parent li
                var parent_li = $this.parent("li");

                //Open the target menu and add the menu-open class
                checkElement.slideDown(animationSpeed, function () {
                    //Add the class active to the parent li
                    checkElement.addClass('menu-open');
                    parent_li.addClass('active');
                    //Fix the layout in case the sidebar stretches over the height of the window
                    _this.layout.fix();
                });
            }
            //if this isn't a link, prevent the page from being redirected
            if (checkElement.is('.treeview-menu')) {
                e.preventDefault();
            }
    });
};*/

const router = new VueRouter({
    mode: 'history',
    hashbang: false,
    routes // short for `routes: routes`
})

const app = new Vue({
    el: '#app',
    router,
    store,
    /*components: {
        'navbar': require('./components/Upload.vue'),
    }*/
}).$mount('#app')