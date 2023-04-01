/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

// start the Stimulus application
import './bootstrap'
import 'bootstrap'

import * as Vue from 'vue'
import * as VueRouter from 'vue-router'
import App from './js/components/App'
import bsCustomFileInput from 'bs-custom-file-input'
import axios from 'axios'
import router from './js/router'
import store, { persistentStore } from './js/store'

import './styles/app.scss'


bsCustomFileInput.init()

axios.defaults.withCredentials = true
axios.defaults.headers.post = { 'Content-Type': 'application/json' }
axios.defaults.headers.put = { 'Content-Type': 'application/json' }
// console.log(axios.defaults)

const app = Vue.createApp(App)
app.use(router)
app.use(store)
app.use(persistentStore)
app.mount('#app')

let sessionExpiry = document.getElementById('sessionExpiry').value
if (sessionExpiry != '') {
    persistentStore.commit('setSessionExpiry', sessionExpiry)
}

